<?php

namespace App\Http\Controllers;

use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use App\Models\District;
use App\Models\DeliveryConfiguration;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

use App\Models\Local;
class CheckoutController extends Controller
{
    protected OrderPaymentService $orderService;

    public function __construct(OrderPaymentService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        // ✅ SOLUCIÓN:
        $districts = District::active()
            ->select([
                'id',
                'department',
                'province',
                'district',
                'delivery_cost',
                'ubigeo'
            ])
            ->get()
            ->groupBy('department')
            ->map(function ($items, $department) {
                return $items->groupBy('province');
            });

        $deliveryConfig = DeliveryConfiguration::first();

        return Inertia::render('Checkout/Index', [
            'cart' => $cart,
            'districts' => $districts,
            'defaultDeliveryCost' => $deliveryConfig?->default_delivery_cost ?? 10.00,
            'freeShippingThreshold' => $deliveryConfig?->free_shipping_threshold,
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_last_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'dni' => 'required|string|max:20',
            'delivery_district_id' => 'required|exists:districts,id',
            'shipping_address' => 'required|string|max:500',
            'delivery_reference' => 'nullable|string|max:255',
            'accepted_terms' => 'required|accepted',
            'accepted_privacy' => 'required|accepted',

            // Estos campos son obligatorios ahora
            'subtotal' => 'required|numeric|min:0',
            'final_total' => 'required|numeric|min:0',
            'items' => 'required|array',
        ]);

        $orderData = [
            'user_id' => auth()->id(),
            'subtotal' => $validated['subtotal'],
            'discount_amount' => $request->input('discount_amount', 0),
            'final_total' => $validated['final_total'],

            'guest_name' => $validated['guest_name'],
            'guest_last_name' => $validated['guest_last_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'],

            'delivery_full_name' => trim($validated['guest_name'] . ' ' . $validated['guest_last_name']),
            'delivery_district_id' => $validated['delivery_district_id'],
            'shipping_address' => $validated['shipping_address'],
            'delivery_reference' => $validated['delivery_reference'] ?? null,
            'delivery_cost' => $request->input('delivery_cost', 0), // ← agregar
            'dni' => $validated['dni'],

            'accepted_terms' => $validated['accepted_terms'],
            'accepted_privacy' => $validated['accepted_privacy'],
            'accepted_marketing' => $request->boolean('accepted_marketing', false),

            'notes' => $request->input('notes'),
            // ✅ ESTA LÍNEA es la que falta
            'items' => $validated['items'],
        ];

        // Crear la Orden (sin pago todavía)
        $order = $this->orderService->createOrderWithPayment($orderData, [
            'provider' => 'mercadopago',   // solo como referencia
        ]);
        // Limpiar el carrito de la sesión
        session()->forget('cart');
        // ✅ BIEN - redirect a una URL real
        return redirect()->route('checkout.payment', $order->order_number);
    }

    /**
     * Página de pago con Mercado Pago
     */
    public function payment(string $order_number)
    {
        $order = Order::with(['items', 'district', 'pickupLocal', 'latestPayment'])
            ->where('order_number', $order_number)
            ->firstOrFail();

        if ($order->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu pedido no tiene productos.');
        }

        if ($order->latestPayment?->isApproved()) {
            return redirect()->route('checkout.success', $order->order_number);
        }

        // Aplica descuento automático al cargar (si corresponde y no hay cupón)
        $this->orderService->applyAutoDiscountIfEligible($order);
        $order->refresh();

        // Cargar datos para los selectores
        $districts = District::active()
            ->select(['id', 'department', 'province', 'district', 'delivery_cost', 'ubigeo'])
            ->get()
            ->groupBy('department')
            ->map(fn($items) => $items->groupBy('province'));

        $locals = Local::active()->get()->map(fn($l) => [
            'id' => $l->id,
            'title' => $l->title,
            'address' => $l->address,
            'short_description' => $l->short_description,
            'label' => $l->label,
        ]);

        return Inertia::render('Checkout/Payment', [
            'order' => $order->only([
                'id',
                'order_number',
                'guest_name',
                'guest_last_name',
                'guest_email',
                'guest_phone',
                'dni',
                'delivery_method',
                'delivery_district_id',
                'shipping_address',
                'delivery_reference',
                'delivery_cost',
                'pickup_local_id',
                'pickup_local_name',
                'pickup_local_address',
                'document_type',
                'billing_ruc',
                'billing_business_name',
                'billing_address',
                'subtotal',
                'discount_amount',
                'final_total',
                'coupon_code',
                'coupon_name',
                'discount_rule_name',
                'discount_rule_percent',
                'accepted_marketing',
                'status',
            ]),
            'items' => $order->items->map(fn($i) => [
                'id' => $i->id,
                'product_name' => $i->product_name,
                'product_image' => $i->product_image,
                'quantity' => $i->quantity,
                'unit_price' => $i->unit_price,
                'subtotal' => $i->subtotal,
                'ml' => $i->ml,
            ]),
            'districts' => $districts,
            'locals' => $locals,
            'mpPublicKey' => config('services.mercadopago.public_key'),
        ]);
    }
    /**
     * Construye los items para Mercado Pago desde la Orden
     */
    private function buildMpItemsFromOrder(Order $order): array
    {
        return $order->items->map(function ($item) {
            return [
                'title' => $item->product_name,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'currency_id' => 'PEN',
            ];
        })->toArray();
    }

    /**
     * Pago aprobado exitosamente
     */
    public function success(string $order_number)
    {
        $order = Order::with(['items', 'district', 'latestPayment'])
            ->where('order_number', $order_number)
            ->firstOrFail();

        // Si el pago ya fue aprobado (vía webhook o manual)
        if ($order->latestPayment?->isApproved()) {
            $order->update(['status' => Order::STATUS_PREPARING]);
        }

        return Inertia::render('Checkout/Success', [
            'order' => $order->only([
                'order_number',
                'final_total',
                'guest_name',
                'guest_email'
            ]),
            'items' => $order->items->map(fn($item) => [
                'name' => $item->product_name,
                'quantity' => $item->quantity,
                'price' => $item->unit_price,
            ])
        ]);
    }

    /**
     * Pago rechazado o fallido
     */
    public function failure(string $order_number)
    {
        $order = Order::with('latestPayment')
            ->where('order_number', $order_number)
            ->firstOrFail();

        // Opcional: marcar como abandonado o mantener en pending
        if (!$order->latestPayment?->isApproved()) {
            $order->update(['status' => Order::STATUS_PENDING]);
        }

        return Inertia::render('Checkout/Failure', [
            'order_number' => $order_number,
            'order' => $order->only(['final_total', 'status']),
            'error' => 'Tu pago fue rechazado. Puedes intentarlo nuevamente.'
        ]);
    }

    /**
     * Pago en proceso (pendiente de aprobación)
     */
    public function pending(string $order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();

        return Inertia::render('Checkout/Pending', [
            'order_number' => $order_number,
            'order' => $order->only(['final_total']),
            'message' => 'Tu pago está siendo procesado. Te enviaremos un correo cuando sea confirmado.'
        ]);
    }

    /**
     * Aplica un cupón a la orden.
     */
    public function applyCoupon(Request $request, string $order_number)
    {
        $request->validate(['code' => 'required|string|max:50']);

        $order = Order::where('order_number', $order_number)->firstOrFail();
        $result = $this->orderService->applyCoupon($order, $request->input('code'));

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Quita el cupón aplicado.
     */
    public function removeCoupon(string $order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();
        $result = $this->orderService->removeCoupon($order);

        return response()->json($result);
    }

    /**
     * Actualiza la información del cliente desde Payment.vue
     * (delivery method, dirección, factura, etc.)
     */
    public function updateOrderInfo(Request $request, string $order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();

        $rules = [
            'guest_name' => 'required|string|max:255',
            'guest_last_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'dni' => 'required|string|max:20',
            'delivery_method' => 'required|in:delivery,pickup',
            'document_type' => 'required|in:boleta,factura',
            'accepted_marketing' => 'sometimes|boolean',
        ];

        if ($request->input('delivery_method') === 'delivery') {
            $rules['delivery_district_id'] = 'required|exists:districts,id';
            $rules['shipping_address'] = 'required|string|max:500';
            $rules['delivery_reference'] = 'nullable|string|max:255';
        } else {
            $rules['pickup_local_id'] = 'required|exists:locals,id';
        }

        if ($request->input('document_type') === 'factura') {
            $rules['billing_ruc'] = 'required|string|size:11';
            $rules['billing_business_name'] = 'required|string|max:255';
            $rules['billing_address'] = 'required|string|max:500';
        }

        $validated = $request->validate($rules);

        // Calcular delivery_cost
        $deliveryCost = 0;
        if ($validated['delivery_method'] === 'delivery') {
            $district = District::find($validated['delivery_district_id']);
            $deliveryCost = (float) ($district->delivery_cost ?? 0);
        }

        // Snapshot del local si pickup
        $pickupName = null;
        $pickupAddress = null;
        if ($validated['delivery_method'] === 'pickup') {
            $local = Local::find($validated['pickup_local_id']);
            $pickupName = $local->title;
            $pickupAddress = $local->address;
        }

        $order->update([
            'guest_name' => $validated['guest_name'],
            'guest_last_name' => $validated['guest_last_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'],
            'dni' => $validated['dni'],
            'delivery_method' => $validated['delivery_method'],
            'delivery_full_name' => trim($validated['guest_name'] . ' ' . $validated['guest_last_name']),

            // Delivery
            'delivery_district_id' => $validated['delivery_method'] === 'delivery' ? $validated['delivery_district_id'] : null,
            'shipping_address' => $validated['delivery_method'] === 'delivery' ? $validated['shipping_address'] : null,
            'delivery_reference' => $validated['delivery_method'] === 'delivery' ? ($validated['delivery_reference'] ?? null) : null,
            'delivery_cost' => $deliveryCost,

            // Pickup
            'pickup_local_id' => $validated['delivery_method'] === 'pickup' ? $validated['pickup_local_id'] : null,
            'pickup_local_name' => $pickupName,
            'pickup_local_address' => $pickupAddress,

            // Documento
            'document_type' => $validated['document_type'],
            'billing_ruc' => $validated['document_type'] === 'factura' ? $validated['billing_ruc'] : null,
            'billing_business_name' => $validated['document_type'] === 'factura' ? $validated['billing_business_name'] : null,
            'billing_address' => $validated['document_type'] === 'factura' ? $validated['billing_address'] : null,

            'accepted_marketing' => $request->boolean('accepted_marketing', $order->accepted_marketing),
        ]);

        // Recalcular totales (cambió delivery_cost)
        $this->orderService->recalculateTotals($order->fresh());

        return response()->json([
            'success' => true,
            'order' => $order->fresh(['items', 'district', 'pickupLocal']),
        ]);
    }

    /**
     * Crea la preferencia de Mercado Pago en el momento que el usuario
     * confirma el pago. Se hace AQUÍ y no en payment() para que el monto
     * refleje cualquier cupón/cambio de último momento.
     */
    public function createPreference(string $order_number)
    {
        $order = Order::with(['items', 'district', 'pickupLocal', 'latestPayment'])
            ->where('order_number', $order_number)
            ->firstOrFail();

        if ($order->items->isEmpty()) {
            return response()->json(['error' => 'Tu pedido no tiene productos.'], 422);
        }

        if ($order->latestPayment?->isApproved()) {
            return response()->json(['error' => 'Esta orden ya fue pagada.'], 422);
        }

        // Validación mínima de info del cliente
        if (empty($order->guest_email) || empty($order->guest_name)) {
            return response()->json(['error' => 'Completa tu información antes de pagar.'], 422);
        }

        if ($order->isDelivery() && empty($order->shipping_address)) {
            return response()->json(['error' => 'Completa la dirección de envío.'], 422);
        }

        if ($order->isPickup() && empty($order->pickup_local_id)) {
            return response()->json(['error' => 'Selecciona una tienda para retiro.'], 422);
        }

        if ($order->isFactura() && empty($order->billing_ruc)) {
            return response()->json(['error' => 'Completa los datos de facturación.'], 422);
        }

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        $client = new PreferenceClient();

        $preferenceData = [
            'items' => $this->buildMpItemsFromOrder($order),
            'payer' => array_filter([
                'name' => $order->guest_name ?? '',
                'surname' => $order->guest_last_name ?? '',
                'email' => $order->guest_email ?? '',
                'phone' => !empty($order->guest_phone) ? [
                    'area_code' => '51',
                    'number' => preg_replace('/\D/', '', $order->guest_phone),
                ] : null,
            ], fn($v) => !is_null($v) && $v !== ''),
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
            'statement_descriptor' => 'BRANA',
            'external_reference' => $order->order_number,
            'notification_url' => route('webhooks.mercadopago'),
        ];

        if (!app()->environment('local')) {
            $preferenceData['back_urls'] = [
                'success' => route('checkout.success', $order->order_number),
                'failure' => route('checkout.failure', $order->order_number),
                'pending' => route('checkout.pending', $order->order_number),
            ];
            $preferenceData['auto_return'] = 'approved';
        }

        try {
            $preference = $client->create($preferenceData);

            $order->update([
                'payment_response' => array_merge(
                    $order->payment_response ?? [],
                    ['preference_id' => $preference->id]
                ),
            ]);

            // ✅ Detecta si estamos usando credenciales de TEST y devuelve el init_point correcto
            $isTest = str_starts_with(
                config('services.mercadopago.access_token') ?? '',
                'TEST-'
            );

            return response()->json([
                'preference_id' => $preference->id,
                'init_point' => $isTest
                    ? $preference->sandbox_init_point
                    : $preference->init_point,
            ]);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error('MP Preference Error', [
                'order_number' => $order_number,
                'message' => $e->getMessage(),
                'mp_response' => $e->getApiResponse()?->getContent(),
            ]);
            return response()->json(['error' => 'No pudimos generar el enlace de pago.'], 500);
        } catch (\Exception $e) {
            Log::error('MP Generic Error', [
                'order_number' => $order_number,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Error al procesar el pago.'], 500);
        }
    }


}
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
        $order = Order::with(['items', 'district', 'latestPayment'])
            ->where('order_number', $order_number)
            ->firstOrFail();

        if ($order->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu pedido no tiene productos.');
        }

        if ($order->latestPayment?->isApproved()) {
            return redirect()->route('checkout.success', $order->order_number);
        }

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $client = new PreferenceClient();
        $preferenceId = null;

        if ($order->payment_response && isset($order->payment_response['preference_id'])) {
            $preferenceId = $order->payment_response['preference_id'];
        } else {

            // ✅ Detecta si estamos en local para no mandar back_urls inválidas
            // $isLocal = app()->environment('local');

            $preferenceData = [
                'items' => $this->buildMpItemsFromOrder($order),
                'payer' => [
                    'name' => $order->guest_name ?? '',
                    'surname' => $order->guest_last_name ?? '',
                    'email' => $order->guest_email ?? '',
                    // 👇 cambio 1: solo manda phone si no está vacío
                    'phone' => !empty($order->guest_phone) ? [
                        'area_code' => '51',
                        'number' => preg_replace('/\D/', '', $order->guest_phone),
                    ] : null,
                ],
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                'statement_descriptor' => 'BRANA',
                'external_reference' => $order->order_number,
            ];

            // 👇 cambio 2: limpia nulls/vacíos del payer (justo después del array)
            // $preferenceData['payer'] = array_filter($preferenceData['payer']);
            $preferenceData['payer'] = array_filter($preferenceData['payer'], fn($v) => !is_null($v) && $v !== '');
            // El resto sigue igual
            $webhookUrl = route('webhooks.mercadopago');
            $preferenceData['notification_url'] = $webhookUrl;


            // Las back_urls solo si NO es localhost
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
                    )
                ]);

                $preferenceId = $preference->id;

            } catch (\MercadoPago\Exceptions\MPApiException $e) {
                // ✅ Logging detallado: ahora SÍ vamos a ver qué dice MP
                Log::error('Mercado Pago Preference Error (Detallado)', [
                    'order_number' => $order_number,
                    'message' => $e->getMessage(),
                    'status_code' => $e->getApiResponse()?->getStatusCode(),
                    'mp_response' => $e->getApiResponse()?->getContent(),
                    'preference_data' => $preferenceData,
                ]);

                return redirect()->route('checkout.failure', $order_number)
                    ->with('error', 'No pudimos generar el enlace de pago.');
            } catch (\Exception $e) {
                Log::error('Mercado Pago Generic Error', [
                    'order_number' => $order_number,
                    'error' => $e->getMessage(),
                    'class' => get_class($e),
                ]);

                return redirect()->route('checkout.failure', $order_number)
                    ->with('error', 'Error al procesar el pago.');
            }
        }

        return Inertia::render('Checkout/Payment', [
            'order' => $order->only([
                'id',
                'order_number',
                'subtotal',
                'delivery_cost',
                'final_total',
                'status'
            ]),
            'order_number' => $order->order_number,
            'preference' => ['id' => $preferenceId],
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


}
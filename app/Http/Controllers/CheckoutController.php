<?php

namespace App\Http\Controllers;

use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use App\Models\District;
use App\Models\DeliveryConfiguration;

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
            'subtotal' => 'required|numeric|min:0',
            'final_total' => 'required|numeric|min:0',
            'delivery_district_id' => 'required|exists:districts,id',
            'shipping_address' => 'required|string|max:500',
            'delivery_full_name' => 'required|string|max:255',
            'guest_name' => 'required_if:user_id,null|string|max:255',
            'guest_last_name' => 'required|string|max:255',
            'guest_email' => 'required_if:user_id,null|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'delivery_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'accepted_terms' => 'required|boolean|accepted',
            'accepted_privacy' => 'required|boolean|accepted',
        ]);

        // Preparar datos para la Orden
        $orderData = [
            'user_id' => auth()->id(),
            'subtotal' => $validated['subtotal'],
            'discount_amount' => $request->discount_amount ?? 0,
            'final_total' => $validated['final_total'],
            'guest_name' => $validated['guest_name'] ?? null,
            'guest_last_name' => $request->guest_last_name,        // ← Agregar esto
            'guest_email' => $validated['guest_email'] ?? null,
            'guest_phone' => $validated['guest_phone'] ?? null,
            'delivery_district_id' => $validated['delivery_district_id'],
            'delivery_cost' => $request->delivery_cost ?? 0,
            'shipping_address' => $validated['shipping_address'],
            'delivery_reference' => $validated['delivery_reference'] ?? null,
            'delivery_full_name' => trim(($validated['guest_name'] ?? '') . ' ' . ($request->guest_last_name ?? '')),
            'coupon_id' => $request->coupon_id,
            'coupon_code' => $request->coupon_code,
            'notes' => $validated['notes'],
            'accepted_terms' => $validated['accepted_terms'],
            'accepted_privacy' => $validated['accepted_privacy'],
            'accepted_marketing' => $request->boolean('accepted_marketing', false),
        ];

        // Crear Orden + Payment
        $order = $this->orderService->createOrderWithPayment($orderData, [
            'provider' => 'mercadopago',
        ]);

        // Configurar Mercado Pago
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $client = new PreferenceClient();

        $preference = $client->create([
            'items' => $this->buildMpItems($request),
            'payer' => [
                'name' => $order->getCustomerNameAttribute(),
                'email' => $order->getCustomerEmailAttribute(),
            ],
            'back_urls' => [
                'success' => route('checkout.success', $order->order_number),
                'failure' => route('checkout.failure'),
                'pending' => route('checkout.pending'),
            ],
            'auto_return' => 'approved',
            'external_reference' => $order->order_number,
            'notification_url' => config('app.url') . '/webhooks/mercadopago',
            'statement_descriptor' => 'BRANA',
        ]);

        // Respuesta a Vue/Inertia
        return Inertia::render('Checkout/Payment', [
            'preference' => $preference,
            'order_number' => $order->order_number,
            'order' => $order->only(['id', 'order_number', 'final_total', 'status']),
        ]);
    }

    /**
     * Construye los items para Mercado Pago
     */
    private function buildMpItems(Request $request): array
    {
        $items = [];

        foreach ($request->items as $item) {
            $items[] = [
                'title' => $item['product_name'] ?? 'Producto',
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
                'currency_id' => 'PEN',
            ];
        }

        return $items;
    }


}
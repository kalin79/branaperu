<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;
class OrderPaymentService
{
    /**
     * Crea una orden + ítems + pago inicial (pending)
     */
    public function createOrderWithPayment(array $orderData, array $paymentData = []): Order
    {
        return DB::transaction(function () use ($orderData, $paymentData) {

            // Crear la Orden
            $order = Order::create([
                'user_id' => $orderData['user_id'] ?? null,
                'order_number' => $this->generateOrderNumber(),
                'subtotal' => $orderData['subtotal'],
                'discount_amount' => $orderData['discount_amount'] ?? 0,
                'final_total' => $orderData['final_total'],
                'delivery_cost' => $orderData['delivery_cost'] ?? 0,

                // Datos del cliente (invitado o registrado)
                'guest_name' => $orderData['guest_name'],
                'guest_last_name' => $orderData['guest_last_name'] ?? null,
                'guest_email' => $orderData['guest_email'],
                'guest_phone' => $orderData['guest_phone'],
                'dni' => $orderData['dni'] ?? null,

                // Delivery
                'delivery_full_name' => $orderData['delivery_full_name'],
                'delivery_district_id' => $orderData['delivery_district_id'],
                'shipping_address' => $orderData['shipping_address'],
                'delivery_reference' => $orderData['delivery_reference'] ?? null,

                // Consentimientos
                'accepted_terms' => $orderData['accepted_terms'],
                'accepted_privacy' => $orderData['accepted_privacy'],
                'accepted_marketing' => $orderData['accepted_marketing'] ?? false,

                'notes' => $orderData['notes'] ?? null,
                'status' => Order::STATUS_PENDING,
            ]);

            // Crear los ítems de la orden
            if (!empty($orderData['items'])) {
                $this->createOrderItems($order, $orderData['items']);
            }

            // Crear registro inicial de Pago
            $this->createInitialPayment($order, $paymentData);

            return $order->fresh(['items', 'latestPayment']);
        });
    }

    /**
     * Genera un número único de orden (ej: BRN-20250508-ABC123)
     */
    private function generateOrderNumber(): string
    {
        do {
            $number = 'BRN-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Crea los OrderItem
     */
    private function createOrderItems(Order $order, array $items): void
    {
        $orderItems = [];

        // Pre-carga productos para evitar N+1
        $productIds = collect($items)
            ->map(fn($i) => $i['product_id'] ?? $i['id'] ?? null)
            ->filter()
            ->unique()
            ->values();

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? $item['id'] ?? null;
            $product = $productId ? $products->get($productId) : null;

            $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
            $originalPrice = (float) ($item['original_price'] ?? $item['price'] ?? $unitPrice);
            $quantity = (int) ($item['quantity'] ?? 1);

            $orderItems[] = [
                'order_id' => $order->id,
                'product_id' => $productId,
                'sku' => $item['sku'] ?? $product?->sku ?? '',
                'product_name' => $item['product_name'] ?? $item['name'] ?? $product?->name ?? 'Producto',
                'product_slug' => $item['product_slug'] ?? $item['slug'] ?? $product?->slug ?? '',
                'product_image' => $item['product_image'] ?? $item['cover_image'] ?? $product?->cover_image ?? '',
                'ml' => $item['ml'] ?? $product?->ml ?? null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'original_price' => $originalPrice,
                'subtotal' => $unitPrice * $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        OrderItem::insert($orderItems);
    }

    /**
     * Crea el registro inicial del pago (estado pending)
     */
    private function createInitialPayment(Order $order, array $paymentData): void
    {
        Payment::create([
            'order_id' => $order->id,
            'provider' => $paymentData['provider'] ?? 'mercadopago',
            'external_id' => null,                    // se llena después con el webhook
            'status' => Payment::STATUS_PENDING,
            'amount' => $order->final_total,
            'currency' => 'PEN',
            'payment_method' => null,
            'payment_response' => null,
            'paid_at' => null,
        ]);
    }

    /**
     * Actualiza el estado del pago y de la orden (usado en Webhook)
     */
    public function updatePaymentStatus(string $orderNumber, array $mpResponse): bool
    {
        return DB::transaction(function () use ($orderNumber, $mpResponse) {

            $order = Order::where('order_number', $orderNumber)
                ->with('latestPayment')
                ->first();

            if (!$order)
                return false;

            $payment = $order->latestPayment;

            if (!$payment)
                return false;

            $status = $this->mapMercadoPagoStatus($mpResponse['status'] ?? '');

            $payment->update([
                'external_id' => $mpResponse['id'] ?? null,
                'status' => $status,
                'payment_method' => $mpResponse['payment_method_id'] ?? null,
                'payment_response' => $mpResponse,
                'paid_at' => in_array($status, [Payment::STATUS_APPROVED]) ? now() : null,
                'failed_at' => in_array($status, [Payment::STATUS_REJECTED]) ? now() : null,
            ]);

            // Actualizar estado de la orden
            if ($status === Payment::STATUS_APPROVED) {
                $order->update(['status' => Order::STATUS_PREPARING]);
            } elseif (in_array($status, [Payment::STATUS_REJECTED, Payment::STATUS_CHARGEBACK])) {
                $order->update(['status' => Order::STATUS_PENDING]); // o ABANDONED
            }

            return true;
        });
    }

    /**
     * Mapea estados de Mercado Pago a nuestros estados internos
     */
    private function mapMercadoPagoStatus(string $mpStatus): string
    {
        return match (strtolower($mpStatus)) {
            'approved' => Payment::STATUS_APPROVED,
            'rejected' => Payment::STATUS_REJECTED,
            'in_process',
            'pending' => Payment::STATUS_PENDING,
            'refunded' => Payment::STATUS_REFUNDED,
            default => Payment::STATUS_PENDING,
        };
    }
}
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;

use App\Models\Coupon;
use App\Models\DiscountRule;
// use App\Models\District;
// use App\Models\Local;

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

    /**
     * Aplica un cupón a la orden.
     * REEMPLAZA cualquier descuento automático previo.
     */
    public function applyCoupon(Order $order, string $couponCode): array
    {
        $coupon = Coupon::valid()->whereRaw('UPPER(code) = ?', [strtoupper(trim($couponCode))])->first();

        if (!$coupon) {
            return ['success' => false, 'message' => 'Cupón no válido o expirado.'];
        }

        if ($order->subtotal < $coupon->min_purchase_amount) {
            return [
                'success' => false,
                'message' => 'Tu pedido debe ser mínimo S/ ' . number_format($coupon->min_purchase_amount, 2) . ' para usar este cupón.',
            ];
        }

        $discount = $coupon->calculateDiscount((float) $order->subtotal);

        if ($discount <= 0) {
            return ['success' => false, 'message' => 'Este cupón no aplica a tu pedido.'];
        }

        // Aplicar cupón → REEMPLAZA descuento automático
        $order->update([
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'coupon_name' => $coupon->name,
            'coupon_discount_value' => $coupon->discount_value,

            // Limpiar descuento automático
            'discount_rule_name' => null,
            'discount_rule_min_amount' => null,
            'discount_rule_percent' => null,

            'discount_amount' => $discount,
            'final_total' => $this->calculateFinalTotal((float) $order->subtotal, $discount, (float) $order->delivery_cost),
        ]);

        return ['success' => true, 'order' => $order->fresh(['items', 'district', 'pickupLocal'])];
    }
    /**
     * Quita el cupón y reaplica descuento automático si corresponde.
     */
    public function removeCoupon(Order $order): array
    {
        $order->update([
            'coupon_id' => null,
            'coupon_code' => null,
            'coupon_name' => null,
            'coupon_discount_value' => null,
            'discount_amount' => 0,
            'final_total' => $this->calculateFinalTotal((float) $order->subtotal, 0, (float) $order->delivery_cost),
        ]);

        // Reaplica descuento automático si el subtotal califica
        $this->applyAutoDiscountIfEligible($order->fresh());

        return ['success' => true, 'order' => $order->fresh(['items', 'district', 'pickupLocal'])];
    }
    /**
     * Aplica el descuento automático de la mejor regla activa.
     * NO se ejecuta si ya hay un cupón aplicado.
     */
    public function applyAutoDiscountIfEligible(Order $order): Order
    {
        if ($order->hasCouponApplied()) {
            return $order;
        }

        $rule = DiscountRule::active()
            ->where('min_amount', '<=', $order->subtotal)
            ->orderBy('discount_percent', 'desc')
            ->first();

        if (!$rule) {
            // Limpia si antes había descuento automático y ya no califica
            if ($order->hasAutoDiscountApplied()) {
                $order->update([
                    'discount_rule_name' => null,
                    'discount_rule_min_amount' => null,
                    'discount_rule_percent' => null,
                    'discount_amount' => 0,
                    'final_total' => $this->calculateFinalTotal((float) $order->subtotal, 0, (float) $order->delivery_cost),
                ]);
            }
            return $order->fresh();
        }

        $discount = $rule->calculateDiscount((float) $order->subtotal);

        $order->update([
            'discount_rule_name' => $rule->name,
            'discount_rule_min_amount' => $rule->min_amount,
            'discount_rule_percent' => $rule->discount_percent,
            'discount_amount' => $discount,
            'final_total' => $this->calculateFinalTotal((float) $order->subtotal, $discount, (float) $order->delivery_cost),
        ]);

        return $order->fresh();
    }
    /**
     * Recalcula totales tras cambiar delivery_cost o subtotal.
     */
    public function recalculateTotals(Order $order): Order
    {
        $discount = (float) $order->discount_amount;
        $order->update([
            'final_total' => $this->calculateFinalTotal(
                (float) $order->subtotal,
                $discount,
                (float) $order->delivery_cost
            ),
        ]);
        return $order->fresh();
    }

    /**
     * Fórmula central del total final.
     */
    private function calculateFinalTotal(float $subtotal, float $discount, ?float $deliveryCost): float
    {
        return round(max(0, $subtotal - $discount + ($deliveryCost ?? 0)), 2);
    }
}
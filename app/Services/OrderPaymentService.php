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
        $coupon = Coupon::valid()
            ->whereRaw('UPPER(code) = ?', [strtoupper(trim($couponCode))])
            ->first();

        if (!$coupon) {
            return ['success' => false, 'message' => 'Cupón no válido o expirado.'];
        }

        // ✅ Todas las validaciones (mínimo, max_uses, max_uses_per_user, etc.)
        if ($error = $this->validateCouponForOrder($coupon, $order)) {
            return ['success' => false, 'message' => $error];
        }

        $discount = $coupon->calculateDiscount((float) $order->subtotal);

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

        // ✅ Sincroniza el monto del pago pendiente con el nuevo final_total
        $this->syncPendingPaymentAmount($order);

        return ['success' => true, 'order' => $order->fresh(['items', 'district', 'pickupLocal'])];
    }

    /**
     * Revalida el cupón aplicado contra el estado actual de la orden.
     * Útil cuando cambian datos del cliente (email/DNI) o el subtotal.
     *
     * - Si la orden no tiene cupón → no hace nada.
     * - Si el cupón sigue siendo válido → recalcula el descuento por si el subtotal cambió.
     * - Si ya no aplica → lo quita y reaplica descuento automático si corresponde.
     *
     * @return array{valid: bool, reason?: string, order: Order}
     */
    public function revalidateCoupon(Order $order): array
    {
        if (!$order->hasCouponApplied()) {
            return ['valid' => true, 'order' => $order];
        }

        $coupon = Coupon::find($order->coupon_id);

        // El cupón fue eliminado de la BD
        if (!$coupon) {
            $this->removeCoupon($order);
            return [
                'valid' => false,
                'reason' => 'El cupón aplicado ya no está disponible.',
                'order' => $order->fresh(['items', 'district', 'pickupLocal']),
            ];
        }

        // Revisa: vigencia, mínimo de compra, max_uses, max_uses_per_user
        $error = $this->validateCouponForOrder($coupon, $order);

        if ($error) {
            $this->removeCoupon($order);
            return [
                'valid' => false,
                'reason' => $error,
                'order' => $order->fresh(['items', 'district', 'pickupLocal']),
            ];
        }

        // Sigue siendo válido → recalculamos descuento por si el subtotal cambió
        $discount = $coupon->calculateDiscount((float) $order->subtotal);

        $order->update([
            'discount_amount' => $discount,
            'final_total' => $this->calculateFinalTotal(
                (float) $order->subtotal,
                $discount,
                (float) $order->delivery_cost
            ),
        ]);

        // ✅ Sincroniza el monto del pago pendiente
        $this->syncPendingPaymentAmount($order);

        return ['valid' => true, 'order' => $order->fresh(['items', 'district', 'pickupLocal'])];
    }

    /**
     * Validaciones de negocio del cupón contra una orden.
     * Devuelve null si todo OK, o un string con el mensaje de error.
     */
    private function validateCouponForOrder(Coupon $coupon, Order $order): ?string
    {
        // Vigencia (importante en revalidación: el cupón pudo haberse desactivado o expirado)
        if (!$coupon->isValid()) {
            return 'El cupón ya no está activo o expiró.';
        }

        // Mínimo de compra
        $minPurchase = (float) ($coupon->min_purchase_amount ?? 0);
        if ((float) $order->subtotal < $minPurchase) {
            return 'Tu pedido debe ser mínimo S/ '
                . number_format($minPurchase, 2)
                . ' para usar este cupón.';
        }

        // Límite GLOBAL de usos
        if (!empty($coupon->max_uses)) {
            if ($this->countCouponUsage($coupon->id, $order->id) >= $coupon->max_uses) {
                return 'Este cupón ya alcanzó su límite máximo de usos.';
            }
        }

        // Límite POR USUARIO (logueado o invitado)
        if (!empty($coupon->max_uses_per_user)) {
            if ($this->countCouponUsageForCustomer($coupon->id, $order) >= $coupon->max_uses_per_user) {
                return 'Ya usaste este cupón el máximo de veces permitido.';
            }
        }

        // El cálculo concreto produce > 0
        if ($coupon->calculateDiscount((float) $order->subtotal) <= 0) {
            return 'Este cupón no aplica a tu pedido.';
        }

        return null;
    }

    /**
     * Cuenta cuántas veces se ha usado el cupón en órdenes con pago APROBADO.
     * Excluye la orden actual (que aún no se ha pagado).
     */
    private function countCouponUsage(int $couponId, ?int $excludeOrderId = null): int
    {
        $query = Order::where('coupon_id', $couponId)
            ->whereHas('latestPayment', function ($q) {
                $q->where('status', Payment::STATUS_APPROVED);
            });

        if ($excludeOrderId) {
            $query->where('id', '!=', $excludeOrderId);
        }

        return $query->count();
    }

    /**
     * Cuenta cuántas veces este cliente (logueado o invitado) ha usado el cupón
     * en órdenes con pago APROBADO.
     *
     * - Si la orden tiene user_id → filtra por user_id
     * - Si es invitado → filtra por guest_email O dni (lo que exista)
     */
    private function countCouponUsageForCustomer(int $couponId, Order $order): int
    {
        $query = Order::where('coupon_id', $couponId)
            ->where('id', '!=', $order->id) // no contar la orden actual
            ->whereHas('latestPayment', function ($q) {
                $q->where('status', Payment::STATUS_APPROVED);
            });

        if (!empty($order->user_id)) {
            // Usuario logueado → identificar por user_id
            $query->where('user_id', $order->user_id);
        } else {
            // Invitado → identificar por email o DNI
            $email = $order->guest_email;
            $dni = $order->dni;

            // Si no tenemos ningún identificador, no podemos validar nada
            if (empty($email) && empty($dni)) {
                return 0;
            }

            $query->where(function ($q) use ($email, $dni) {
                if (!empty($email)) {
                    $q->orWhere('guest_email', $email);
                }
                if (!empty($dni)) {
                    $q->orWhere('dni', $dni);
                }
            });
        }

        return $query->count();
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
            'final_total' => $this->calculateFinalTotal(
                (float) $order->subtotal,
                0,
                (float) $order->delivery_cost
            ),
        ]);

        // ✅ Sincroniza el monto del pago pendiente
        $this->syncPendingPaymentAmount($order);

        // Reaplica descuento automático si el subtotal califica
        // (applyAutoDiscountIfEligible se encarga de re-sincronizar el pago)
        $order = $this->applyAutoDiscountIfEligible($order->fresh());

        return [
            'success' => true,
            'order' => $order->fresh(['items', 'district', 'pickupLocal']),
        ];
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
                // ✅ Sincroniza el monto del pago pendiente
                $this->syncPendingPaymentAmount($order);
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

        // ✅ Sincroniza el monto del pago pendiente
        $this->syncPendingPaymentAmount($order);

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

        // ✅ Sincroniza el monto del pago pendiente
        $this->syncPendingPaymentAmount($order);

        return $order->fresh();
    }

    /**
     * Fórmula central del total final.
     */
    private function calculateFinalTotal(float $subtotal, float $discount, ?float $deliveryCost): float
    {
        return round(max(0, $subtotal - $discount + ($deliveryCost ?? 0)), 2);
    }

    /**
     * Sincroniza el monto del Payment pendiente con el final_total actual
     * de la orden. Se llama después de aplicar/quitar cupón, descuento
     * automático, o recalcular totales, para evitar que MP cobre un monto
     * distinto al final_total real de la orden.
     *
     * Solo afecta pagos en estado PENDING (los aprobados/rechazados ya
     * tienen su monto histórico y no debe alterarse).
     */
    private function syncPendingPaymentAmount(Order $order): void
    {
        Payment::where('order_id', $order->id)
            ->where('status', Payment::STATUS_PENDING)
            ->update(['amount' => $order->final_total]);
    }
}
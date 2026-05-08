<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class OrderPaymentService
{
    public function createOrderWithPayment(array $orderData, array $paymentData): Order
    {
        return DB::transaction(function () use ($orderData, $paymentData) {

            $order = Order::create([
                'user_id' => $orderData['user_id'] ?? null,
                'order_number' => $orderData['order_number'] ?? 'ORD-' . now()->format('YmdHis'),
                'subtotal' => $orderData['subtotal'],
                'discount_amount' => $orderData['discount_amount'] ?? 0,
                'final_total' => $orderData['final_total'],
                'status' => Order::STATUS_PENDING,        // ← Siempre inicia como pending
                'guest_name' => $orderData['guest_name'] ?? null,
                'guest_email' => $orderData['guest_email'] ?? null,
                'guest_phone' => $orderData['guest_phone'] ?? null,
                'delivery_district_id' => $orderData['delivery_district_id'],
                'delivery_cost' => $orderData['delivery_cost'] ?? 0,
                'shipping_address' => $orderData['shipping_address'],
                'delivery_reference' => $orderData['delivery_reference'] ?? null,
                'delivery_full_name' => $orderData['delivery_full_name'] ?? null,
                'coupon_id' => $orderData['coupon_id'] ?? null,
                'coupon_code' => $orderData['coupon_code'] ?? null,
                'notes' => $orderData['notes'] ?? null,
                'accepted_terms' => $orderData['accepted_terms'] ?? true,
                'accepted_privacy' => $orderData['accepted_privacy'] ?? true,
                'accepted_marketing' => $orderData['accepted_marketing'] ?? false,
            ]);

            // Crear registro inicial de pago
            Payment::create([
                'order_id' => $order->id,
                'provider' => $paymentData['provider'] ?? 'mercadopago',
                'external_id' => $paymentData['external_id'] ?? null,
                'status' => Payment::STATUS_PENDING,
                'amount' => $order->final_total,
                'currency' => 'PEN',
                'payment_method' => $paymentData['payment_method'] ?? null,
                'payment_response' => $paymentData['payment_response'] ?? null,
            ]);

            return $order->fresh(['payments', 'latestPayment']);
        });
    }

    /**
     * Actualizar estado cuando llega el webhook de MercadoPago
     */
    public function handlePaymentWebhook(string $externalId, array $mpResponse): void
    {
        $payment = Payment::where('external_id', $externalId)->first();

        if (!$payment)
            return;

        DB::transaction(function () use ($payment, $mpResponse) {

            $status = $this->mapMercadoPagoStatus($mpResponse['status'] ?? '');

            $payment->update([
                'status' => $status,
                'payment_response' => $mpResponse,
                'paid_at' => in_array($status, [Payment::STATUS_APPROVED]) ? now() : null,
                'failed_at' => in_array($status, [Payment::STATUS_REJECTED, Payment::STATUS_CHARGEBACK]) ? now() : null,
            ]);

            $order = $payment->order;

            // Actualizar estado del pedido según el resultado del pago
            if ($status === Payment::STATUS_APPROVED) {
                $order->update(['status' => Order::STATUS_PREPARING]);   // ← Cambiado a PREPARING
            } elseif ($status === Payment::STATUS_REFUNDED) {
                $order->update(['status' => Order::STATUS_REFUNDED]);
            }
            // Si es rejected, podemos dejarlo en pending o marcar como abandoned más tarde
        });
    }

    private function mapMercadoPagoStatus(string $mpStatus): string
    {
        return match (strtolower($mpStatus)) {
            'approved' => Payment::STATUS_APPROVED,
            'rejected' => Payment::STATUS_REJECTED,
            'in_process' => Payment::STATUS_IN_PROCESS,
            'refunded' => Payment::STATUS_REFUNDED,
            'charged_back' => Payment::STATUS_CHARGEBACK,
            default => Payment::STATUS_PENDING,
        };
    }
}
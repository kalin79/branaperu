<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class MercadoPagoWebhookController extends Controller
{
    protected OrderPaymentService $orderService;

    public function __construct(OrderPaymentService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function handle(Request $request)
    {
        $data = $request->all();

        Log::info('🌐 Mercado Pago Webhook Recibido', [
            'type' => $data['type'] ?? 'unknown',
            'action' => $data['action'] ?? null,
            'ip' => $request->ip()
        ]);

        // Solo procesamos notificaciones de tipo "payment"
        if (($data['type'] ?? '') !== 'payment') {
            return response()->json(['status' => 'ignored'], 200);
        }

        $paymentId = $data['data']['id'] ?? null;

        if (!$paymentId) {
            Log::warning('Webhook sin payment ID');
            return response()->json(['status' => 'error', 'message' => 'Missing payment ID'], 400);
        }

        try {
            $paymentInfo = $this->getPaymentInfo($paymentId);

            if (!$paymentInfo) {
                Log::error('No se pudo obtener info del pago', ['payment_id' => $paymentId]);
                return response()->json(['status' => 'error'], 400);
            }

            // Obtener order_number desde diferentes ubicaciones posibles
            $orderNumber = $paymentInfo['external_reference']
                ?? $paymentInfo['metadata']['order_number']
                ?? null;

            if (!$orderNumber) {
                Log::warning('No se encontró order_number en el pago', ['payment_info' => $paymentInfo]);
                return response()->json(['status' => 'ignored'], 200);
            }

            $updated = $this->orderService->updatePaymentStatus($orderNumber, $paymentInfo);

            if ($updated) {
                Log::info('✅ Pago procesado correctamente vía Webhook', [
                    'order_number' => $orderNumber,
                    'status' => $paymentInfo['status'] ?? 'unknown'
                ]);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('❌ Error en Webhook Mercado Pago', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Obtiene la información completa del pago
     */
    private function getPaymentInfo(string $paymentId): ?array
    {
        try {
            MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));

            $client = new PaymentClient();
            $payment = $client->get($paymentId);

            // Mejor forma de convertir a array (más confiable que toArray())
            return json_decode(json_encode($payment), true);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error('MPApiException en getPaymentInfo', [
                'payment_id' => $paymentId,
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error general en getPaymentInfo', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
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
            'ip' => $request->ip(),
        ]);

        // 1. Validar firma del webhook (seguridad)
        if (!$this->isValidSignature($request)) {
            Log::warning('⚠️ Webhook con firma inválida', ['ip' => $request->ip()]);
            return response()->json(['status' => 'invalid signature'], 401);
        }

        // 2. Solo procesamos notificaciones de tipo "payment"
        if (($data['type'] ?? '') !== 'payment') {
            return response()->json(['status' => 'ignored'], 200);
        }

        $paymentId = $data['data']['id'] ?? null;

        if (!$paymentId) {
            Log::warning('Webhook sin payment ID');
            return response()->json(['status' => 'missing_id'], 200);
        }

        // 3. Saltar IDs de prueba del simulador de MP
        $testIds = ['123456', '0'];
        if (in_array((string) $paymentId, $testIds, true)) {
            Log::info('🧪 Payment ID de prueba detectado, ignorando', [
                'payment_id' => $paymentId,
            ]);
            return response()->json(['status' => 'test_payment_ignored'], 200);
        }

        try {
            $paymentInfo = $this->getPaymentInfo($paymentId);

            if (!$paymentInfo) {
                Log::warning('No se pudo obtener info del pago', ['payment_id' => $paymentId]);
                return response()->json(['status' => 'payment_not_found'], 200);
            }

            $orderNumber = $paymentInfo['external_reference']
                ?? $paymentInfo['metadata']['order_number']
                ?? null;

            if (!$orderNumber) {
                Log::warning('No se encontró order_number en el pago', ['payment_info' => $paymentInfo]);
                return response()->json(['status' => 'no_order_number'], 200);
            }

            $updated = $this->orderService->updatePaymentStatus($orderNumber, $paymentInfo);

            if ($updated) {
                Log::info('✅ Pago procesado correctamente vía Webhook', [
                    'order_number' => $orderNumber,
                    'status' => $paymentInfo['status'] ?? 'unknown',
                    'payment_id' => $paymentId,
                ]);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('❌ Error en Webhook Mercado Pago', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // 500 → MP reintentará. 200 → MP no reintenta.
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Valida la firma del webhook según la documentación de Mercado Pago.
     * Headers esperados:
     *   x-signature: "ts=1704905527,v1=618c85345248dd820d5..."
     *   x-request-id: "f2a37bbf-..."
     */
    private function isValidSignature(Request $request): bool
    {
        $secret = config('services.mercadopago.webhook_secret');

        // Si no hay secret configurado, no rechazamos (útil en local/dev)
        if (empty($secret)) {
            Log::warning('⚠️ MP_WEBHOOK_SECRET no configurado — saltando validación');
            return true;
        }

        // 🧪 Bypass en local para usar el simulador de MP
        if (app()->environment('local')) {
            Log::info('🧪 Local environment — saltando validación de firma');
            return true;
        }

        $xSignature = $request->header('x-signature');
        $xRequestId = $request->header('x-request-id');

        if (!$xSignature || !$xRequestId) {
            return false;
        }

        // Parse "ts=...,v1=..." → ['ts' => '...', 'v1' => '...']
        $parts = [];
        foreach (explode(',', $xSignature) as $part) {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, null);
            if ($key && $value) {
                $parts[trim($key)] = trim($value);
            }
        }

        $ts = $parts['ts'] ?? null;
        $v1 = $parts['v1'] ?? null;

        if (!$ts || !$v1) {
            return false;
        }

        // El "data.id" desde la URL (?data.id=...) o del body
        $dataId = $request->query('data.id') ?? $request->input('data.id') ?? '';

        // Manifest exacto que MP firma:
        // id:[dataId];request-id:[xRequestId];ts:[ts];
        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";

        $expected = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($expected, $v1);
    }

    /**
     * Obtiene la información completa del pago desde la API de MP
     */
    private function getPaymentInfo(string $paymentId): ?array
    {
        try {
            // Usar config() en vez de env() para que funcione en producción cacheada
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

            $client = new PaymentClient();
            $payment = $client->get($paymentId);

            return json_decode(json_encode($payment), true);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error('MPApiException en getPaymentInfo', [
                'payment_id' => $paymentId,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error general en getPaymentInfo', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
<?php

namespace App\Http\Controllers\Webhook;

use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;   // ← Agregar esta línea

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request, OrderPaymentService $service)
    {
        $data = $request->all();

        if (isset($data['data']['id'])) {
            $service->handlePaymentWebhook($data['data']['id'], $data);
        }

        return response()->json(['status' => 'ok']);
    }
}
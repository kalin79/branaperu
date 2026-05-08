<?php

use App\Http\Controllers\Webhook\MercadoPagoWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/mercadopago', [MercadoPagoWebhookController::class, 'handle'])
    ->name('webhooks.mercadopago');
<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Console\Command;

class MigrateOldPaymentsToNewTable extends Command
{
    protected $signature = 'orders:migrate-payments';
    protected $description = 'Migra pagos antiguos a la nueva tabla payments';

    public function handle()
    {
        $this->info('Iniciando migración de pagos...');

        Order::whereNotNull('payment_id')->chunk(100, function ($orders) {
            foreach ($orders as $order) {
                $response = $order->payment_response ?? [];

                Payment::create([
                    'order_id' => $order->id,
                    'provider' => 'mercadopago',
                    'external_id' => $order->payment_id,
                    'status' => $this->mapOldStatus($order->status),
                    'amount' => $order->final_total,
                    'payment_response' => $response,
                    'paid_at' => $order->status === 'paid' ? $order->updated_at : null,
                    'failed_at' => in_array($order->status, ['rejected']) ? $order->updated_at : null,
                ]);
            }
        });

        $this->info('¡Migración completada!');
    }

    private function mapOldStatus(string $oldStatus): string
    {
        return match ($oldStatus) {
            'paid' => Payment::STATUS_APPROVED,
            'rejected' => Payment::STATUS_REJECTED,
            default => Payment::STATUS_PENDING,
        };
    }
}
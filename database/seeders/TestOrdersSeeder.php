<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestOrdersSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🧹 Borrando datos anteriores...');

        // Desactivar restricciones de foreign keys
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Payment::truncate();
        Order::truncate();        // Ahora sí funciona

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('📦 Generando 35 órdenes realistas...');

        $user = User::firstOrCreate(
            ['email' => 'test@brana.pe'],
            ['name' => 'Carlos Cliente', 'password' => bcrypt('password')]
        );

        $orderStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_PREPARING,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_REFUNDED,
        ];

        for ($i = 1; $i <= 35; $i++) {
            $orderStatus = $orderStatuses[array_rand($orderStatuses)];

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . now()->format('Ymd') . Str::padLeft($i, 4, '0'),
                'subtotal' => rand(9200, 32000),
                'discount_amount' => rand(0, 4800),
                'final_total' => rand(8500, 29500),
                'status' => $orderStatus,
                'delivery_district_id' => rand(1, 50),
                'delivery_cost' => rand(0, 1800),
                'shipping_address' => 'Av. Siempre Viva 123, Surco',
                'delivery_full_name' => 'Carlos Espinoza',
                'accepted_terms' => true,
                'accepted_privacy' => true,
                'notes' => rand(0, 1) ? 'Dejar en portería' : null,
            ]);

            // Crear pagos
            $numPayments = rand(1, 3);
            for ($p = 0; $p < $numPayments; $p++) {
                $paymentStatus = match (true) {
                    in_array($orderStatus, [Order::STATUS_PREPARING, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED]) => 'approved',
                    $orderStatus === Order::STATUS_REFUNDED => 'refunded',
                    default => collect(['approved', 'pending', 'rejected', 'in_process'])->random()
                };

                Payment::create([
                    'order_id' => $order->id,
                    'provider' => 'mercadopago',
                    'external_id' => 'PAY-' . rand(10000000, 99999999),
                    'status' => $paymentStatus,
                    'amount' => $order->final_total,
                    'currency' => 'PEN',
                    'payment_method' => collect(['credit_card', 'yape', 'debit_card', 'transfer'])->random(),
                    'paid_at' => in_array($paymentStatus, ['approved']) ? now()->subHours(rand(1, 120)) : null,
                ]);
            }
        }

        $this->command->info('✅ ¡35 órdenes realistas creadas correctamente!');
    }
}
<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Carbon\Carbon;

class MarkAbandonedOrders extends Command
{
    protected $signature = 'orders:mark-abandoned 
                            {--minutes=30 : Minutos para considerar abandonado}';

    protected $description = 'Marca como abandoned las órdenes pending antiguas';

    public function handle()
    {
        $minutes = (int) $this->option('minutes');
        $cutoff = Carbon::now()->subMinutes($minutes);

        $count = Order::where('status', Order::STATUS_PENDING)
            ->where('created_at', '<', $cutoff)
            ->update([
                'status' => Order::STATUS_ABANDONED,
                'updated_at' => now()
            ]);

        $this->info("✅ {$count} órdenes marcadas como 'Carrito Abandonado'.");
    }
}
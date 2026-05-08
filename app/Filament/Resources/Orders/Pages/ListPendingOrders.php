<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Resources\Pages\ListRecords;

class ListPendingOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::with(['user', 'district', 'currentPayment'])   // ← Cambiar a currentPayment
            ->latest();
    }

    protected static ?string $title = 'Ventas Pendientes';
}
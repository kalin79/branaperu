<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Resources\Pages\ListRecords;

class ListSuccessfulOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::successful()->latest();
    }

    protected static ?string $title = 'Ventas Exitosas (Pagadas)';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
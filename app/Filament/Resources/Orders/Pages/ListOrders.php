<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Exports\OrdersExport;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::with(['user', 'district', 'currentPayment', 'coupon'])
            ->latest();
    }

    /**
     * Botón "Exportar a Excel" en el header de la página.
     * Respeta los filtros activos de la tabla (estado, tipo cliente, búsqueda).
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // getFilteredTableQuery() respeta filtros y búsqueda actuales
                    $query = $this->getFilteredTableQuery();

                    $filename = 'ordenes_' . now()->format('Y-m-d_His') . '.xlsx';

                    return Excel::download(
                        new OrdersExport($query),
                        $filename
                    );
                }),
        ];
    }
}
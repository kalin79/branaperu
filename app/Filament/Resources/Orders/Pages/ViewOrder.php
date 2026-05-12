<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * Eager-load relaciones necesarias para evitar N+1 en la vista.
     */
    protected function resolveRecord(int|string $key): \Illuminate\Database\Eloquent\Model
    {
        return static::getResource()::getEloquentQuery()
            ->with([
                'user',
                'items',
                'payments',
                'district',
                'pickupLocal',
                'coupon',
            ])
            ->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Imprimir Comprobante')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn() => route('admin.orders.print', $this->record), shouldOpenInNewTab: true),
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.orders.pages.view-order';
    }
}

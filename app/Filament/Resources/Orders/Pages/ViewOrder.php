<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Imprimir Comprobante')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action('printReceipt'),
        ];
    }

    public function printReceipt()
    {
        $this->js('window.print();');
    }

    public function getView(): string
    {
        return 'filament.resources.orders.pages.view-order';
    }
}
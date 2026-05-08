<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action('printReceipt'),
        ];
    }

    public function printReceipt()
    {
        $this->js('window.print();');
    }

    // Usamos vista Blade personalizada (más estable en tu versión)
    public function getView(): string
    {
        return 'filament.resources.payments.pages.view-payment';
    }
}
<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
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
                ->url(fn() => route('admin.payments.print', $this->record), shouldOpenInNewTab: true),

            // Solo mostrar Editar si el pago está aprobado (única transición manual válida)
            Action::make('edit')
                ->label('Editar Estado')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->url(fn() => PaymentResource::getUrl('edit', ['record' => $this->record]))
                ->visible(fn() => $this->record->status === Payment::STATUS_APPROVED),
        ];
    }

    // Usamos vista Blade personalizada
    public function getView(): string
    {
        return 'filament.resources.payments.pages.view-payment';
    }
}

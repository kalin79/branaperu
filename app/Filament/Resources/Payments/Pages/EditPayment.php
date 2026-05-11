<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->label('Ver Detalle')
                ->icon('heroicon-o-eye')
                ->url(fn() => PaymentResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    /**
     * Permitimos editar solo el campo `status` del pago.
     * El formulario lo enmascara: el resto son Placeholders no editables.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Defensivo: aseguramos que ningún otro campo se filtre por el form
        return [
            'status' => $data['status'] ?? $this->record->status,
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return '✅ Estado del pago actualizado correctamente';
    }
}

<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

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

    protected function afterSave(): void
    {
        $newStatus = $this->data['order_status'] ?? null;

        if ($newStatus && $this->record->order) {
            $this->record->order->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return '✅ Estado del Pedido actualizado correctamente';
    }
}
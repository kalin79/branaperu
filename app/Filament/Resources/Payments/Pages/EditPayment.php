<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    /**
     * Estado de la orden enviado en el form (campo virtual `order_status`).
     * Se captura en mutateFormDataBeforeSave y se aplica en afterSave.
     */
    public ?string $pendingOrderStatus = null;

    /**
     * Bloquea el acceso a /edit si el pago no está en estado APROBADO.
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->status !== Payment::STATUS_APPROVED) {
            Notification::make()
                ->title('Pago no editable')
                ->body('Solo se pueden editar pagos en estado "Aprobado". Estado actual: '
                    . (Payment::getStatusOptions()[$this->record->status] ?? $this->record->status))
                ->danger()
                ->send();

            $this->redirect(PaymentResource::getUrl('view', ['record' => $this->record]));
        }
    }

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
     * Inyecta el estado actual de la Orden en el campo virtual `order_status`
     * para que el Select del form se cargue con el valor correcto.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['order_status'] = $this->record->order?->status;

        return $data;
    }

    /**
     * Filtra los datos antes de guardar:
     *   - Captura `order_status` en una propiedad (no es columna de payments).
     *   - Valida que `status` del pago sea una transición manual válida.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Capturamos el estado de la Orden para aplicarlo en afterSave
        $this->pendingOrderStatus = $data['order_status'] ?? null;

        // Defensa: el status del pago solo puede ser uno de los permitidos
        $allowed = array_keys(PaymentResource::manualStatusOptions());
        $newPaymentStatus = $data['status'] ?? $this->record->status;

        if (!in_array($newPaymentStatus, $allowed, true)) {
            $newPaymentStatus = $this->record->status;
        }

        // Solo dejamos pasar `status` (lo demás se ignora a propósito)
        return ['status' => $newPaymentStatus];
    }

    /**
     * Después de guardar el Payment, aplica el cambio al estado de la Orden.
     *
     * Reglas:
     *   - Si el admin cambió `order_status` explícitamente, usa ese valor.
     *   - Si NO lo cambió pero sí cambió el estado del Pago a refunded/chargeback,
     *     sincroniza la Orden automáticamente (refunded → STATUS_REFUNDED,
     *     chargeback → STATUS_RETURNED).
     */
    protected function afterSave(): void
    {
        $order = $this->record->order;

        if (!$order) {
            return;
        }

        // Estado que correspondería automáticamente según el pago
        $autoSync = match ($this->record->status) {
            Payment::STATUS_REFUNDED => Order::STATUS_REFUNDED,
            Payment::STATUS_CHARGEBACK => Order::STATUS_RETURNED,
            default => null,
        };

        // ¿El admin cambió manualmente el order_status?
        $adminChanged = $this->pendingOrderStatus !== null
            && $this->pendingOrderStatus !== $order->status
            && array_key_exists($this->pendingOrderStatus, Order::getStatusOptions());

        // Decide qué aplicar: lo manual gana sobre lo automático
        $newOrderStatus = $adminChanged
            ? $this->pendingOrderStatus
            : $autoSync;

        if ($newOrderStatus && $order->status !== $newOrderStatus) {
            $order->update(['status' => $newOrderStatus]);

            Notification::make()
                ->title('Estado de la orden actualizado')
                ->body("Orden {$order->order_number}: "
                    . (Order::getStatusOptions()[$newOrderStatus] ?? $newOrderStatus))
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Cambios guardados correctamente';
    }
}
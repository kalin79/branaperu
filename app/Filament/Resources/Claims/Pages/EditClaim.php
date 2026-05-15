<?php

namespace App\Filament\Resources\Claims\Pages;

use App\Filament\Resources\Claims\ClaimResource;
use App\Mail\ClaimResponded;
use App\Models\Claim;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EditClaim extends EditRecord
{
    protected static string $resource = ClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No permitimos eliminar reclamos para mantener trazabilidad legal.
        ];
    }

    /**
     * Si el admin cambia la respuesta o el estado, registramos
     * quién respondió y cuándo. Si el nuevo estado es "atendido",
     * enviamos correo al consumidor con la respuesta.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Claim $record */
        $record = $this->record;

        $responseChanged = ($record->admin_response ?? null) !== ($data['admin_response'] ?? null);
        $statusChanged = $record->status !== ($data['status'] ?? null);

        if ($responseChanged || $statusChanged) {
            $data['responded_by'] = auth()->id();
            $data['responded_at'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var Claim $record */
        $record = $this->record->fresh();

        // Solo enviamos correo al consumidor si el reclamo quedó marcado como "atendido"
        // y tiene una respuesta no vacía.
        if (
            $record->status === Claim::STATUS_ATENDIDO
            && !empty(trim((string) $record->admin_response))
        ) {
            try {
                Mail::to($record->consumer_email)->send(new ClaimResponded($record));

                Notification::make()
                    ->title('Respuesta enviada al consumidor')
                    ->body($record->consumer_email)
                    ->success()
                    ->send();
            } catch (\Throwable $e) {
                Log::error('Error enviando respuesta de reclamo ' . $record->claim_number . ': ' . $e->getMessage());

                Notification::make()
                    ->title('Reclamo guardado, pero el correo falló')
                    ->body('Revisa storage/logs/laravel.log')
                    ->warning()
                    ->send();
            }
        }
    }

    public function cancel()
    {
        return $this->redirect(static::getResource()::getUrl('index'), navigate: true);
    }
}
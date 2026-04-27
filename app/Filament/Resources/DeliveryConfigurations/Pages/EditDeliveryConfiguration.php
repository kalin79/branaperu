<?php

namespace App\Filament\Resources\DeliveryConfigurations\Pages;

use App\Filament\Resources\DeliveryConfigurations\DeliveryConfigurationResource;
use App\Models\DeliveryConfiguration;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryConfiguration extends EditRecord
{
    protected static string $resource = DeliveryConfigurationResource::class;

    /**
     * Siempre devolvemos el único registro de configuración
     */
    public function getRecord(): DeliveryConfiguration
    {
        return DeliveryConfiguration::getInstance();
    }

    /**
     * mount() corregido para Filament v5 + singleton
     * Hacemos $record opcional para que no falle cuando la ruta es "/"
     */
    public function mount(string|int|null $record = null): void
    {
        $this->record = $this->getRecord();
        $this->form->fill($this->record->toArray());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar cambios')
                ->action('save'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
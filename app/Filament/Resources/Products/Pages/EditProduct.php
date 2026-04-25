<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\RedirectResponse;
class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    // ✅ Correcto en Filament v5
    protected string $view = 'filament.resources.products.pages.edit-product';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    // Método para el botón Cancelar
    public function cancel()
    {
        return $this->redirect(static::getResource()::getUrl('index'), navigate: true);
    }
    // ✅ Importante para Filament v5 - pasar el record a la vista custom
    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}
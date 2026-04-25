<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\RelationManagers\SectionsRelationManager; // ← Ajusta si el nombre es diferente
use Filament\Actions;

use Filament\Resources\Pages\ManageRelatedRecords;

class ManageSections extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'sections';

    protected static ?string $title = 'Bloques de Contenido';

    public function getRelationManagers(): array
    {
        return [
            SectionsRelationManager::class,   // ← Cambia si tu RelationManager tiene otro nombre
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Volver al Producto')
                ->url(fn() => ProductResource::getUrl('edit', ['record' => $this->getRecord()]))
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }
}
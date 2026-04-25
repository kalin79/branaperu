<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\RelationManagers\MediaRelationManager;
use Filament\Actions;
use Filament\Resources\Pages\ManageRelatedRecords;

class ManageMedia extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'media';

    protected static ?string $title = 'Galería de Multimedia';

    public function getRelationManagers(): array
    {
        return [
            MediaRelationManager::class,
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
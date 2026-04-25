<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\RelationManagers\FeaturesRelationManager;
use Filament\Actions;

use Filament\Resources\Pages\ManageRelatedRecords;

class ManageFeatures extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'features';

    protected static ?string $title = 'Características';

    public function getRelationManagers(): array
    {
        return [
            FeaturesRelationManager::class,
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
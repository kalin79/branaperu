<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\RelationManagers\RelatedProductsRelationManager;
use Filament\Resources\Pages\ManageRelatedRecords;

class ManageRelatedProducts extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'relatedProducts';

    protected static ?string $title = 'Productos Relacionados';

    public function getRelationManagers(): array
    {
        return [
            RelatedProductsRelationManager::class,
        ];
    }
}
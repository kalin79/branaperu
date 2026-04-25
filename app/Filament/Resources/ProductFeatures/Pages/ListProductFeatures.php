<?php

namespace App\Filament\Resources\ProductFeatures\Pages;

use App\Filament\Resources\ProductFeatures\ProductFeatureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductFeatures extends ListRecords
{
    protected static string $resource = ProductFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

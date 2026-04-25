<?php

namespace App\Filament\Resources\ProductFeatures\Pages;

use App\Filament\Resources\ProductFeatures\ProductFeatureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductFeature extends EditRecord
{
    protected static string $resource = ProductFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

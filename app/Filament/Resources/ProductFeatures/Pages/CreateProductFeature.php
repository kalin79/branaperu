<?php

namespace App\Filament\Resources\ProductFeatures\Pages;

use App\Filament\Resources\ProductFeatures\ProductFeatureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductFeature extends CreateRecord
{
    protected static string $resource = ProductFeatureResource::class;
}

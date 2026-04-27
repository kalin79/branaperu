<?php

namespace App\Filament\Resources\ProductFeatures;

use App\Filament\Resources\ProductFeatures\Schemas\ProductFeatureForm;
use App\Filament\Resources\ProductFeatures\Tables\ProductFeaturesTable;
use App\Filament\Resources\ProductFeatures\Pages\ListProductFeatures;
use App\Filament\Resources\ProductFeatures\Pages\CreateProductFeature;
use App\Filament\Resources\ProductFeatures\Pages\EditProductFeature;
use App\Models\ProductFeature;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ProductFeatureResource extends Resource
{
    protected static ?string $model = ProductFeature::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Star;
    protected static string|UnitEnum|null $navigationGroup = 'Configuración Global';

    // ← Agrega esta línea
    protected static ?string $navigationLabel = 'Beneficios de tu compra';

    protected static ?string $label = 'Beneficio de tu compra';
    protected static ?string $pluralLabel = 'Beneficios de tus compras';
    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return ProductFeatureForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return ProductFeaturesTable::configure($table);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Administrador', 'Editor']);
    }


    public static function getPages(): array
    {
        return [
            'index' => ListProductFeatures::route('/'),
            'create' => CreateProductFeature::route('/create'),
            'edit' => EditProductFeature::route('/{record}/edit'),
        ];
    }
}
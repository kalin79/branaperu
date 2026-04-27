<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ShoppingBag;
    protected static string|UnitEnum|null $navigationGroup = 'Catálogo de Productos';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Productos';

    protected static ?string $label = 'Producto';
    protected static ?string $pluralLabel = 'Productos';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\Products\RelationManagers\MediaRelationManager::class,
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Administrador', 'Editor']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
            'manage-media' => \App\Filament\Resources\Products\Pages\ManageMedia::route('/{record}/media'),
            'manage-features' => \App\Filament\Resources\Products\Pages\ManageFeatures::route('/{record}/features'), // ← Nueva
            'manage-sections' => \App\Filament\Resources\Products\Pages\ManageSections::route('/{record}/sections'), // ← Nueva
            'manage-related-products' => \App\Filament\Resources\Products\Pages\ManageRelatedProducts::route('/{record}/related-products'),
        ];
    }
}
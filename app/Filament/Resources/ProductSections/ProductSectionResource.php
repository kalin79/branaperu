<?php

namespace App\Filament\Resources\ProductSections;

use App\Filament\Resources\ProductSections\Pages\CreateProductSection;
use App\Filament\Resources\ProductSections\Pages\EditProductSection;
use App\Filament\Resources\ProductSections\Pages\ListProductSections;
use App\Filament\Resources\ProductSections\Schemas\ProductSectionForm;
use App\Filament\Resources\ProductSections\Tables\ProductSectionsTable;
use App\Models\ProductSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductSectionResource extends Resource
{
    protected static ?string $model = ProductSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ProductSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;   // ← Esto lo oculta del menú
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductSections::route('/'),
            'create' => CreateProductSection::route('/create'),
            'edit' => EditProductSection::route('/{record}/edit'),
        ];
    }
}

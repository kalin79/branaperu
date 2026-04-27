<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Categories\Tables\CategoriesTable;   // ← Este es el correcto
use App\Models\Category;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|UnitEnum|null $navigationGroup = 'Catálogo de Productos';
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Folder;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Categorias';
    protected static ?string $label = 'Categoria';
    protected static ?string $pluralLabel = 'Categorías';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return CategoriesTable::configure($table);   // ← Aquí estaba el error
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Administrador', 'Editor']);
    }
}
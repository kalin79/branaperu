<?php

namespace App\Filament\Resources\CategoryTypes;

use App\Filament\Resources\CategoryTypes\Pages\CreateCategoryType;
use App\Filament\Resources\CategoryTypes\Pages\EditCategoryType;
use App\Filament\Resources\CategoryTypes\Pages\ListCategoryTypes;
use App\Filament\Resources\CategoryTypes\Schemas\CategoryTypeForm;
use App\Filament\Resources\CategoryTypes\Tables\CategoryTypesTable;
use App\Models\CategoryType;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;   // ← Necesario para navigationGroup

class CategoryTypeResource extends Resource
{
    protected static ?string $model = CategoryType::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Folder;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración Global';

    // ← Agrega esta línea
    protected static ?string $navigationLabel = 'Tipos de Categorías';
    protected static ?string $label = 'Tipo de Categoría';
    protected static ?string $pluralLabel = 'Tipos de Categorías';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return CategoryTypeForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return CategoryTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategoryTypes::route('/'),
            'create' => CreateCategoryType::route('/create'),
            'edit' => EditCategoryType::route('/{record}/edit'),
        ];
    }
}
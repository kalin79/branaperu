<?php

namespace App\Filament\Resources\Locals;

use App\Filament\Resources\Locals\Schemas\LocalForm;
use App\Filament\Resources\Locals\Tables\LocalsTable;
use App\Models\Local;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LocalResource extends Resource
{
    protected static ?string $model = Local::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::MapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración E-commerce';

    protected static ?string $navigationLabel = 'Locales / Puntos de Recojo';
    protected static ?string $label = 'Local';
    protected static ?string $pluralLabel = 'Locales';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return LocalForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return LocalsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Locals\Pages\ListLocals::route('/'),
            'create' => \App\Filament\Resources\Locals\Pages\CreateLocal::route('/create'),
            'edit' => \App\Filament\Resources\Locals\Pages\EditLocal::route('/{record}/edit'),
        ];
    }
}
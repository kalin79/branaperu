<?php

namespace App\Filament\Resources\Districts;

use App\Filament\Resources\Districts\Schemas\DistrictForm;
use App\Filament\Resources\Districts\Tables\DistrictsTable;
use App\Models\District;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::MapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración E-commerce';

    protected static ?string $navigationLabel = 'Distritos - Costo Delivery';
    protected static ?string $label = 'Distrito';
    protected static ?string $pluralLabel = 'Distritos';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return DistrictForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return DistrictsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Districts\Pages\ListDistricts::route('/'),
            'create' => \App\Filament\Resources\Districts\Pages\CreateDistrict::route('/create'),
            'edit' => \App\Filament\Resources\Districts\Pages\EditDistrict::route('/{record}/edit'),
        ];
    }
}
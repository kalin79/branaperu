<?php

namespace App\Filament\Resources\DeliveryConfigurations;

use App\Filament\Resources\DeliveryConfigurations\Schemas\DeliveryConfigurationForm;
use App\Models\DeliveryConfiguration;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DeliveryConfigurationResource extends Resource
{
    protected static ?string $model = DeliveryConfiguration::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Truck;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración E-commerce';

    protected static ?string $navigationLabel = 'Configuración Delivery';
    protected static ?string $label = 'Configuración Delivery';
    protected static ?string $pluralLabel = 'Configuración Delivery';

    protected static ?int $navigationSort = 4;

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return DeliveryConfigurationForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\DeliveryConfigurations\Pages\EditDeliveryConfiguration::route('/'),
        ];
    }
}
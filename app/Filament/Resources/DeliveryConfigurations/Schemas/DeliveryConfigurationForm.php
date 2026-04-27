<?php

namespace App\Filament\Resources\DeliveryConfigurations\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DeliveryConfigurationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Configuración Global de Delivery')
                    ->description('Este valor se usará cuando un distrito tenga costo de delivery en 0')
                    ->schema([
                        TextInput::make('default_delivery_cost')
                            ->label('Costo de Delivery por Defecto')
                            ->numeric()
                            ->suffix('S/')
                            ->required()
                            ->default(10.00)
                            ->helperText('Monto que se cobrará si el distrito no tiene costo específico'),

                        TextInput::make('free_shipping_threshold')
                            ->label('Envío Gratis a partir de')
                            ->numeric()
                            ->suffix('S/')
                            ->nullable()
                            ->helperText('Dejar en blanco = nunca se aplica envío gratis'),
                    ])
                    ->columns(2),

                Toggle::make('is_active')
                    ->label('Configuración activa')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}
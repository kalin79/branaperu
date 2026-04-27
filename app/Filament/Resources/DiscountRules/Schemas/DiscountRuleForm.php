<?php

namespace App\Filament\Resources\DiscountRules\Schemas;

use Filament\Schemas\Components\Section;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DiscountRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Regla de Descuento Automático')
                    ->description('Se aplicará cuando el subtotal supere el monto mínimo')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre de la regla')
                            ->placeholder('Ej: Descuento por volumen')
                            ->maxLength(255),

                        TextInput::make('min_amount')
                            ->label('Monto mínimo de compra')
                            ->numeric()
                            ->required()
                            ->suffix('S/')
                            ->helperText('A partir de qué monto se aplica el descuento'),

                        TextInput::make('discount_percent')
                            ->label('Porcentaje de descuento')
                            ->numeric()
                            ->required()
                            ->suffix('%')
                            ->minValue(1)
                            ->maxValue(100)
                            ->helperText('Ej: 10 = 10% de descuento'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Regla activa')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}
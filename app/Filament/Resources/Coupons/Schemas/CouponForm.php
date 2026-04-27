<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Cupón')
                    ->schema([
                        TextInput::make('code')
                            ->label('Código del Cupón')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('VERANO2026'),

                        TextInput::make('name')
                            ->label('Nombre interno')
                            ->maxLength(255),

                        Select::make('discount_type')
                            ->label('Tipo de Descuento')
                            ->options([
                                'percent' => 'Porcentaje (%)',
                                'fixed' => 'Monto Fijo (S/)',
                            ])
                            ->required()
                            ->live(),

                        TextInput::make('discount_value')
                            ->label(fn($get) => $get('discount_type') === 'percent' ? 'Porcentaje' : 'Monto Fijo')
                            ->required()
                            ->numeric()
                            ->suffix(fn($get) => $get('discount_type') === 'percent' ? '%' : 'S/'),

                        TextInput::make('min_purchase_amount')
                            ->label('Monto mínimo de compra')
                            ->numeric()
                            ->default(0)
                            ->suffix('S/'),

                        // Campos agregados
                        DateTimePicker::make('starts_at')
                            ->label('Fecha de inicio')
                            ->nullable(),

                        DateTimePicker::make('expires_at')
                            ->label('Fecha de caducidad')
                            ->nullable(),

                        TextInput::make('max_uses')
                            ->label('Máximo de usos totales')
                            ->numeric()
                            ->nullable()
                            ->default(null)
                            ->helperText('Dejar en blanco = sin límite'),

                        TextInput::make('max_uses_per_user')
                            ->label('Máximo de usos por usuario')
                            ->numeric()
                            ->nullable()
                            ->default(null)
                            ->helperText('Dejar en blanco = sin límite'),

                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
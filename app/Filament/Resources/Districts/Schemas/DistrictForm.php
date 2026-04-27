<?php

namespace App\Filament\Resources\Districts\Schemas;

use App\Models\District;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DistrictForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Distrito')
                    ->schema([
                        TextInput::make('ubigeo')
                            ->label('Código Ubigeo (INEI)')
                            ->required()
                            ->maxLength(6)
                            ->unique(ignoreRecord: true)
                            ->placeholder('150101'),

                        Select::make('department')
                            ->label('Departamento')
                            ->options(fn() => District::distinct()->orderBy('department')->pluck('department', 'department')->all())
                            ->required()
                            ->searchable()
                            ->live(),

                        TextInput::make('province')
                            ->label('Provincia')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('district')
                            ->label('Distrito')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('delivery_cost')
                            ->label('Costo de Delivery')
                            ->numeric()
                            ->suffix('S/')
                            ->default(0)
                            ->helperText('0 = usar el costo global por defecto'),
                    ])
                    ->columns(2),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}
<?php

namespace App\Filament\Resources\Locals\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput\Actions\CopyAction;
use Filament\Schemas\Schema;

class LocalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Local')
                    ->schema([
                        TextInput::make('title')
                            ->label('Nombre del Local')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('address')
                            ->label('Dirección completa')
                            ->required()
                            ->rows(3),

                        TextInput::make('google_maps_link')
                            ->label('Enlace de Google Maps')
                            ->url()
                            ->suffixAction(CopyAction::make())
                            ->maxLength(500),

                        TextInput::make('label')
                            ->label('Etiqueta (ej: Gratis, 24 horas)')
                            ->maxLength(50),

                        Textarea::make('short_description')
                            ->label('Descripción corta')
                            ->rows(2)
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Local activo')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label('Orden de visualización')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
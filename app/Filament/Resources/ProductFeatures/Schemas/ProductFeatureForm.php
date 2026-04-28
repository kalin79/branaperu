<?php

namespace App\Filament\Resources\ProductFeatures\Schemas;   // ← Namespace correcto (ProductFeatures)

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductFeatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('image')
                    ->label('Imagen / Icono')
                    ->disk('public')
                    ->preserveFilenames()
                    ->image()
                    ->directory('features')
                    ->columnSpan(1),

                TextInput::make('icon')
                    ->label('Icono (Heroicon)')
                    ->helperText('Ej: heroicon-o-light-bulb'),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3),

                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ])
            ->columns(2);
    }
}
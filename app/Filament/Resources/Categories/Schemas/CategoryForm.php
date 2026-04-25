<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use App\Models\CategoryType;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Categoría')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(150)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => 
                                $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),

                        Select::make('category_type_id')
                            ->label('Tipo de Categoría')
                            ->options(CategoryType::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('parent_id')
                            ->label('Categoría Padre')
                            ->options(Category::whereNull('parent_id')->pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->preload(),

                        TextInput::make('order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Activa')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
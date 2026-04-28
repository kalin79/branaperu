<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;
class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Principal')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Producto')
                            ->required()
                            ->maxLength(200)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('subtitle')->label('Subtítulo')->maxLength(200),

                        Select::make('category_id')
                            ->label('Categoría')
                            ->options(Category::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('ml')
                            ->label('Cantidad / Volumen')
                            ->placeholder('Ej: 30ml, 5 litros, 1.5 L, 500 cc')
                            ->helperText('Escribe la cantidad con unidad (ml, L, cc, etc.)')
                            ->maxLength(50),
                        TextInput::make('sku')->label('SKU')->unique(ignoreRecord: true),

                        TextInput::make('price')->label('Precio')->numeric()->prefix('S./')->required(),
                        TextInput::make('old_price')->label('Precio Anterior')->numeric()->prefix('$'),
                        TextInput::make('stock')
                            ->label('Stock')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->minValue(0)                    // ← Mayor o igual a 0
                            ->rules(['integer', 'min:1']) // ← Mayor a 0
                            ->helperText('Cantidad disponible (debe ser mayor a 0)'),
                        // === IMAGEN PRINCIPAL (Cover) ===
                        FileUpload::make('cover_image')
                            ->label('Imagen Principal / Cover')
                            ->directory('products/covers')
                            ->acceptedFileTypes(['image/*'])
                            ->image()
                            ->maxSize(10240) // 10MB
                            ->disk('public')
                            ->preserveFilenames(),

                        // Orden (no obligatorio)
                        TextInput::make('order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->helperText('Orden de visualización en el frontend'),

                        Toggle::make('is_active')->label('Activo')->default(true),
                        Toggle::make('featured')->label('Destacado')->default(false),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Descripción')
                    ->schema([
                        Textarea::make('short_description')->label('Descripción Corta')->rows(3),
                        RichEditor::make('description')->label('Descripción Completa'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
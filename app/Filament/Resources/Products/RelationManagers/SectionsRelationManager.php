<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;     // ← Importante
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Bloques de Contenido';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del Bloque')
                    ->required()
                    ->maxLength(255),

                Select::make('section_type')
                    ->label('Tipo de Sección')
                    ->options([
                        'description' => 'Descripción',
                        // 'features' => 'Características',
                        // 'specifications' => 'Especificaciones',
                        // 'content' => 'Contenido Libre',
                        // 'video' => 'Video',
                    ])
                    ->required()
                    ->live(),

                TextInput::make('title')
                    ->label('Título (opcional)'),

                Textarea::make('description')
                    ->label('Descripción Corta')
                    ->rows(2),

                RichEditor::make('content')
                    ->label('Contenido Completo')
                    ->columnSpanFull(),

                // Tipo de Media
                Select::make('media_type')
                    ->label('Tipo de Media')
                    ->options([
                        'image' => 'Imagen',
                        'video_mp4' => 'Video MP4',
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo',
                    ])
                    ->live()
                    ->afterStateUpdated(fn(callable $set) => $set('file_media', null)),

                // Subida de archivo (Imagen / MP4)
                FileUpload::make('file_media')
                    ->label('Archivo')
                    ->directory('products/sections')
                    ->acceptedFileTypes(['image/*', 'video/mp4'])
                    ->visible(fn(callable $get) => in_array($get('media_type'), ['image', 'video_mp4']))
                    ->required(fn(callable $get) => in_array($get('media_type'), ['image', 'video_mp4'])),

                // ID para YouTube / Vimeo
                TextInput::make('video_id')
                    ->label('ID del Video')
                    ->helperText('Ej: dQw4w9wgxcq (YouTube) | 123456789 (Vimeo)')
                    ->visible(fn(callable $get) => in_array($get('media_type'), ['youtube', 'vimeo']))
                    ->required(fn(callable $get) => in_array($get('media_type'), ['youtube', 'vimeo'])),

                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Sin bloques de contenido aún')
            ->emptyStateDescription('Haz clic en "Agregar Bloque" para comenzar')
            ->emptyStateIcon('heroicon-o-document-text')
            ->columns([
                Tables\Columns\TextColumn::make('orden')->label('Orden')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('section_type')->label('Tipo'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Activo'),
            ])
            ->defaultSort('orden')
            ->reorderable('orden')
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar Bloque'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
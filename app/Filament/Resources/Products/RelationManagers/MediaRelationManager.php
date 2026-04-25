<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MediaRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $title = 'Galería de Multimedia';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('media_type')
                    ->label('Tipo')
                    ->options([
                        'image' => 'Imagen',
                        'video_mp4' => 'Video MP4',
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn(callable $set) => $set('file_url', null)),

                TextInput::make('title')->label('Título')->required(),
                TextInput::make('alt_text')->label('Texto Alternativo'),

                // Campo para imágenes y videos MP4
                FileUpload::make('file_url')
                    ->label('Archivo')
                    ->directory('products/media')
                    ->acceptedFileTypes(['image/*', 'video/mp4'])
                    ->visible(fn(callable $get) => in_array($get('media_type'), ['image', 'video_mp4']))
                    ->required(fn(callable $get) => in_array($get('media_type'), ['image', 'video_mp4'])),  // ← Correcto

                // Campo para YouTube y Vimeo
                TextInput::make('video_id')
                    ->label('ID del Video')
                    ->helperText('Ej: dQw4w9wgxcq (YouTube) o 123456789 (Vimeo)')
                    ->visible(fn(callable $get) => in_array($get('media_type'), ['youtube', 'vimeo']))
                    ->required(fn(callable $get) => in_array($get('media_type'), ['youtube', 'vimeo'])),

                FileUpload::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->directory('products/thumbnails')
                    ->acceptedFileTypes(['image/*'])
                    ->image(),

                TextInput::make('order')->label('Orden')->numeric()->default(0),

                Toggle::make('is_main')->label('Principal')->default(false),
                Toggle::make('is_active')->label('Activo')->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Sin multimedia aún')
            ->emptyStateDescription('Haz clic en "Agregar Multimedia" para comenzar')
            ->emptyStateIcon('heroicon-o-photo')
            ->columns([
                Tables\Columns\TextColumn::make('order')->label('Orden')->sortable(),
                Tables\Columns\ImageColumn::make('thumbnail_url')->label('Vista')->square(),
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable(),
                Tables\Columns\TextColumn::make('media_type')->label('Tipo')->badge(),
                Tables\Columns\ToggleColumn::make('is_main')->label('Principal'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Activo'),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar Multimedia'),
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
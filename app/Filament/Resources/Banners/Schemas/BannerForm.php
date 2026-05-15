<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Banner')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('Tipo de contenido')
                            ->options([
                                'image' => 'Imagen',
                                'video_mp4' => 'Video MP4',
                                'youtube' => 'YouTube',
                            ])
                            ->default('image')
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        FileUpload::make('image_pc')
                            ->label('Imagen PC')
                            ->image()
                            ->directory('banners/images')
                            ->disk('public')
                            ->maxSize(5120)
                            ->preserveFilenames()
                            ->helperText('Recomendado: 1920x600 px. Máximo 5MB.')
                            ->columnSpan(1)
                            ->visible(fn(Get $get) => $get('type') === 'image'),

                        FileUpload::make('image_mobile')
                            ->label('Imagen Mobile')
                            ->image()
                            ->directory('banners/images')
                            ->disk('public')
                            ->maxSize(5120)
                            ->preserveFilenames()
                            ->helperText('Recomendado: 768x400 px. Máximo 5MB.')
                            ->columnSpan(1)
                            ->visible(fn(Get $get) => $get('type') === 'image'),

                        FileUpload::make('video_file')
                            ->label('Archivo de video (MP4)')
                            ->acceptedFileTypes(['video/mp4'])
                            ->directory('banners/videos')
                            ->disk('public')
                            ->maxSize(102400)
                            ->preserveFilenames()
                            ->helperText('Formato: MP4. Máximo 100MB.')
                            ->columnSpanFull()
                            ->visible(fn(Get $get) => $get('type') === 'video_mp4'),

                        TextInput::make('youtube_url')
                            ->label('URL de YouTube')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->helperText('Pega el enlace completo del video de YouTube.')
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->visible(fn(Get $get) => $get('type') === 'youtube'),

                        TextInput::make('order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}

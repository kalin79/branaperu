<?php
namespace App\Filament\Resources\PersonalCareSections\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PersonalCareSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contenido de la Sección')
                ->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    TextInput::make('subtitle')
                        ->label('Subtítulo')
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Descripción corta')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),

                    FileUpload::make('icon')
                        ->label('Icono')
                        ->image()
                        ->directory('personal-care/icons')
                        ->disk('public')
                        ->maxSize(1024)
                        ->preserveFilenames()
                        ->helperText('Icono de la sección. Máximo 1MB.')
                        ->columnSpan(1),

                    FileUpload::make('background_image')
                        ->label('Imagen de fondo')
                        ->image()
                        ->directory('personal-care/backgrounds')
                        ->disk('public')
                        ->maxSize(5120)
                        ->preserveFilenames()
                        ->helperText('Imagen de fondo de la sección. Máximo 5MB.')
                        ->columnSpan(1),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}
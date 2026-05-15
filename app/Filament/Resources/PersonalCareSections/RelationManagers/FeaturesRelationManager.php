<?php
namespace App\Filament\Resources\PersonalCareSections\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class FeaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'features';
    protected static ?string $title = 'Características';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            FileUpload::make('icon')
                ->label('Icono')
                ->image()
                ->directory('personal-care/features')
                ->disk('public')
                ->maxSize(1024)
                ->preserveFilenames()
                ->columnSpanFull(),

            Textarea::make('description')
                ->label('Descripción')
                ->rows(3)
                ->maxLength(500)
                ->columnSpanFull(),
            TextInput::make('color')
                ->label('Color (verde, amarrillo, violeta, rosado)')
                ->placeholder('verde')
                ->helperText('Colores disponibles: verde, amarrillo, violeta, rosado')
                ->maxLength(50),

            TextInput::make('order')
                ->label('Orden')
                ->numeric()
                ->default(0),

            Toggle::make('is_active')
                ->label('Activo')
                ->default(true),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Sin características aún')
            ->emptyStateDescription('Haz clic en "Agregar Característica" para comenzar')
            ->columns([
                ImageColumn::make('icon')->label('Icono')->disk('public')->square()->size(48),
                TextColumn::make('title')->label('Título')->searchable()->weight('bold'),
                TextColumn::make('order')->label('Orden')->sortable()->alignCenter(),
                ToggleColumn::make('is_active')->label('Activo'),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->headerActions([
                CreateAction::make()->label('Agregar Característica'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
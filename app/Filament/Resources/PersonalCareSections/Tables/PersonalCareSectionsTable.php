<?php
namespace App\Filament\Resources\PersonalCareSections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PersonalCareSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon')
                    ->label('Icono')
                    ->disk('public')
                    ->square()
                    ->size(48),

                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('subtitle')
                    ->label('Subtítulo')
                    ->toggleable(),

                TextColumn::make('features_count')
                    ->label('Características')
                    ->counts('features')
                    ->alignCenter(),
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
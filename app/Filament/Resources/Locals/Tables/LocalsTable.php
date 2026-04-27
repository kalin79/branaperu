<?php

namespace App\Filament\Resources\Locals\Tables;

use Filament\Tables;
use Filament\Tables\Table;
// use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;           // ← Aquí está el cambio
use Filament\Actions\DeleteBulkAction;
class LocalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Nombre del Local')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('label')
                    ->label('Etiqueta')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('short_description')
                    ->label('Descripción corta')
                    ->wrap()
                    ->limit(60),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activo'),
            ])
            ->defaultSort('sort_order')
            ->searchable(['title', 'address'])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
<?php

namespace App\Filament\Resources\ProductFeatures\Tables;   // ← Namespace correcto (Tables)

use Filament\Tables;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;           // ← Aquí está el cambio
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class ProductFeaturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activo'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),           // ← Botón eliminar por fila
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
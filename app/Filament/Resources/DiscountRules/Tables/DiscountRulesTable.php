<?php

namespace App\Filament\Resources\DiscountRules\Tables;

use Filament\Tables;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;           // ← Aquí está el cambio
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class DiscountRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('min_amount')
                    ->label('Monto mínimo')
                    ->money('PEN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Descuento')
                    ->suffix('%')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activa'),
            ])
            ->defaultSort('min_amount', 'asc')
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
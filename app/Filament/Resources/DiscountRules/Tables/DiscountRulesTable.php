<?php

namespace App\Filament\Resources\DiscountRules\Tables;

// use App\Models\DiscountRule;
use Filament\Tables;
use Filament\Tables\Table;

// use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;           // ← Aquí está el cambio
// use Filament\Actions\DeleteBulkAction;

class DiscountRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre de la regla')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('min_amount')
                    ->label('Monto mínimo')
                    ->money('PEN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Descuento')
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activo'),
            ])
            ->defaultSort('is_active', 'desc')           // ← Activos primero
            ->defaultSort('created_at', 'desc')          // Luego por fecha (secundario)
            ->searchable(['name'])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado'),
            ])
            ->actions([
                EditAction::make(),
                // ← Se eliminó DeleteAction
            ])
            ->bulkActions([
                // Se eliminó la posibilidad de eliminar en masa
                BulkActionGroup::make([]),
            ]);
    }
}
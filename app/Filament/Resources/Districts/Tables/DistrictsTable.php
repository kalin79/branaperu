<?php

namespace App\Filament\Resources\Districts\Tables;

use App\Models\District;
use Filament\Tables;
use Filament\Tables\Table;
// use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;           // ← Aquí está el cambio
use Filament\Actions\DeleteBulkAction;
class DistrictsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ubigeo')
                    ->label('Ubigeo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department')
                    ->label('Departamento')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('province')
                    ->label('Provincia')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('district')
                    ->label('Distrito')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('delivery_cost')
                    ->label('Costo Delivery')
                    ->money('PEN')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activo'),
            ])
            ->defaultSort('department', 'asc')
            ->searchable(['ubigeo', 'department', 'province', 'district'])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->label('Departamento')
                    ->options(fn() => District::distinct()->orderBy('department')->pluck('department', 'department')->all())
                    ->searchable(),
            ])
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
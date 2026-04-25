<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;           // ← Aquí está el cambio
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(),

                TextColumn::make('categoryType.name')
                    ->label('Tipo de Categoría'),

                TextColumn::make('parent.name')
                    ->label('Categoría Padre')
                    ->default('—'),

                ToggleColumn::make('is_active')
                    ->label('Activa'),

                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->label('Categoría Padre')
                    ->options(Category::whereNull('parent_id')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('category_type_id')
                    ->label('Tipo de Categoría')
                    ->relationship('categoryType', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('order', 'asc')
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
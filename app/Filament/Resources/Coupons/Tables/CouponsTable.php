<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),

                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Descuento')
                    ->formatStateUsing(fn($record) => $record->discount_type === 'percent'
                        ? $record->discount_value . '%'
                        : 'S/ ' . $record->discount_value),

                Tables\Columns\TextColumn::make('min_purchase_amount')
                    ->label('Mínimo')
                    ->formatStateUsing(fn($state) => 'S/ ' . $state),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Caduca')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activo'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
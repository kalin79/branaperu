<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('N° Orden')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('final_total')
                    ->label('Total')
                    ->money('PEN')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado Pedido')
                    ->badge()
                    ->color(fn(Order $record) => $record->status_color)
                    ->formatStateUsing(fn(Order $record) => $record->status_label),

                // === ESTADO DEL PAGO ===
                Tables\Columns\TextColumn::make('currentPayment.status')
                    ->label('Estado Pago')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        Payment::STATUS_APPROVED => 'success',
                        Payment::STATUS_REJECTED => 'danger',
                        Payment::STATUS_REFUNDED => 'warning',
                        Payment::STATUS_CHARGEBACK => 'danger',
                        Payment::STATUS_IN_PROCESS,
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(
                        fn(?string $state): string =>
                        $state ? Payment::getStatusOptions()[$state] ?? $state : 'Sin pago'
                    )
                    ->default('Sin pago'),

                // === MÉTODO DE PAGO ===
                Tables\Columns\TextColumn::make('currentPayment.payment_method')
                    ->label('Método')
                    ->default('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchable(['order_number', 'user.name'])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado Pedido')
                    ->options(Order::getStatusOptions()),
            ])
            ->actions([
                ViewAction::make()->label('Ver detalle'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
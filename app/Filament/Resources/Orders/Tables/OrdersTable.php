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

                // === TIPO DE CLIENTE (Invitado / Cliente) ===
                Tables\Columns\TextColumn::make('customer_type')
                    ->label('Tipo')
                    ->badge()
                    ->state(fn(Order $record): string => $record->user_id ? 'cliente' : 'invitado')
                    ->color(fn(string $state): string => match ($state) {
                        'cliente' => 'info',
                        'invitado' => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'cliente' => 'heroicon-m-user',
                        'invitado' => 'heroicon-m-user-circle',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cliente' => 'Cliente',
                        'invitado' => 'Invitado',
                    })
                    ->sortable(query: function ($query, string $direction) {
                        $query->orderByRaw('user_id IS NULL ' . $direction);
                    }),

                // === CLIENTE (usa el accessor customer_name del modelo) ===
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable(query: function ($query, string $search) {
                        $query->where(function ($q) use ($search) {
                            $q->whereHas('user', fn($u) => $u
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%"))
                                ->orWhere('guest_name', 'like', "%{$search}%")
                                ->orWhere('guest_last_name', 'like', "%{$search}%")
                                ->orWhere('guest_email', 'like', "%{$search}%")
                                ->orWhere('delivery_full_name', 'like', "%{$search}%");
                        });
                    })
                    ->description(fn(Order $record): ?string => $record->customer_email)
                    ->wrap(),

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
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado Pedido')
                    ->options(Order::getStatusOptions()),

                // === FILTRO POR TIPO DE CLIENTE ===
                Tables\Filters\Filter::make('customer_type')
                    ->label('Tipo de Cliente')
                    ->form([
                        \Filament\Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'cliente' => 'Cliente registrado',
                                'invitado' => 'Invitado (guest)',
                            ])
                            ->placeholder('Todos'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(($data['type'] ?? null) === 'cliente', fn($q) => $q->whereNotNull('user_id'))
                            ->when(($data['type'] ?? null) === 'invitado', fn($q) => $q->whereNull('user_id'));
                    }),
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
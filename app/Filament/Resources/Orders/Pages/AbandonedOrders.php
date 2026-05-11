<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class AbandonedOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = '🛒 Carritos Abandonados';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Clock;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::with(['user', 'district', 'latestPayment'])
            ->where('status', Order::STATUS_ABANDONED)
            ->latest();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('N° Orden')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                // === TIPO DE CLIENTE ===
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
                    }),

                // === CLIENTE (con accessor) ===
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

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado hace')
                    ->since()
                    ->sortable(),

                Tables\Columns\TextColumn::make('latestPayment.status')
                    ->label('Último Intento')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        Payment::STATUS_APPROVED => 'success',
                        Payment::STATUS_REJECTED => 'danger',
                        Payment::STATUS_IN_PROCESS,
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(
                        fn(?string $state): string =>
                        $state ? Payment::getStatusOptions()[$state] ?? $state : 'Sin intento'
                    ),

                Tables\Columns\TextColumn::make('latestPayment.payment_method')
                    ->label('Método')
                    ->default('-'),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(50)
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No hay carritos abandonados')
            ->emptyStateDescription('Las órdenes abandonadas aparecerán aquí automáticamente.');
    }
}
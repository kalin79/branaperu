<?php

namespace App\Filament\Resources\Payments;

use App\Exports\PaymentsExport;
use App\Models\Order;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|UnitEnum|null $navigationGroup = 'Ventas - Ecommerce';

    protected static ?string $navigationLabel = 'Pagos';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'external_id';

    /**
     * Estados de pago que SÍ son válidos para una edición manual.
     * El método de pago, monto y external_id NO se editan: vienen de MP.
     *
     * Transiciones permitidas (origen → destino):
     *   approved → approved   (no hay cambio)
     *   approved → refunded   (reembolso procesado fuera del flujo MP)
     *   approved → chargeback (cliente disputó el cobro con su banco)
     */
    public static function manualStatusOptions(): array
    {
        return [
            Payment::STATUS_APPROVED => Payment::getStatusOptions()[Payment::STATUS_APPROVED],
            Payment::STATUS_REFUNDED => Payment::getStatusOptions()[Payment::STATUS_REFUNDED],
            Payment::STATUS_CHARGEBACK => Payment::getStatusOptions()[Payment::STATUS_CHARGEBACK],
        ];
    }

    /**
     * Form de edición: SOLO se puede modificar el estado del pago,
     * y únicamente entre opciones manuales válidas.
     */
    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Información del Pago')
                    ->description('Datos sincronizados con MercadoPago — no editables.')
                    ->schema([
                        Placeholder::make('order_number')
                            ->label('N° Orden')
                            ->content(fn($record) => $record?->order?->order_number ?? '—'),

                        Placeholder::make('customer_name')
                            ->label('Cliente')
                            ->content(fn($record) => $record?->order?->customer_name ?? '—'),

                        Placeholder::make('external_id')
                            ->label('ID MercadoPago')
                            ->content(fn($record) => $record?->external_id ?? '—'),

                        Placeholder::make('amount')
                            ->label('Monto')
                            ->content(fn($record) => 'S/ ' . number_format($record?->amount ?? 0, 2)),

                        Placeholder::make('payment_method')
                            ->label('Método de Pago')
                            ->content(fn($record) => ucfirst($record?->payment_method ?? '—')),

                        Placeholder::make('attempts')
                            ->label('Intentos totales para esta orden')
                            ->content(fn($record) => $record?->order?->payments()->count() ?? 0),
                    ])
                    ->columns(2),

                Section::make('Estado del Pedido')
                    ->description('Avanza el pedido en su flujo: Preparando → Enviado → Entregado, o márcalo como Cancelado, Reembolsado, etc.')
                    ->schema([
                        Select::make('order_status')
                            ->label('Estado actual del pedido')
                            ->options(Order::getStatusOptions())
                            ->required()
                            ->native(false)
                            ->helperText('Esto actualiza el campo `status` de la Orden asociada.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Estado del Pago (uso excepcional)')
                    ->description(
                        'El estado del pago lo sincroniza MercadoPago automáticamente. ' .
                        'Solo cámbialo a mano para reflejar un Reembolso o un Chargeback.'
                    )
                    ->schema([
                        Select::make('status')
                            ->label('Estado del Pago')
                            ->options(self::manualStatusOptions())
                            ->required()
                            ->native(false)
                            ->helperText('El método de pago, monto e ID MP vienen de MercadoPago y no se modifican aquí.')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                // Solo pagos APROBADOS — una fila por orden (el más reciente aprobado).
                Payment::query()
                    ->where('status', Payment::STATUS_APPROVED)
                    ->whereIn('id', function ($q) {
                        $q->selectRaw('MAX(id)')
                            ->from('payments')
                            ->where('status', Payment::STATUS_APPROVED)
                            ->whereNull('deleted_at')
                            ->groupBy('order_id');
                    })
                    ->with([
                        'order' => fn($q) => $q
                            ->withCount('payments')
                            ->with('user'),
                    ])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('N° Orden')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer_type')
                    ->label('Tipo')
                    ->badge()
                    ->state(fn($record) => $record->order?->user_id ? 'cliente' : 'invitado')
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

                Tables\Columns\TextColumn::make('order.customer_name')
                    ->label('Cliente')
                    ->description(fn($record) => $record->order?->customer_email)
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('order', function ($q) use ($search) {
                            $q->where('guest_name', 'like', "%{$search}%")
                                ->orWhere('guest_last_name', 'like', "%{$search}%")
                                ->orWhere('guest_email', 'like', "%{$search}%")
                                ->orWhereHas('user', fn($u) => $u
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%"));
                        });
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('order.payments_count')
                    ->label('Intentos')
                    ->badge()
                    ->color(fn(?int $state): string => match (true) {
                        $state === null => 'gray',
                        $state === 1 => 'success',
                        $state <= 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn(?int $state) => $state === 1 ? '1 intento' : "{$state} intentos")
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('order.status_label')
                    ->label('Estado Pedido')
                    ->badge()
                    ->color(fn($record) => $record->order?->status_color ?? 'gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado Pago')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        Payment::STATUS_APPROVED => 'success',
                        Payment::STATUS_REJECTED => 'danger',
                        Payment::STATUS_CHARGEBACK => 'danger',
                        Payment::STATUS_REFUNDED => 'warning',
                        Payment::STATUS_IN_PROCESS,
                        Payment::STATUS_PENDING => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(
                        fn(?string $state) =>
                        $state ? (Payment::getStatusOptions()[$state] ?? $state) : '—'
                    ),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método')
                    ->default('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Último intento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('customer_type')
                    ->label('Tipo de Cliente')
                    ->form([
                        Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'cliente' => 'Cliente registrado',
                                'invitado' => 'Invitado',
                            ])
                            ->placeholder('Todos'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                ($data['type'] ?? null) === 'cliente',
                                fn($q) => $q->whereHas('order', fn($o) => $o->whereNotNull('user_id'))
                            )
                            ->when(
                                ($data['type'] ?? null) === 'invitado',
                                fn($q) => $q->whereHas('order', fn($o) => $o->whereNull('user_id'))
                            );
                    }),
            ])
            ->actions([
                ViewAction::make()->label('Ver'),

                EditAction::make()
                    ->label('Editar Estado')
                    // Solo aparece si el pago está APROBADO (única transición manual válida)
                    ->visible(fn(Payment $record) => $record->status === Payment::STATUS_APPROVED),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exportar a Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery();

                        return Excel::download(
                            new PaymentsExport($query),
                            'pagos_' . now()->format('Y-m-d_His') . '.xlsx'
                        );
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Payments\Pages\ManagePayments::route('/'),
            'view' => \App\Filament\Resources\Payments\Pages\ViewPayment::route('/{record}'),
            'edit' => \App\Filament\Resources\Payments\Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
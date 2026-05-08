<?php

namespace App\Filament\Resources\Payments;

use App\Models\Payment;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Actions\Action;
use UnitEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|UnitEnum|null $navigationGroup = 'Ventas - Ecommerce';

    protected static ?string $navigationLabel = 'Pagos';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'external_id';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Información del Pago')
                    ->description('Esta información no se puede modificar')
                    ->schema([
                        Placeholder::make('order_number')
                            ->label('N° Orden')
                            ->content(fn($record) => $record->order?->order_number ?? '—'),

                        Placeholder::make('external_id')
                            ->label('ID MercadoPago')
                            ->content(fn($record) => $record->external_id),

                        Placeholder::make('amount')
                            ->label('Monto Pagado')
                            ->content(fn($record) => 'S/ ' . number_format($record->amount ?? 0, 2)),

                        Placeholder::make('payment_method')
                            ->label('Método')
                            ->content(fn($record) => ucfirst($record->payment_method ?? '—')),

                        Placeholder::make('payment_status')
                            ->label('Estado del Pago')
                            ->content('Aprobado'),
                    ])
                    ->columns(2),

                Section::make('Estado del Pedido')
                    ->description('Cambia el estado logístico de esta orden')
                    ->schema([
                        Select::make('order_status')
                            ->label('Estado del Pedido')
                            ->options(\App\Models\Order::getStatusOptions())
                            ->default(fn($record) => $record->order?->status)
                            ->required()
                            ->columnSpanFull()
                            ->afterStateHydrated(function ($component, $record) {
                                $component->state($record->order?->status);
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::where('status', Payment::STATUS_APPROVED)
                    ->with('order')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('N° Orden')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('external_id')
                    ->label('ID MercadoPago')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('order.status_label')
                    ->label('Estado del Pedido')
                    ->badge()
                    ->color(fn($record) => $record->order?->status_color ?? 'gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado Pago')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn() => 'Aprobado'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método')
                    ->default('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make()->label('Editar Estado'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Descargar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(route('export.payments'))   // ← Cambiado a la ruta simple
                    ->color('success'),
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
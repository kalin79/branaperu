<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;

// use Filament\Actions\DeleteAction;
// use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;           // ← Aquí está el cambio
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
                    ->sortable(),

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
                    ->label('Estado')
                    ->badge()
                    ->color(fn(Order $record) => $record->status_color)
                    ->formatStateUsing(fn(Order $record) => $record->status_label),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_id')
                    ->label('ID Pago')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('district.full_name')
                    ->label('Distrito')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchable(['order_number', 'user.name'])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Order::getStatusOptions()),
            ])
            ->actions([
                ViewAction::make()->label('Ver detalle'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => static::exportToExcel()),   // ← Corrección aquí
            ]);
    }

    /**
     * Exportar a CSV (funciona desde cualquier vista: Todas, Pendientes, Exitosas)
     */
    public static function exportToExcel()
    {
        $filename = 'ventas_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        // Exporta todas las órdenes (puedes mejorarlo después para respetar filtros)
        $orders = Order::with(['user', 'items', 'district'])->latest()->get();

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'N° Orden',
                'Fecha',
                'Cliente',
                'Email',
                'Total',
                'Costo Delivery',
                'Descuento',
                'Estado',
                'ID Pago',
                'Distrito',
                'Dirección',
                'Notas'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->user?->name ?? 'Cliente invitado',
                    $order->user?->email ?? '-',
                    $order->final_total,
                    $order->delivery_cost ?? 0,
                    $order->discount_amount ?? 0,
                    $order->status_label,
                    $order->payment_id ?? 'Sin pago',
                    $order->district?->full_name ?? '-',
                    $order->shipping_address ?? '-',
                    $order->notes ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
<x-filament::page>
    <div class="space-y-6">
        <div id="print-area" class="bg-white p-8 border rounded-2xl shadow-sm">
            <div class="flex justify-between items-start mb-8 border-b pb-6">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Orden #{{ $record->order_number }}</h1>
                    <p class="text-gray-500 mt-1">{{ $record->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <span class="inline-flex px-5 py-2 rounded-2xl text-sm font-semibold"
                          style="background: {{ $record->status_color === 'success' ? '#10b981' : ($record->status_color === 'warning' ? '#f59e0b' : '#ef4444') }}; color: white;">
                        {{ $record->status_label }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="font-semibold text-lg mb-3">Cliente</h3>
                    <p><strong>Nombre:</strong> {{ $record->user?->name ?? 'Cliente invitado' }}</p>
                    <p><strong>Email:</strong> {{ $record->user?->email }}</p>
                    <p><strong>Dirección:</strong> {{ $record->shipping_address }}</p>
                    <p><strong>Distrito:</strong> {{ $record->district?->full_name ?? '-' }}</p>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-3">Resumen de Pago</h3>
                    <p><strong>Total:</strong> S/ {{ number_format($record->final_total, 2) }}</p>
                    <p><strong>ID Pago:</strong> {{ $record->payment_id ?? 'Sin pago' }}</p>
                </div>
            </div>

            <h3 class="font-semibold text-lg mb-4">Productos</h3>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3">Producto</th>
                        <th class="text-center py-3">Cant.</th>
                        <th class="text-right py-3">Precio Unit.</th>
                        <th class="text-right py-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($record->items as $item)
                    <tr class="border-b last:border-none">
                        <td class="py-4">{{ $item->product_name }} ({{ $item->ml }} ml)</td>
                        <td class="text-center py-4">{{ $item->quantity }}</td>
                        <td class="text-right py-4">S/ {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right py-4 font-medium">S/ {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end gap-3">
            <button onclick="window.print()" 
                    class="flex items-center gap-x-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium">
                <x-heroicon-o-printer class="w-5 h-5" />
                Imprimir Comprobante
            </button>
        </div>
    </div>
</x-filament::page>

<style>
    @media print {
        #print-area { box-shadow: none !important; border: 1px solid #ddd !important; }
        .fi-page { padding: 0 !important; }
    }
</style>
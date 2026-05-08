<x-filament::page>
    <style>
        .order-container {
            width: 100%;
        }

        .layoutContainer{
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .layoutContainer > section {
            flex: 1;
        }
        
        .info-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .dark .info-card {
            background: #1f2937;
            border-color: #4b5563;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }
        
        .dark .section-title {
            color: #f3f4f6;
        }
        
        .label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
        }
        
        .dark .label {
            color: #9ca3af;
        }
        
        .value {
            font-size: 22px;
            font-weight: 600;
            color: #111827;
        }
        
        .dark .value {
            color: #f3f4f6;
        }

        @media print {
            .fi-page { padding: 0 !important; background: white !important; }
            .info-card { box-shadow: none !important; border: 1px solid #ddd !important; }
            nav, .fi-header, .fi-sidebar { display: none !important; }
        }
    </style>

    <div class="order-container">
        <div class="layoutContainer">
        <!-- Información General -->
        <x-filament::section>
            <h2 class="section-title">📋 Información General</h2>
            <div class="info-card">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
                    <div>
                        <p class="label">N° Orden</p>
                        <p class="value">{{ $record->order_number }}</p>
                    </div>
                    <div>
                        <p class="label">Estado del Pedido</p>
                        <x-filament::badge :color="$record->status_color" size="lg">
                            {{ $record->status_label }}
                        </x-filament::badge>
                    </div>
                    <div>
                        <p class="label">Total</p>
                        <p class="value">S/ {{ number_format($record->final_total, 2) }}</p>
                    </div>
                </div>
            </div>
        </x-filament::section>

         <!-- Historial de Pagos -->
        <x-filament::section>
            <h2 class="section-title">💰 Historial de Pagos</h2>
            <div class="info-card">
                @if($record->payments->isEmpty())
                    <p class="text-gray-500 italic py-12 text-center">No hay pagos registrados para esta orden.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        @foreach($record->payments as $payment)
                            <div style=" border-radius: 12px; padding: 20px;">
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 20px;">
                                    <div>
                                        <p class="label">ID Pago</p>
                                        <p style="font-family: monospace;">{{ $payment->external_id }}</p>
                                    </div>
                                    <div>
                                        <p class="label">Estado</p>
                                        <x-filament::badge 
                                            :color="match($payment->status) {
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'refunded' => 'warning',
                                                default => 'gray'
                                            }">
                                          {{ \App\Models\Payment::getStatusOptions()[$payment->status] ?? $payment->status }}
                                        </x-filament::badge>
                                    </div>
                                    <div>
                                        <p class="label">Método</p>
                                        <p style="font-weight: 500;">{{ $payment->payment_method ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="label">Monto</p>
                                        <p style="font-weight: 600;">S/ {{ number_format($payment->amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-filament::section>
    </div>
       

       <!-- Dirección de Entrega -->
            <x-filament::section>
                <h2 class="section-title">📍 Dirección de Entrega</h2>
                <div class="info-card">
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div>
                            <p class="label">Destinatario</p>
                            <p style="font-weight: 500;">{{ $record->delivery_full_name }}</p>
                        </div>
                        <div>
                            <p class="label">Distrito</p>
                            <p style="font-weight: 500;">{{ $record->district?->full_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="label">Dirección Completa</p>
                            <p style="font-weight: 500;">{{ $record->shipping_address }}</p>
                        </div>
                        <div>
                            <p class="label">Referencia</p>
                            <p style="font-weight: 500;">{{ $record->delivery_reference ?? 'Sin referencia' }}</p>
                        </div>
                    </div>
                </div>
            </x-filament::section>

    </div>
</x-filament::page>
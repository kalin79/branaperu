<x-filament::page>
    <style>
        .order-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .detail-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .dark .detail-card {
            background: #1f2937;
            border-color: #4b5563;
        }
        
        .detail-label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
            font-weight: 500;
        }
        
        .dark .detail-label {
            color: #9ca3af;
        }
        
        .detail-value {
            font-size: 17px;
            font-weight: 600;
            color: #111827;
        }
        
        .dark .detail-value {
            color: #f3f4f6;
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        @media (min-width: 768px) {
            .grid-container {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media print {
            .fi-page { padding: 0 !important; background: white !important; }
            .detail-card { box-shadow: none !important; border: 1px solid #ddd !important; }
            nav, .fi-header, .fi-sidebar { display: none !important; }
        }
    </style>

    <div class="order-detail-container">

        <!-- Información del Pago -->
        <x-filament::section heading="Información del Pago">
            <div class="detail-card">
                <div class="grid-container">
                    <div>
                        <p class="detail-label">Orden</p>
                        <p class="detail-value">{{ $record->order?->order_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="detail-label">Estado del Pedido</p>
                        <x-filament::badge :color="$record->order?->status_color ?? 'gray'">
                            {{ $record->order?->status_label ?? 'Sin estado' }}
                        </x-filament::badge>
                    </div>
                    <div>
                        <p class="detail-label">ID MercadoPago</p>
                        <p class="detail-value font-mono">{{ $record->external_id }}</p>
                    </div>
                    <div>
                        <p class="detail-label">Estado del Pago</p>
                        <x-filament::badge color="success">Aprobado</x-filament::badge>
                    </div>
                    <div>
                        <p class="detail-label">Monto</p>
                        <p class="detail-value">S/ {{ number_format($record->amount ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="detail-label">Método de Pago</p>
                        <p class="detail-value capitalize">{{ $record->payment_method ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Respuesta Completa -->
        <x-filament::section heading="Respuesta Completa de MercadoPago">
            <div class="detail-card">
                <pre style="background:#f8fafc; padding:20px; border-radius:8px; overflow:auto; font-size:13px; line-height:1.5; color:#334155;" class="dark:bg-gray-900 dark:text-gray-300">
{{ json_encode($record->payment_response, JSON_PRETTY_PRINT) }}
                </pre>
            </div>
        </x-filament::section>

    </div>
</x-filament::page>
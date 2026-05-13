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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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

        /* Historial de intentos */
        .attempts-table {
            width: 100%;
            border-collapse: collapse;
        }

        .attempts-table thead th {
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            padding: 10px 12px;
            border-bottom: 2px solid #e5e7eb;
        }

        .dark .attempts-table thead th {
            color: #9ca3af;
            border-bottom-color: #4b5563;
        }

        .attempts-table tbody td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }

        .dark .attempts-table tbody td {
            border-bottom-color: #374151;
        }

        .attempts-table tbody tr:last-child td {
            border-bottom: none;
        }

        .attempts-table tbody tr.current-row {
            background: #ecfdf5;
        }

        .dark .attempts-table tbody tr.current-row {
            background: rgba(16, 185, 129, 0.1);
        }

        @media print {
            .fi-page { padding: 0 !important; background: white !important; }
            .detail-card { box-shadow: none !important; border: 1px solid #ddd !important; }
            nav, .fi-header, .fi-sidebar { display: none !important; }
        }
    </style>

    <div class="order-detail-container">

        {{-- ============ DATOS DEL CLIENTE ============ --}}
        <x-filament::section heading="👤 Cliente">
            <div class="detail-card">
                <div style="margin-bottom: 20px;">
                    @if ($record->order?->user_id)
                        <x-filament::badge color="info" icon="heroicon-m-user" size="lg">
                            Cliente Registrado
                        </x-filament::badge>
                    @else
                        <x-filament::badge color="gray" icon="heroicon-m-user-circle" size="lg">
                            Invitado
                        </x-filament::badge>
                    @endif
                </div>

                <div class="grid-container">
                    <div>
                        <p class="detail-label">Nombre</p>
                        <p class="detail-value">{{ $record->order?->customer_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="detail-label">Email</p>
                        <p class="detail-value" style="word-break: break-all;">
                            {{ $record->order?->customer_email ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="detail-label">Teléfono</p>
                        <p class="detail-value">{{ $record->order?->guest_phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="detail-label">DNI</p>
                        <p class="detail-value">{{ $record->order?->dni ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="detail-label">Fecha de nacimiento</p>
                        <p class="detail-value">
                            {{ $record->order?->display_birth_date?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- ============ ORDEN + PAGO ACTUAL ============ --}}
        <x-filament::section heading="💳 Información del Pago">
            <div class="detail-card">
                <div class="grid-container">
                    <div>
                        <p class="detail-label">N° Orden</p>
                        <p class="detail-value">{{ $record->order?->order_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="detail-label">Estado del Pedido</p>
                        <x-filament::badge :color="$record->order?->status_color ?? 'gray'" size="lg">
                            {{ $record->order?->status_label ?? 'Sin estado' }}
                        </x-filament::badge>
                    </div>
                    <div>
                        <p class="detail-label">ID MercadoPago</p>
                        <p class="detail-value" style="font-family: monospace;">{{ $record->external_id ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="detail-label">Estado del Pago</p>
                        <x-filament::badge
                            :color="match($record->status) {
                                'approved'   => 'success',
                                'rejected', 'chargeback' => 'danger',
                                'refunded'   => 'warning',
                                'in_process', 'pending' => 'warning',
                                default      => 'gray',
                            }"
                            size="lg"
                        >
                            {{ \App\Models\Payment::getStatusOptions()[$record->status] ?? $record->status }}
                        </x-filament::badge>
                    </div>
                    <div>
                        <p class="detail-label">Monto</p>
                        <p class="detail-value">S/ {{ number_format($record->amount ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="detail-label">Método de Pago</p>
                        <p class="detail-value" style="text-transform: capitalize;">
                            {{ $record->payment_method ?? '—' }}
                        </p>
                    </div>
                    @if ($record->paid_at)
                        <div>
                            <p class="detail-label">Aprobado el</p>
                            <p class="detail-value">{{ $record->paid_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                    @if ($record->failed_at)
                        <div>
                            <p class="detail-label">Rechazado el</p>
                            <p class="detail-value">{{ $record->failed_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </x-filament::section>

        {{-- ============ HISTORIAL DE INTENTOS ============ --}}
        @php
            $allAttempts = $record->order?->payments()->orderBy('id', 'asc')->get() ?? collect();
        @endphp

        @if ($allAttempts->count() > 1)
            <x-filament::section heading="🔁 Historial de Intentos ({{ $allAttempts->count() }})">
                <div class="detail-card" style="padding: 0; overflow-x: auto;">
                    <table class="attempts-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>ID MercadoPago</th>
                                <th>Estado</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allAttempts as $idx => $attempt)
                                <tr class="{{ $attempt->id === $record->id ? 'current-row' : '' }}">
                                    <td><strong>{{ $idx + 1 }}</strong></td>
                                    <td style="font-family: monospace; font-size: 12px;">
                                        {{ $attempt->external_id ?? '—' }}
                                    </td>
                                    <td>
                                        <x-filament::badge
                                            :color="match($attempt->status) {
                                                'approved'   => 'success',
                                                'rejected', 'chargeback' => 'danger',
                                                'refunded'   => 'warning',
                                                'in_process', 'pending' => 'warning',
                                                default      => 'gray',
                                            }"
                                        >
                                            {{ \App\Models\Payment::getStatusOptions()[$attempt->status] ?? $attempt->status }}
                                        </x-filament::badge>
                                        @if ($attempt->id === $record->id)
                                            <span style="font-size: 11px; color: #059669; margin-left: 6px;">← actual</span>
                                        @endif
                                    </td>
                                    <td>{{ $attempt->payment_method ?? '—' }}</td>
                                    <td><strong>S/ {{ number_format($attempt->amount ?? 0, 2) }}</strong></td>
                                    <td>{{ $attempt->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        {{-- ============ RESPUESTA COMPLETA ============ --}}
        @if (!empty($record->payment_response))
            <x-filament::section heading="📦 Respuesta Completa de MercadoPago">
                <div class="detail-card">
                    <pre style="background:#f8fafc; padding:20px; border-radius:8px; overflow:auto; font-size:13px; line-height:1.5; color:#334155; max-height: 400px;" class="dark:bg-gray-900 dark:text-gray-300">{{ json_encode($record->payment_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </x-filament::section>
        @endif

    </div>
</x-filament::page>

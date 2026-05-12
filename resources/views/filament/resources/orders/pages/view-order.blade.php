<x-filament::page>
    <style>
        .fi-section{
            margin-top: 2rem;
        }
        .order-container {
            width: 100%;
        }

        .layoutContainer {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .layoutContainer > section {
            flex: 1;
            min-width: 320px;
        }

        .info-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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

        .field-value {
            font-weight: 500;
            color: #111827;
        }

        .dark .field-value {
            color: #f3f4f6;
        }

        .grid-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        /* === TABLA DE PRODUCTOS === */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead th {
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            padding: 12px 16px;
            border-bottom: 2px solid #e5e7eb;
        }

        .dark .items-table thead th {
            color: #9ca3af;
            border-bottom-color: #4b5563;
        }

        .items-table tbody td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .dark .items-table tbody td {
            border-bottom-color: #374151;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .product-img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .dark .product-img {
            border-color: #4b5563;
            background: #111827;
        }

        .product-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }

        .dark .product-name {
            color: #f3f4f6;
        }

        .product-meta {
            font-size: 12px;
            color: #6b7280;
        }

        .dark .product-meta {
            color: #9ca3af;
        }

        .price-original {
            text-decoration: line-through;
            color: #9ca3af;
            font-size: 13px;
        }

        .text-right {
            text-align: right;
        }

        /* === DESGLOSE DE MONTOS === */
        .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            font-size: 15px;
        }

        .totals-row.discount {
            color: #059669;
        }

        .dark .totals-row.discount {
            color: #34d399;
        }

        .totals-row.total {
            border-top: 2px solid #e5e7eb;
            margin-top: 12px;
            padding-top: 18px;
            font-size: 22px;
            font-weight: 700;
        }

        .dark .totals-row.total {
            border-top-color: #4b5563;
        }

        @media print {
            .fi-page { padding: 0 !important; background: white !important; }
            .info-card { box-shadow: none !important; border: 1px solid #ddd !important; }
            nav, .fi-header, .fi-sidebar { display: none !important; }
        }
    </style>

    <div class="order-container">

        {{-- ============ FILA 1: INFORMACIÓN GENERAL + PAGOS ============ --}}
        <div class="layoutContainer">

            {{-- Información General --}}
            <x-filament::section>
                <h2 class="section-title">📋 Información General</h2>
                <div class="info-card">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
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
                        <div>
                            <p class="label">Fecha de Creación</p>
                            <p class="field-value">{{ $record->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </x-filament::section>

            {{-- Historial de Pagos --}}
            <x-filament::section>
                <h2 class="section-title">💰 Historial de Pagos</h2>
                <div class="info-card">
                    @if ($record->payments->isEmpty())
                        <p class="text-gray-500 italic py-12 text-center">No hay pagos registrados para esta orden.</p>
                    @else
                        <div style="display: flex; flex-direction: column; gap: 24px;">
                            @foreach ($record->payments as $payment)
                                <div style="border-radius: 12px; padding: 20px;">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 20px;">
                                        <div>
                                            <p class="label">ID Pago</p>
                                            <p style="font-family: monospace;">{{ $payment->external_id }}</p>
                                        </div>
                                        <div>
                                            <p class="label">Estado</p>
                                            <x-filament::badge :color="match ($payment->status) {
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'refunded' => 'warning',
                                                default => 'gray',
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

        {{-- ============ FILA 2: DATOS DEL CLIENTE + DOCUMENTO ============ --}}
        <div class="layoutContainer">

            {{-- 👤 DATOS DEL CLIENTE --}}
            <x-filament::section>
                <h2 class="section-title">👤 Datos del Cliente</h2>
                <div class="info-card">
                    <div style="margin-bottom: 20px;">
                        @if ($record->user_id)
                            <x-filament::badge color="info" icon="heroicon-m-user" size="lg">
                                Cliente Registrado
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="gray" icon="heroicon-m-user-circle" size="lg">
                                Invitado (Guest Checkout)
                            </x-filament::badge>
                        @endif
                    </div>

                    <div class="grid-fields">
                        <div>
                            <p class="label">Nombre</p>
                            <p class="field-value">
                                {{ $record->user?->name ?? $record->guest_name ?? '—' }}
                            </p>
                        </div>

                        @if (! $record->user_id)
                            <div>
                                <p class="label">Apellido</p>
                                <p class="field-value">{{ $record->guest_last_name ?? '—' }}</p>
                            </div>
                        @endif

                        <div>
                            <p class="label">Email</p>
                            <p class="field-value" style="word-break: break-all;">
                                {{ $record->customer_email ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="label">Teléfono</p>
                            <p class="field-value">{{ $record->guest_phone ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="label">DNI</p>
                            <p class="field-value">{{ $record->dni ?? '—' }}</p>
                        </div>

                        @if ($record->user_id)
                            <div>
                                <p class="label">ID Usuario</p>
                                <p class="field-value">#{{ $record->user_id }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Consentimientos --}}
                    @if ($record->accepted_marketing || $record->accepted_terms || $record->accepted_privacy)
                        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 8px; flex-wrap: wrap;">
                            @if ($record->accepted_terms)
                                <x-filament::badge color="success" icon="heroicon-m-check-circle">
                                    Aceptó Términos
                                </x-filament::badge>
                            @endif
                            @if ($record->accepted_privacy)
                                <x-filament::badge color="success" icon="heroicon-m-check-circle">
                                    Aceptó Privacidad
                                </x-filament::badge>
                            @endif
                            @if ($record->accepted_marketing)
                                <x-filament::badge color="info" icon="heroicon-m-envelope">
                                    Acepta Marketing
                                </x-filament::badge>
                            @endif
                        </div>
                    @endif
                </div>
            </x-filament::section>

            {{-- 🧾 DOCUMENTO DE VENTA --}}
            <x-filament::section>
                <h2 class="section-title">🧾 Documento de Venta</h2>
                <div class="info-card">
                    <div style="margin-bottom: 20px;">
                        @if ($record->isFactura())
                            <x-filament::badge color="warning" icon="heroicon-m-document-text" size="lg">
                                Factura
                            </x-filament::badge>
                        @elseif ($record->isBoleta())
                            <x-filament::badge color="success" icon="heroicon-m-receipt-percent" size="lg">
                                Boleta
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="gray" size="lg">Sin definir</x-filament::badge>
                        @endif
                    </div>

                    @if ($record->isFactura())
                        <div class="grid-fields">
                            <div>
                                <p class="label">RUC</p>
                                <p class="field-value" style="font-family: monospace;">
                                    {{ $record->billing_ruc ?? '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="label">Razón Social</p>
                                <p class="field-value">{{ $record->billing_business_name ?? '—' }}</p>
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <p class="label">Dirección Fiscal</p>
                                <p class="field-value">{{ $record->billing_address ?? '—' }}</p>
                            </div>
                        </div>
                    @else
                        <p style="color: #6b7280;">Se emitirá boleta a nombre de
                            <strong>{{ $record->customer_name }}</strong>
                            @if ($record->dni)
                                (DNI: {{ $record->dni }})
                            @endif
                        </p>
                    @endif
                </div>
            </x-filament::section>
        </div>

        {{-- ============ MÉTODO DE ENTREGA (Delivery o Pickup) ============ --}}
        @if ($record->isPickup())
            <x-filament::section>
                <h2 class="section-title">🏪 Retiro en Tienda</h2>
                <div class="info-card">
                    <div style="margin-bottom: 16px;">
                        <x-filament::badge color="primary" icon="heroicon-m-building-storefront" size="lg">
                            Retiro en Local
                        </x-filament::badge>
                    </div>
                    <div class="grid-fields">
                        <div>
                            <p class="label">Local</p>
                            <p class="field-value">{{ $record->pickup_local_name ?? '—' }}</p>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <p class="label">Dirección del Local</p>
                            <p class="field-value">{{ $record->pickup_local_address ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <h2 class="section-title">📍 Dirección de Entrega</h2>
                <div class="info-card">
                    <div style="margin-bottom: 16px;">
                        <x-filament::badge color="primary" icon="heroicon-m-truck" size="lg">
                            Envío a Domicilio
                        </x-filament::badge>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div>
                            <p class="label">Destinatario</p>
                            <p class="field-value">{{ $record->delivery_full_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="label">Distrito</p>
                            <p class="field-value">{{ $record->district?->full_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="label">Dirección Completa</p>
                            <p class="field-value">{{ $record->shipping_address ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="label">Referencia</p>
                            <p class="field-value">{{ $record->delivery_reference ?? 'Sin referencia' }}</p>
                        </div>
                        @if ($record->delivery_cost)
                            <div>
                                <p class="label">Costo de Envío</p>
                                <p class="field-value">S/ {{ number_format($record->delivery_cost, 2) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- ============ 🛍️ PRODUCTOS DE LA ORDEN ============ --}}
        <x-filament::section>
            <h2 class="section-title">
                🛍️ Productos
                <span style="font-size: 14px; font-weight: 400; color: #6b7280;">
                    ({{ $record->items->sum('quantity') }}
                    {{ $record->items->sum('quantity') === 1 ? 'unidad' : 'unidades' }})
                </span>
            </h2>
            <div class="info-card" style="padding: 0; overflow-x: auto;">
                @if ($record->items->isEmpty())
                    <p class="text-gray-500 italic py-12 text-center">No hay productos en esta orden.</p>
                @else
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Imagen</th>
                                <th>Producto</th>
                                <th class="text-right" style="width: 80px;">Cant.</th>
                                <th class="text-right" style="width: 130px;">Precio Unit.</th>
                                <th class="text-right" style="width: 110px;">Ahorro</th>
                                <th class="text-right" style="width: 130px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($record->items as $item)
                                <tr>
                                    <td>
                                        <img
                                            src="{{ $item->product_image ? asset('storage/' . $item->product_image) : asset('images/no-image.png') }}"
                                            alt="{{ $item->product_name }}"
                                            class="product-img"
                                            onerror="this.src='{{ asset('images/no-image.png') }}'"
                                        >
                                    </td>
                                    <td>
                                        <div class="product-name">{{ $item->product_name }}</div>
                                        <div class="product-meta">
                                            @if ($item->sku)
                                                <span>SKU: <strong>{{ $item->sku }}</strong></span>
                                            @endif
                                            @if ($item->ml)
                                                <span style="margin-left: 8px;">• {{ $item->ml }} ml</span>
                                            @endif
                                        </div>
                                        @if ($item->notes)
                                            <div class="product-meta" style="margin-top: 4px; font-style: italic;">
                                                📝 {{ $item->notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <span style="font-weight: 600;">×{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-right">
                                        @if ($item->original_price && $item->original_price > $item->unit_price)
                                            <div class="price-original">
                                                S/ {{ number_format($item->original_price, 2) }}
                                            </div>
                                        @endif
                                        <div style="font-weight: 600;">
                                            S/ {{ number_format($item->unit_price, 2) }}
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        @if ($item->savings > 0)
                                            <x-filament::badge color="success">
                                                -S/ {{ number_format($item->savings, 2) }}
                                            </x-filament::badge>
                                        @else
                                            <span style="color: #9ca3af;">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right" style="font-weight: 600;">
                                        S/ {{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </x-filament::section>

        {{-- ============ 💵 DESGLOSE DE MONTOS ============ --}}
        <x-filament::section>
            <h2 class="section-title">💵 Desglose de Montos</h2>
            <div class="info-card" style="max-width: 560px; margin-left: auto;">

                {{-- Subtotal --}}
                <div class="totals-row">
                    <span style="color: #6b7280;">Subtotal ({{ $record->items->sum('quantity') }} ítems)</span>
                    <span class="field-value">S/ {{ number_format($record->subtotal ?? 0, 2) }}</span>
                </div>

                {{-- =========================================================
                     DESCUENTOS APLICADOS
                     - `discount_amount` = monto real descontado (S/)
                     - `coupon_discount_value` = valor NOMINAL del cupón (ej. 10 → 10%)
                     - Mostramos siempre el monto REAL, y el valor nominal como contexto
                ========================================================= --}}

                @php
                    // Formato del valor nominal del cupón según su tipo (percent / fixed)
                    $couponTypeLabel = null;
                    if ($record->hasCouponApplied()) {
                        $couponType = $record->coupon?->discount_type;
                        $nominal = $record->coupon_discount_value;
                        if ($nominal !== null) {
                            $couponTypeLabel = match ($couponType) {
                                'percent' => rtrim(rtrim(number_format($nominal, 2), '0'), '.') . '%',
                                'fixed'   => 'S/ ' . number_format($nominal, 2),
                                default   => null,
                            };
                        }
                    }
                @endphp

                {{-- CASO 1: Solo cupón aplicado --}}
                @if ($record->hasCouponApplied() && ! $record->hasAutoDiscountApplied())
                    <div class="totals-row discount">
                        <span>
                            🎟️ Cupón
                            <strong>{{ $record->coupon_code }}</strong>
                            @if ($record->coupon_name)
                                <span style="font-size: 12px; opacity: 0.8;">({{ $record->coupon_name }})</span>
                            @endif
                            @if ($couponTypeLabel)
                                <span style="font-size: 12px; opacity: 0.8;">— {{ $couponTypeLabel }}</span>
                            @endif
                        </span>
                        <span style="font-weight: 600;">
                            -S/ {{ number_format($record->discount_amount ?? 0, 2) }}
                        </span>
                    </div>

                {{-- CASO 2: Solo regla automática aplicada --}}
                @elseif ($record->hasAutoDiscountApplied() && ! $record->hasCouponApplied())
                    <div class="totals-row discount">
                        <span>
                            🏷️ {{ $record->discount_rule_name }}
                            @if ($record->discount_rule_percent)
                                <span style="font-size: 12px; opacity: 0.8;">
                                    ({{ rtrim(rtrim(number_format($record->discount_rule_percent, 2), '0'), '.') }}%)
                                </span>
                            @endif
                        </span>
                        <span style="font-weight: 600;">
                            -S/ {{ number_format($record->discount_amount ?? 0, 2) }}
                        </span>
                    </div>

                {{-- CASO 3: Ambos aplicados (cupón + regla). Mostramos los dos como etiquetas
                     y un único monto total para evitar suposiciones sobre el split --}}
                @elseif ($record->hasCouponApplied() && $record->hasAutoDiscountApplied())
                    <div class="totals-row discount" style="align-items: flex-start;">
                        <span style="display: flex; flex-direction: column; gap: 4px;">
                            <span>
                                🎟️ Cupón <strong>{{ $record->coupon_code }}</strong>
                                @if ($couponTypeLabel)
                                    <span style="font-size: 12px; opacity: 0.8;">— {{ $couponTypeLabel }}</span>
                                @endif
                            </span>
                            <span>
                                🏷️ {{ $record->discount_rule_name }}
                                @if ($record->discount_rule_percent)
                                    <span style="font-size: 12px; opacity: 0.8;">
                                        ({{ rtrim(rtrim(number_format($record->discount_rule_percent, 2), '0'), '.') }}%)
                                    </span>
                                @endif
                            </span>
                        </span>
                        <span style="font-weight: 600;">
                            -S/ {{ number_format($record->discount_amount ?? 0, 2) }}
                        </span>
                    </div>

                {{-- CASO 4: Hay descuento pero sin etiqueta (fallback defensivo) --}}
                @elseif ($record->discount_amount > 0)
                    <div class="totals-row discount">
                        <span>Descuento aplicado</span>
                        <span style="font-weight: 600;">
                            -S/ {{ number_format($record->discount_amount, 2) }}
                        </span>
                    </div>
                @endif

                {{-- Costo de envío --}}
                @if ($record->isDelivery())
                    <div class="totals-row">
                        <span style="color: #6b7280;">🚚 Envío</span>
                        <span class="field-value">
                            @if ($record->delivery_cost > 0)
                                S/ {{ number_format($record->delivery_cost, 2) }}
                            @else
                                <span style="color: #059669;">Gratis</span>
                            @endif
                        </span>
                    </div>
                @else
                    <div class="totals-row">
                        <span style="color: #6b7280;">🏪 Retiro en tienda</span>
                        <span style="color: #059669; font-weight: 600;">Sin costo</span>
                    </div>
                @endif

                {{-- Total final --}}
                <div class="totals-row total">
                    <span>Total a pagar</span>
                    <span>S/ {{ number_format($record->final_total, 2) }}</span>
                </div>

                {{-- Ahorro total destacado --}}
                @if ($record->discount_amount > 0)
                    <div style="margin-top: 16px; padding: 12px; background: #ecfdf5; border-radius: 8px; text-align: center;">
                        <span style="color: #059669; font-weight: 600; font-size: 14px;">
                            🎉 ¡Ahorraste S/ {{ number_format($record->discount_amount, 2) }} en esta compra!
                        </span>
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- ============ NOTAS (si existen) ============ --}}
        @if ($record->notes)
            <x-filament::section>
                <h2 class="section-title">📝 Notas</h2>
                <div class="info-card">
                    <p style="white-space: pre-wrap;">{{ $record->notes }}</p>
                </div>
            </x-filament::section>
        @endif

    </div>
</x-filament::page>
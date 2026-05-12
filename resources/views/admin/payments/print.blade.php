@php
    use App\Models\Payment;
    use App\Models\Order;

    $order = $payment->order;
    $statusOrder = $order ? (Order::getStatusOptions()[$order->status] ?? $order->status) : '—';
    $statusPayment = Payment::getStatusOptions()[$payment->status] ?? $payment->status;
    $allAttempts = $order ? $order->payments->sortBy('id')->values() : collect();

    $paymentBadge = match($payment->status) {
        Payment::STATUS_APPROVED   => 'success',
        Payment::STATUS_REJECTED,
        Payment::STATUS_CHARGEBACK => 'danger',
        Payment::STATUS_REFUNDED   => 'warning',
        Payment::STATUS_PENDING,
        Payment::STATUS_IN_PROCESS => 'warning',
        default                    => 'gray',
    };
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago {{ $payment->external_id ?? $payment->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #111827;
            margin: 0;
            padding: 24px;
            font-size: 12px;
            line-height: 1.45;
        }
        .wrapper { max-width: 800px; margin: 0 auto; }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        header h1 { margin: 0; font-size: 22px; letter-spacing: 1px; }
        header .meta { text-align: right; font-size: 11px; color: #4B5563; }
        header .meta strong { color: #111827; font-size: 13px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px; }
        .card {
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .card h3 {
            margin: 0 0 6px 0;
            font-size: 11px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .card p { margin: 2px 0; }
        .card .mono { font-family: 'Courier New', monospace; }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 18px;
        }
        table.data th {
            background: #F3F4F6;
            text-align: left;
            padding: 8px;
            font-size: 11px;
            text-transform: uppercase;
            border-bottom: 1px solid #E5E7EB;
        }
        table.data td {
            padding: 8px;
            border-bottom: 1px solid #F3F4F6;
            vertical-align: top;
        }
        table.data tr:last-child td { border-bottom: none; }
        table.data tr.current { background: #ECFDF5; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totals {
            margin-left: auto;
            width: 320px;
            border-top: 2px solid #111827;
            padding-top: 8px;
            margin-bottom: 18px;
        }
        .totals .row { display: flex; justify-content: space-between; padding: 3px 0; }
        .totals .row.total {
            font-size: 16px;
            font-weight: bold;
            border-top: 1px solid #E5E7EB;
            margin-top: 6px;
            padding-top: 8px;
        }
        .totals .discount { color: #B91C1C; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #DCFCE7; color: #166534; }
        .badge-warning { background: #FEF3C7; color: #92400E; }
        .badge-danger  { background: #FEE2E2; color: #991B1B; }
        .badge-info    { background: #DBEAFE; color: #1E40AF; }
        .badge-gray    { background: #E5E7EB; color: #374151; }

        .big-amount { font-size: 26px; font-weight: 700; color: #111827; }

        footer {
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px dashed #E5E7EB;
            text-align: center;
            font-size: 10px;
            color: #6B7280;
        }

        .actions { margin-bottom: 14px; }
        .actions button {
            background: #111827;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }
        .actions button.secondary { background: #E5E7EB; color: #111827; }

        @media print {
            .actions { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="actions">
        <button onclick="window.print()">Imprimir</button>
        <button class="secondary" onclick="window.close()">Cerrar</button>
    </div>

    <header>
        <div>
            <h1>COMPROBANTE DE PAGO</h1>
            <div style="font-size:11px;color:#6B7280;">
                ID MercadoPago: <span style="font-family:monospace;">{{ $payment->external_id ?? '—' }}</span>
            </div>
            @if($order)
                <div style="font-size:11px;color:#6B7280;">
                    Orden: <strong>{{ $order->order_number }}</strong>
                    @if($order->document_type)
                        · {{ strtoupper($order->document_type) }}
                    @endif
                </div>
            @endif
        </div>
        <div class="meta">
            <div><strong>Fecha:</strong> {{ $payment->created_at?->format('d/m/Y H:i') }}</div>
            <div><strong>Estado Pago:</strong>
                <span class="badge badge-{{ $paymentBadge }}">{{ $statusPayment }}</span>
            </div>
            @if($order)
                <div><strong>Estado Pedido:</strong>
                    <span class="badge badge-{{ $order->status_color ?? 'gray' }}">{{ $statusOrder }}</span>
                </div>
            @endif
        </div>
    </header>

    {{-- Monto destacado --}}
    <div class="card" style="text-align:center; margin-bottom:18px;">
        <h3>Monto pagado</h3>
        <div class="big-amount">S/ {{ number_format((float) $payment->amount, 2) }}</div>
        <div style="font-size:11px;color:#6B7280;margin-top:4px;">
            Método: <strong>{{ ucfirst($payment->payment_method ?? '—') }}</strong>
            @if($payment->currency) · Moneda: {{ $payment->currency }} @endif
            @if($payment->paid_at) · Acreditado: {{ $payment->paid_at->format('d/m/Y H:i') }} @endif
        </div>
    </div>

    {{-- Cliente + Documento --}}
    <div class="grid-2">
        <div class="card">
            <h3>Cliente</h3>
            @if($order)
                <p>
                    <strong>{{ $order->customer_name }}</strong>
                    @if($order->user_id)
                        <span class="badge badge-info" style="margin-left:6px;">Cliente</span>
                    @else
                        <span class="badge badge-gray" style="margin-left:6px;">Invitado</span>
                    @endif
                </p>
                @if($order->customer_email) <p>{{ $order->customer_email }}</p> @endif
                @if($order->guest_phone)    <p>Tel: {{ $order->guest_phone }}</p> @endif
                @if($order->dni)            <p>DNI: {{ $order->dni }}</p> @endif
            @else
                <p>—</p>
            @endif
        </div>

        <div class="card">
            <h3>Comprobante solicitado</h3>
            @if($order && $order->isFactura())
                <p><strong>FACTURA</strong>
                    <span class="badge badge-info" style="margin-left:6px;">Empresa</span>
                </p>
                <p><strong>RUC:</strong> {{ $order->billing_ruc ?? '—' }}</p>
                <p><strong>Razón social:</strong> {{ $order->billing_business_name ?? '—' }}</p>
                @if($order->billing_address)
                    <p>{{ $order->billing_address }}</p>
                @endif
            @elseif($order && $order->isBoleta())
                <p><strong>BOLETA DE VENTA</strong>
                    <span class="badge badge-gray" style="margin-left:6px;">Consumidor final</span>
                </p>
                @if($order->dni) <p><strong>DNI:</strong> {{ $order->dni }}</p> @endif
            @else
                <p>—</p>
            @endif
        </div>
    </div>

    {{-- Método de entrega --}}
    @if($order)
        <div class="card" style="margin-bottom:18px;">
            <h3>Método de entrega</h3>
            @if($order->isDelivery())
                <p>
                    <strong>📦 Envío a domicilio</strong>
                    <span class="badge badge-info" style="margin-left:6px;">Delivery</span>
                </p>
                @if($order->delivery_full_name)
                    <p><strong>Recibe:</strong> {{ $order->delivery_full_name }}</p>
                @endif
                @if($order->shipping_address)
                    <p><strong>Dirección:</strong> {{ $order->shipping_address }}</p>
                @endif
                @if($order->district)
                    <p><strong>Distrito:</strong> {{ $order->district->name }}</p>
                @endif
                @if($order->delivery_reference)
                    <p><strong>Referencia:</strong> {{ $order->delivery_reference }}</p>
                @endif
                @if((float) $order->delivery_cost > 0)
                    <p><strong>Costo de envío:</strong> S/ {{ number_format((float) $order->delivery_cost, 2) }}</p>
                @endif
            @else
                <p>
                    <strong>🏬 Retiro en tienda</strong>
                    <span class="badge badge-gray" style="margin-left:6px;">Pickup</span>
                </p>
                @if($order->pickup_local_name)
                    <p><strong>Local:</strong> {{ $order->pickup_local_name }}</p>
                @endif
                @if($order->pickup_local_address)
                    <p><strong>Dirección:</strong> {{ $order->pickup_local_address }}</p>
                @endif
            @endif
        </div>

        {{-- Productos comprados --}}
        <h3 style="font-size:13px;margin:14px 0 6px;color:#111827;">Productos comprados</h3>
        <table class="data">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-center">SKU</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-right">P. Unit</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}
                            @if($item->ml)
                                <div style="font-size:10px;color:#6B7280;">{{ $item->ml }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->sku ?? '—' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">S/ {{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="text-right">S/ {{ number_format((float) $item->subtotal, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="color:#6B7280;">Sin productos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Totales --}}
        <div class="totals">
            <div class="row">
                <span>Subtotal</span>
                <span>S/ {{ number_format((float) $order->subtotal, 2) }}</span>
            </div>

            @if($order->hasCouponApplied())
                <div class="row discount">
                    <span>Cupón ({{ $order->coupon_code }})</span>
                    <span>− S/ {{ number_format((float) $order->discount_amount, 2) }}</span>
                </div>
            @elseif($order->hasAutoDiscountApplied())
                <div class="row discount">
                    <span>{{ $order->discount_rule_name }} ({{ rtrim(rtrim(number_format((float) $order->discount_rule_percent, 2), '0'), '.') }}%)</span>
                    <span>− S/ {{ number_format((float) $order->discount_amount, 2) }}</span>
                </div>
            @elseif((float) $order->discount_amount > 0)
                <div class="row discount">
                    <span>Descuento</span>
                    <span>− S/ {{ number_format((float) $order->discount_amount, 2) }}</span>
                </div>
            @endif

            @if($order->isDelivery() && (float) $order->delivery_cost > 0)
                <div class="row">
                    <span>Envío</span>
                    <span>S/ {{ number_format((float) $order->delivery_cost, 2) }}</span>
                </div>
            @endif

            <div class="row total">
                <span>TOTAL</span>
                <span>S/ {{ number_format((float) $order->final_total, 2) }}</span>
            </div>
        </div>
    @endif

    {{-- Historial de intentos --}}
    @if($allAttempts->count() > 1)
        <h3 style="font-size:13px;margin:14px 0 6px;color:#111827;">Historial de intentos para esta orden</h3>
        <table class="data">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>ID MercadoPago</th>
                    <th>Estado</th>
                    <th>Método</th>
                    <th class="text-right">Monto</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allAttempts as $idx => $a)
                    @php
                        $aBadge = match($a->status) {
                            Payment::STATUS_APPROVED   => 'success',
                            Payment::STATUS_REJECTED,
                            Payment::STATUS_CHARGEBACK => 'danger',
                            Payment::STATUS_REFUNDED   => 'warning',
                            Payment::STATUS_PENDING,
                            Payment::STATUS_IN_PROCESS => 'warning',
                            default                    => 'gray',
                        };
                    @endphp
                    <tr class="{{ $a->id === $payment->id ? 'current' : '' }}">
                        <td><strong>{{ $idx + 1 }}</strong></td>
                        <td class="mono" style="font-size:11px;">{{ $a->external_id ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $aBadge }}">
                                {{ Payment::getStatusOptions()[$a->status] ?? $a->status }}
                            </span>
                            @if($a->id === $payment->id)
                                <span style="font-size:10px;color:#059669;margin-left:4px;">← actual</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($a->payment_method ?? '—') }}</td>
                        <td class="text-right">S/ {{ number_format((float) $a->amount, 2) }}</td>
                        <td>{{ $a->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($order && $order->notes)
        <div class="card" style="margin-top:14px;">
            <h3>Notas</h3>
            <p>{{ $order->notes }}</p>
        </div>
    @endif

    <footer>
        Documento generado el {{ now()->format('d/m/Y H:i') }} ·
        Pago {{ $payment->external_id ?? $payment->id }}
    </footer>

</div>

<script>
    window.addEventListener('load', () => {
        setTimeout(() => window.print(), 300);
    });
</script>
</body>
</html>
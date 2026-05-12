@php
    use App\Models\Order;
    use App\Models\Payment;

    $payment = $order->latestPayment ?? $order->payments->sortByDesc('id')->first();
    $statusOrder = Order::getStatusOptions()[$order->status] ?? $order->status;
    $statusPayment = $payment ? (Payment::getStatusOptions()[$payment->status] ?? $payment->status) : 'Sin pago';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante {{ $order->order_number }}</title>
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
        header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 1px;
        }
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

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 18px;
        }
        table.items th {
            background: #F3F4F6;
            text-align: left;
            padding: 8px;
            font-size: 11px;
            text-transform: uppercase;
            border-bottom: 1px solid #E5E7EB;
        }
        table.items td {
            padding: 8px;
            border-bottom: 1px solid #F3F4F6;
            vertical-align: top;
        }
        table.items tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totals {
            margin-left: auto;
            width: 320px;
            border-top: 2px solid #111827;
            padding-top: 8px;
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }
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
        .actions button.secondary {
            background: #E5E7EB;
            color: #111827;
        }

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
            <h1>COMPROBANTE</h1>
            <div style="font-size:11px;color:#6B7280;">
                {{ strtoupper($order->document_type ?? 'boleta') }} — N° {{ $order->order_number }}
            </div>
        </div>
        <div class="meta">
            <div><strong>Fecha:</strong> {{ $order->created_at?->format('d/m/Y H:i') }}</div>
            <div><strong>Estado Pedido:</strong>
                <span class="badge badge-{{ $order->status_color ?? 'gray' }}">{{ $statusOrder }}</span>
            </div>
            <div><strong>Estado Pago:</strong>
                @php
                    $pBadge = match($payment?->status) {
                        Payment::STATUS_APPROVED => 'success',
                        Payment::STATUS_REJECTED, Payment::STATUS_CHARGEBACK => 'danger',
                        Payment::STATUS_REFUNDED => 'warning',
                        Payment::STATUS_PENDING, Payment::STATUS_IN_PROCESS => 'warning',
                        default => 'gray',
                    };
                @endphp
                <span class="badge badge-{{ $pBadge }}">{{ $statusPayment }}</span>
            </div>
        </div>
    </header>

    <div class="grid-2">
        <div class="card">
            <h3>Cliente</h3>
            <p><strong>{{ $order->customer_name }}</strong></p>
            @if($order->customer_email)
                <p>{{ $order->customer_email }}</p>
            @endif
            @if($order->guest_phone)
                <p>Tel: {{ $order->guest_phone }}</p>
            @endif
            @if($order->dni)
                <p>DNI: {{ $order->dni }}</p>
            @endif

            @if($order->isFactura())
                <div style="margin-top:6px;padding-top:6px;border-top:1px dashed #E5E7EB;">
                    <p><strong>RUC:</strong> {{ $order->billing_ruc }}</p>
                    <p><strong>Razón social:</strong> {{ $order->billing_business_name }}</p>
                    @if($order->billing_address)
                        <p>{{ $order->billing_address }}</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="card">
            <h3>Entrega</h3>
            @if($order->isDelivery())
                <p><strong>Envío a domicilio</strong></p>
                <p>{{ $order->delivery_full_name }}</p>
                <p>{{ $order->shipping_address }}</p>
                @if($order->district)
                    <p>Distrito: {{ $order->district->name }}</p>
                @endif
                @if($order->delivery_reference)
                    <p>Ref: {{ $order->delivery_reference }}</p>
                @endif
            @else
                <p><strong>Retiro en tienda</strong></p>
                <p>{{ $order->pickup_local_name }}</p>
                @if($order->pickup_local_address)
                    <p>{{ $order->pickup_local_address }}</p>
                @endif
            @endif
        </div>
    </div>

    <table class="items">
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
        @elseif($order->discount_amount > 0)
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

    @if($payment)
        <div class="card" style="margin-top:18px;">
            <h3>Pago</h3>
            <p>
                <strong>Método:</strong> {{ ucfirst($payment->payment_method ?? '—') }}
                @if($payment->external_id)
                    &nbsp;·&nbsp; <strong>ID MP:</strong> {{ $payment->external_id }}
                @endif
            </p>
            @if($payment->paid_at)
                <p><strong>Acreditado el:</strong> {{ $payment->paid_at->format('d/m/Y H:i') }}</p>
            @endif
        </div>
    @endif

    @if($order->notes)
        <div class="card" style="margin-top:14px;">
            <h3>Notas</h3>
            <p>{{ $order->notes }}</p>
        </div>
    @endif

    <footer>
        Documento generado el {{ now()->format('d/m/Y H:i') }} ·
        Comprobante {{ $order->order_number }}
    </footer>

</div>

<script>
    // Auto-abrir el diálogo de imprimir al cargar
    window.addEventListener('load', () => {
        setTimeout(() => window.print(), 300);
    });
</script>
</body>
</html>

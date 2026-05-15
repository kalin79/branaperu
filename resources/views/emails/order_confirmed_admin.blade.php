<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva venta aprobada - Brana</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f4;font-family:Arial,Helvetica,sans-serif;color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f4;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="680" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;">

                    <tr>
                        <td style="background:#1b5e20;padding:22px 28px;color:#fff;">
                            <h1 style="margin:0;font-size:20px;">💰 Nueva venta aprobada</h1>
                            <p style="margin:6px 0 0;font-size:13px;opacity:.9;">
                                {{ $order->created_at->format('d/m/Y H:i') }} hrs
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 28px;">

                            <!-- Datos clave -->
                            <table width="100%" cellpadding="8" cellspacing="0" style="font-size:14px;margin-bottom:18px;">
                                <tr>
                                    <td style="background:#f1f5f1;width:35%;font-weight:bold;">N° Orden</td>
                                    <td><strong>{{ $order->order_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="background:#f1f5f1;font-weight:bold;">Total</td>
                                    <td style="color:#1b5e20;font-weight:bold;font-size:16px;">S/ {{ number_format($order->final_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#f1f5f1;font-weight:bold;">Cliente</td>
                                    <td>{{ $order->customer_name }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#f1f5f1;font-weight:bold;">Correo</td>
                                    <td><a href="mailto:{{ $order->customer_email }}" style="color:#2e7d32;">{{ $order->customer_email }}</a></td>
                                </tr>
                                <tr>
                                    <td style="background:#f1f5f1;font-weight:bold;">Celular</td>
                                    <td>{{ $order->guest_phone }}</td>
                                </tr>
                                @if($order->dni)
                                    <tr>
                                        <td style="background:#f1f5f1;font-weight:bold;">DNI / CE</td>
                                        <td>{{ $order->dni }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="background:#f1f5f1;font-weight:bold;">Documento</td>
                                    <td>
                                        @if($order->isFactura())
                                            FACTURA — RUC {{ $order->billing_ruc }}<br>
                                            <small>{{ $order->billing_business_name }}</small>
                                        @else
                                            BOLETA
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background:#f1f5f1;font-weight:bold;">Entrega</td>
                                    <td>
                                        @if($order->isPickup())
                                            <strong>Retiro en tienda:</strong> {{ $order->pickup_local_name }}<br>
                                            <small>{{ $order->pickup_local_address }}</small>
                                        @else
                                            <strong>Delivery:</strong> {{ $order->shipping_address }}
                                            @if($order->delivery_reference)
                                                <br><small>Ref.: {{ $order->delivery_reference }}</small>
                                            @endif
                                            @if($order->district)
                                                <br><small>{{ $order->district->district }}, {{ $order->district->province }}</small>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Tabla de items -->
                            <h3 style="font-size:14px;color:#1b5e20;margin:20px 0 8px;">Productos</h3>
                            <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:13px;">
                                <thead>
                                    <tr style="background:#f1f5f1;">
                                        <th align="left">SKU</th>
                                        <th align="left">Producto</th>
                                        <th align="center">Cant.</th>
                                        <th align="right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr style="border-bottom:1px solid #eee;">
                                            <td><small>{{ $item->sku }}</small></td>
                                            <td>{!! $item->product_name !!} @if($item->ml)<br><small style="color:#777;">{{ $item->ml }}</small>@endif</td>
                                            <td align="center">{{ $item->quantity }}</td>
                                            <td align="right">S/ {{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" align="right" style="color:#555;">Subtotal:</td>
                                        <td align="right">S/ {{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    @if($order->discount_amount > 0)
                                        <tr>
                                            <td colspan="3" align="right" style="color:#2e7d32;">
                                                Descuento
                                                @if($order->coupon_code)
                                                    ({{ $order->coupon_code }})
                                                @elseif($order->discount_rule_name)
                                                    ({{ $order->discount_rule_name }})
                                                @endif:
                                            </td>
                                            <td align="right" style="color:#2e7d32;">- S/ {{ number_format($order->discount_amount, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" align="right" style="color:#555;">Envío:</td>
                                        <td align="right">S/ {{ number_format($order->delivery_cost, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" align="right" style="font-weight:bold;color:#1b5e20;">Total:</td>
                                        <td align="right" style="font-weight:bold;color:#1b5e20;">S/ {{ number_format($order->final_total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>

                            <p style="margin:22px 0 0;font-size:12px;color:#777;">
                                Puedes responder este correo y le llegará directamente al cliente.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:12px 28px;font-size:11px;color:#888;text-align:center;">
                            {{ $company['name'] }} — Notificación automática del sistema
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido confirmado - Brana</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f4;font-family:Arial,Helvetica,sans-serif;color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f4;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="680" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);">

                    <!-- HEADER -->
                    <tr>
                        <td style="background:#2e7d32;padding:28px 32px;color:#fff;">
                            <h1 style="margin:0 0 6px;font-size:24px;font-weight:600;">
                                ¡Hola {{ $order->guest_name }}!
                            </h1>
                            <p style="margin:0;font-size:15px;opacity:.95;">
                                Tu pedido fue confirmado correctamente.
                            </p>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:28px 32px;">

                            <p style="margin:0 0 18px;font-size:15px;line-height:1.55;">
                                Gracias por elegir <strong>Brana</strong>. Ya recibimos el pago y estamos preparando tu pedido con todo el cuidado que merece.
                            </p>

                            <!-- N° de pedido -->
                            <div style="background:#f1f7f1;border-left:4px solid #2e7d32;padding:16px 18px;border-radius:6px;margin:18px 0 24px;">
                                <p style="margin:0;font-size:13px;color:#555;">N° de Pedido</p>
                                <p style="margin:2px 0 0;font-size:22px;font-weight:700;color:#1b5e20;letter-spacing:.3px;">
                                    {{ $order->order_number }}
                                </p>
                                <p style="margin:8px 0 0;font-size:12px;color:#666;">
                                    Realizado el {{ $order->created_at->format('d/m/Y') }} a las {{ $order->created_at->format('H:i') }} hrs
                                </p>
                            </div>

                            <!-- Tabla de productos -->
                            <h3 style="font-size:15px;color:#2e7d32;margin:0 0 12px;border-bottom:1px solid #e3ece3;padding-bottom:6px;">
                                Detalle del pedido
                            </h3>

                            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;font-size:14px;margin-bottom:18px;">
                                <thead>
                                    <tr style="background:#fafafa;">
                                        <th align="left" style="font-size:12px;color:#555;text-transform:uppercase;letter-spacing:.5px;">Producto</th>
                                        <th align="center" style="font-size:12px;color:#555;text-transform:uppercase;letter-spacing:.5px;">Cant.</th>
                                        <th align="right" style="font-size:12px;color:#555;text-transform:uppercase;letter-spacing:.5px;">Precio</th>
                                        <th align="right" style="font-size:12px;color:#555;text-transform:uppercase;letter-spacing:.5px;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr style="border-bottom:1px solid #f0f0f0;">
                                            <td>
                                                <strong>{!! $item->product_name !!}</strong>
                                                @if($item->ml)
                                                    <br><small style="color:#888;">{{ $item->ml }}</small>
                                                @endif
                                            </td>
                                            <td align="center">{{ $item->quantity }}</td>
                                            <td align="right">S/ {{ number_format($item->unit_price, 2) }}</td>
                                            <td align="right"><strong>S/ {{ number_format($item->subtotal, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Resumen -->
                            <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-size:14px;margin-bottom:24px;">
                                <tr>
                                    <td align="right" style="color:#555;">Subtotal:</td>
                                    <td align="right" style="width:120px;">S/ {{ number_format($order->subtotal, 2) }}</td>
                                </tr>

                                @if($order->coupon_code)
                                    <tr>
                                        <td align="right" style="color:#2e7d32;">Cupón {{ $order->coupon_code }}:</td>
                                        <td align="right" style="color:#2e7d32;">- S/ {{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                @elseif($order->discount_rule_name)
                                    <tr>
                                        <td align="right" style="color:#2e7d32;">{{ $order->discount_rule_name }}:</td>
                                        <td align="right" style="color:#2e7d32;">- S/ {{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                @endif

                                <tr>
                                    <td align="right" style="color:#555;">
                                        @if($order->isPickup())
                                            Envío:
                                        @else
                                            Envío:
                                        @endif
                                    </td>
                                    <td align="right">
                                        @if($order->isPickup() || (float)$order->delivery_cost === 0.0)
                                            <span style="color:#2e7d32;">Gratis</span>
                                        @else
                                            S/ {{ number_format($order->delivery_cost, 2) }}
                                        @endif
                                    </td>
                                </tr>

                                <tr style="border-top:2px solid #2e7d32;">
                                    <td align="right" style="font-size:16px;font-weight:700;color:#1b5e20;padding-top:10px;">Total:</td>
                                    <td align="right" style="font-size:18px;font-weight:700;color:#1b5e20;padding-top:10px;">
                                        S/ {{ number_format($order->final_total, 2) }}
                                    </td>
                                </tr>
                            </table>

                            <!-- Entrega -->
                            <h3 style="font-size:15px;color:#2e7d32;margin:0 0 10px;border-bottom:1px solid #e3ece3;padding-bottom:6px;">
                                @if($order->isPickup())
                                    Retiro en tienda
                                @else
                                    Envío a domicilio
                                @endif
                            </h3>

                            @if($order->isPickup())
                                <table width="100%" cellpadding="6" cellspacing="0" style="font-size:14px;margin-bottom:18px;">
                                    <tr>
                                        <td style="color:#666;width:120px;">Tienda:</td>
                                        <td><strong>{{ $order->pickup_local_name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="color:#666;vertical-align:top;">Dirección:</td>
                                        <td>{{ $order->pickup_local_address }}</td>
                                    </tr>
                                </table>
                                <p style="font-size:13px;color:#555;background:#fff8e1;border:1px solid #ffe082;padding:10px 14px;border-radius:6px;">
                                    Te avisaremos cuando tu pedido esté listo para recoger.
                                </p>
                            @else
                                <table width="100%" cellpadding="6" cellspacing="0" style="font-size:14px;margin-bottom:18px;">
                                    <tr>
                                        <td style="color:#666;width:120px;">Destinatario:</td>
                                        <td>{{ $order->delivery_full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#666;vertical-align:top;">Dirección:</td>
                                        <td>
                                            {{ $order->shipping_address }}
                                            @if($order->delivery_reference)
                                                <br><small style="color:#777;">Ref.: {{ $order->delivery_reference }}</small>
                                            @endif
                                            @if($order->district)
                                                <br><small style="color:#777;">{{ $order->district->district }}, {{ $order->district->province }}, {{ $order->district->department }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#666;">Celular:</td>
                                        <td>{{ $order->guest_phone }}</td>
                                    </tr>
                                </table>
                                <p style="font-size:13px;color:#555;background:#fff8e1;border:1px solid #ffe082;padding:10px 14px;border-radius:6px;">
                                    Te enviaremos un correo en cuanto tu pedido salga a entrega.
                                </p>
                            @endif

                            <!-- Documento si es factura -->
                            @if($order->isFactura())
                                <h3 style="font-size:15px;color:#2e7d32;margin:24px 0 10px;border-bottom:1px solid #e3ece3;padding-bottom:6px;">
                                    Datos de facturación
                                </h3>
                                <table width="100%" cellpadding="6" cellspacing="0" style="font-size:14px;margin-bottom:18px;">
                                    <tr>
                                        <td style="color:#666;width:120px;">Razón social:</td>
                                        <td>{{ $order->billing_business_name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#666;">RUC:</td>
                                        <td>{{ $order->billing_ruc }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#666;vertical-align:top;">Dirección fiscal:</td>
                                        <td>{{ $order->billing_address }}</td>
                                    </tr>
                                </table>
                            @endif

                            <p style="margin:24px 0 0;font-size:14px;line-height:1.55;">
                                Si tienes alguna duda, escríbenos a <a href="mailto:branasac@gmail.com" style="color:#2e7d32;">branasac@gmail.com</a> o por WhatsApp al <a href="https://wa.me/51955128016" style="color:#2e7d32;">+51 955 128 016</a>.
                            </p>

                            <p style="margin:18px 0 0;font-size:14px;">
                                Un abrazo,<br>
                                <strong>Equipo Brana</strong><br>
                                <span style="font-size:12px;color:#888;">Mente &amp; Cuerpo en equilibrio natural</span>
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background:#fafafa;padding:16px 32px;font-size:11px;color:#888;text-align:center;line-height:1.5;">
                            {{ $company['name'] }} — RUC: {{ $company['ruc'] }}<br>
                            {{ $company['address'] }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo reclamo en el Libro de Reclamaciones</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f4;font-family:Arial,Helvetica,sans-serif;color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f4;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="640" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#b71c1c;padding:20px 24px;color:#fff;">
                            <h1 style="margin:0;font-size:18px;">
                                Nuevo {{ $claim->claim_type_label }} — {{ $claim->claim_number }}
                            </h1>
                            <p style="margin:4px 0 0;font-size:12px;opacity:.9;">
                                Recibido el {{ $claim->created_at->format('d/m/Y H:i') }} hrs
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:22px 24px;">

                            <p style="margin:0 0 16px;font-size:13px;color:#444;">
                                Se ha registrado un nuevo {{ strtolower($claim->claim_type_label) }} en el Libro de Reclamaciones. Plazo de respuesta: <strong>15 días hábiles</strong>.
                            </p>

                            <h3 style="margin:18px 0 8px;font-size:14px;color:#b71c1c;">Consumidor</h3>
                            <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-size:13px;">
                                <tr><td style="background:#f1f5f1;width:35%;font-weight:bold;">Nombre</td><td>{{ $claim->full_name }}</td></tr>
                                <tr><td style="background:#f1f5f1;font-weight:bold;">Documento</td><td>{{ $claim->consumer_document_type }}: {{ $claim->consumer_document_number }}</td></tr>
                                <tr><td style="background:#f1f5f1;font-weight:bold;">Correo</td><td><a href="mailto:{{ $claim->consumer_email }}" style="color:#2e7d32;">{{ $claim->consumer_email }}</a></td></tr>
                                <tr><td style="background:#f1f5f1;font-weight:bold;">Celular</td><td>{{ $claim->consumer_phone }}</td></tr>
                            </table>

                            @if($claim->product_name || $claim->order_number || $claim->product_description)
                            <h3 style="margin:22px 0 8px;font-size:14px;color:#b71c1c;">Bien / Servicio</h3>
                            <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-size:13px;">
                                @if($claim->product_name)<tr><td style="background:#f1f5f1;width:35%;font-weight:bold;">Producto</td><td>{{ $claim->product_name }}</td></tr>@endif
                                @if($claim->order_number)<tr><td style="background:#f1f5f1;font-weight:bold;">N° pedido</td><td>{{ $claim->order_number }}</td></tr>@endif
                                @if($claim->product_description)<tr><td style="background:#f1f5f1;font-weight:bold;vertical-align:top;">Descripción</td><td style="white-space:pre-line;">{{ $claim->product_description }}</td></tr>@endif
                            </table>
                            @endif

                            <h3 style="margin:22px 0 8px;font-size:14px;color:#b71c1c;">Detalle</h3>
                            <div style="background:#fafafa;border:1px solid #eee;border-radius:6px;padding:14px;font-size:13px;line-height:1.5;white-space:pre-line;">{{ $claim->claim_detail }}</div>

                            <h3 style="margin:22px 0 8px;font-size:14px;color:#b71c1c;">Pedido del consumidor</h3>
                            <div style="background:#fafafa;border:1px solid #eee;border-radius:6px;padding:14px;font-size:13px;line-height:1.5;white-space:pre-line;">{{ $claim->consumer_request }}</div>

                            <p style="margin:22px 0 0;font-size:12px;color:#777;">
                                Puedes responder este correo y le llegará directamente al consumidor.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:12px 24px;font-size:11px;color:#888;text-align:center;">
                            {{ $company['name'] }} — Libro de Reclamaciones
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
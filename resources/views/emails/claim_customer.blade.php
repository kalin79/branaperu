<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hemos recibido tu reclamo - Brana</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f4;font-family:Arial,Helvetica,sans-serif;color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f4;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="640" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);">

                    <!-- HEADER -->
                    <tr>
                        <td style="background:#2e7d32;padding:28px 32px;color:#fff;">
                            <h1 style="margin:0 0 6px;font-size:22px;font-weight:600;">
                                Hola {{ $claim->consumer_first_name }},
                            </h1>
                            <p style="margin:0;font-size:14px;opacity:.9;">
                                Hemos recibido tu {{ $claim->claim_type_label }} correctamente.
                            </p>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.55;">
                                Gracias por escribirnos. En <strong>{{ $company['name'] }}</strong> valoramos tu confianza y trabajamos para ofrecerte productos y experiencias en equilibrio con la naturaleza. Lamentamos cualquier inconveniente y queremos asegurarte que ya estamos atendiendo tu caso.
                            </p>

                            <!-- Caja de resumen -->
                            <div style="background:#f1f7f1;border-left:4px solid #2e7d32;padding:16px 18px;border-radius:6px;margin:18px 0 22px;">
                                <p style="margin:0;font-size:13px;color:#555;">N° de {{ $claim->claim_type_label }}:</p>
                                <p style="margin:2px 0 0;font-size:20px;font-weight:700;color:#1b5e20;letter-spacing:.3px;">
                                    {{ $claim->claim_number }}
                                </p>
                                <p style="margin:8px 0 0;font-size:12px;color:#666;">
                                    Registrado el {{ $claim->created_at->format('d/m/Y') }} a las {{ $claim->created_at->format('H:i') }} hrs
                                </p>
                            </div>

                            <p style="margin:0 0 18px;font-size:14px;line-height:1.55;">
                                Nuestro equipo revisará la información con atención y te daremos respuesta en un plazo no mayor a <strong>quince (15) días hábiles</strong>, conforme lo establece el Código de Protección y Defensa del Consumidor.
                            </p>

                            <!-- Copia de los datos -->
                            <h3 style="font-size:15px;color:#2e7d32;margin:24px 0 10px;border-bottom:1px solid #e3ece3;padding-bottom:6px;">
                                Copia de la información registrada
                            </h3>

                            <p style="margin:14px 0 6px;font-size:13px;color:#888;text-transform:uppercase;letter-spacing:.5px;">Identificación del consumidor</p>
                            <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
                                <tr>
                                    <td style="width:38%;color:#666;">Nombre completo</td>
                                    <td>{{ $claim->full_name }}</td>
                                </tr>
                                <tr style="background:#fafafa;">
                                    <td style="color:#666;">Documento</td>
                                    <td>{{ $claim->consumer_document_type }}: {{ $claim->consumer_document_number }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#666;">Correo</td>
                                    <td>{{ $claim->consumer_email }}</td>
                                </tr>
                                <tr style="background:#fafafa;">
                                    <td style="color:#666;">Celular</td>
                                    <td>{{ $claim->consumer_phone }}</td>
                                </tr>
                            </table>

                            @if($claim->product_name || $claim->order_number || $claim->product_description)
                            <p style="margin:18px 0 6px;font-size:13px;color:#888;text-transform:uppercase;letter-spacing:.5px;">Bien contratado</p>
                            <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
                                @if($claim->product_name)
                                <tr>
                                    <td style="width:38%;color:#666;">Producto</td>
                                    <td>{{ $claim->product_name }}</td>
                                </tr>
                                @endif
                                @if($claim->order_number)
                                <tr style="background:#fafafa;">
                                    <td style="color:#666;">N° de pedido</td>
                                    <td>{{ $claim->order_number }}</td>
                                </tr>
                                @endif
                                @if($claim->product_description)
                                <tr>
                                    <td style="color:#666;vertical-align:top;">Descripción</td>
                                    <td style="white-space:pre-line;">{{ $claim->product_description }}</td>
                                </tr>
                                @endif
                            </table>
                            @endif

                            <p style="margin:18px 0 6px;font-size:13px;color:#888;text-transform:uppercase;letter-spacing:.5px;">Detalle</p>
                            <div style="background:#fafafa;border:1px solid #eee;border-radius:6px;padding:14px;font-size:14px;line-height:1.5;white-space:pre-line;margin-bottom:14px;">{{ $claim->claim_detail }}</div>

                            <p style="margin:18px 0 6px;font-size:13px;color:#888;text-transform:uppercase;letter-spacing:.5px;">Tu pedido</p>
                            <div style="background:#fafafa;border:1px solid #eee;border-radius:6px;padding:14px;font-size:14px;line-height:1.5;white-space:pre-line;">{{ $claim->consumer_request }}</div>

                            <!-- Nota legal -->
                            <div style="margin-top:26px;padding:14px 16px;background:#fff8e1;border:1px solid #ffe082;border-radius:6px;font-size:12px;color:#5d4037;line-height:1.5;">
                                La formulación de este {{ $claim->claim_type_label }} no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.
                            </div>

                            <p style="margin:24px 0 0;font-size:14px;line-height:1.55;">
                                Si tienes alguna duda adicional, puedes responder a este correo o escribirnos a <a href="mailto:branasac@gmail.com" style="color:#2e7d32;">branasac@gmail.com</a>.
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respuesta a tu reclamo - Brana</title>
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
                                Tenemos una respuesta a tu {{ $claim->claim_type_label }}.
                            </p>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:28px 32px;">

                            <p style="margin:0 0 14px;font-size:15px;line-height:1.55;">
                                Gracias por tu paciencia. Después de revisar con atención tu caso, queremos compartirte la respuesta de nuestro equipo.
                            </p>

                            <!-- Caja con número -->
                            <div style="background:#f1f7f1;border-left:4px solid #2e7d32;padding:14px 18px;border-radius:6px;margin:14px 0 22px;">
                                <p style="margin:0;font-size:12px;color:#555;">N° de {{ $claim->claim_type_label }}:</p>
                                <p style="margin:2px 0 0;font-size:18px;font-weight:700;color:#1b5e20;">
                                    {{ $claim->claim_number }}
                                </p>
                                <p style="margin:6px 0 0;font-size:12px;color:#666;">
                                    Estado actual: <strong style="color:#1b5e20;">{{ $claim->status_label }}</strong>
                                </p>
                            </div>

                            <!-- Respuesta -->
                            <h3 style="font-size:15px;color:#2e7d32;margin:0 0 10px;">Nuestra respuesta</h3>
                            <div style="background:#fafafa;border:1px solid #e6e6e6;border-radius:6px;padding:16px 18px;font-size:14px;line-height:1.6;white-space:pre-line;color:#333;">{{ $claim->admin_response }}</div>

                            @if($responder)
                                <p style="margin:14px 0 0;font-size:12px;color:#777;">
                                    Atendido por <strong>{{ $responder->name }}</strong>
                                    el {{ $claim->responded_at?->format('d/m/Y') }} a las {{ $claim->responded_at?->format('H:i') }} hrs.
                                </p>
                            @endif

                            <!-- Recordatorio del reclamo original -->
                            <h3 style="font-size:14px;color:#2e7d32;margin:28px 0 8px;border-top:1px solid #eee;padding-top:18px;">Tu mensaje original</h3>
                            <p style="margin:0 0 6px;font-size:12px;color:#888;text-transform:uppercase;letter-spacing:.5px;">Detalle</p>
                            <div style="background:#fafafa;border:1px solid #eee;border-radius:6px;padding:12px;font-size:13px;line-height:1.5;white-space:pre-line;margin-bottom:10px;">{{ $claim->claim_detail }}</div>

                            <p style="margin:0 0 6px;font-size:12px;color:#888;text-transform:uppercase;letter-spacing:.5px;">Pedido</p>
                            <div style="background:#fafafa;border:1px solid #eee;border-radius:6px;padding:12px;font-size:13px;line-height:1.5;white-space:pre-line;">{{ $claim->consumer_request }}</div>

                            <!-- Nota legal -->
                            <div style="margin-top:26px;padding:14px 16px;background:#fff8e1;border:1px solid #ffe082;border-radius:6px;font-size:12px;color:#5d4037;line-height:1.5;">
                                Si esta respuesta no resuelve tu caso, recuerda que puedes acudir a otras vías de solución de controversias o presentar una denuncia ante el INDECOPI.
                            </div>

                            <p style="margin:24px 0 0;font-size:14px;line-height:1.55;">
                                Si necesitas algo más, escríbenos a <a href="mailto:branasac@gmail.com" style="color:#2e7d32;">branasac@gmail.com</a>. Estamos aquí para ti.
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
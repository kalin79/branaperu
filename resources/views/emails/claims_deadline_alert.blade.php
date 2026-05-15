<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alerta de plazos - Libro de Reclamaciones</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f4;font-family:Arial,Helvetica,sans-serif;color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f4;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="680" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;">

                    <tr>
                        <td style="background:#b71c1c;padding:22px 28px;color:#fff;">
                            <h1 style="margin:0;font-size:20px;">⏰ Alerta de plazos — Libro de Reclamaciones</h1>
                            <p style="margin:6px 0 0;font-size:13px;opacity:.9;">
                                Revisión automática del {{ now()->format('d/m/Y H:i') }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 28px;">

                            @if($overdue->isNotEmpty())
                                <h2 style="color:#b71c1c;font-size:16px;margin:0 0 8px;border-bottom:2px solid #ffcdd2;padding-bottom:6px;">
                                    🔴 Reclamos VENCIDOS ({{ $overdue->count() }})
                                </h2>
                                <p style="font-size:13px;color:#666;margin:0 0 12px;">
                                    Estos reclamos ya superaron el plazo legal de 15 días hábiles.
                                </p>
                                <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:13px;margin-bottom:24px;">
                                    <thead>
                                        <tr style="background:#fdecea;">
                                            <th align="left">N°</th>
                                            <th align="left">Tipo</th>
                                            <th align="left">Consumidor</th>
                                            <th align="left">Registrado</th>
                                            <th align="center">Días vencido</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($overdue as $c)
                                            <tr style="border-bottom:1px solid #eee;">
                                                <td><strong>{{ $c->claim_number }}</strong></td>
                                                <td>{{ $c->claim_type_label }}</td>
                                                <td>{{ $c->full_name }}<br><small style="color:#777;">{{ $c->consumer_email }}</small></td>
                                                <td>{{ $c->created_at->format('d/m/Y') }}</td>
                                                <td align="center" style="color:#b71c1c;font-weight:700;">
                                                    {{ abs($c->business_days_remaining) }} día(s)
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            @if($approaching->isNotEmpty())
                                <h2 style="color:#e65100;font-size:16px;margin:0 0 8px;border-bottom:2px solid #ffe0b2;padding-bottom:6px;">
                                    🟡 Próximos a vencer ({{ $approaching->count() }})
                                </h2>
                                <p style="font-size:13px;color:#666;margin:0 0 12px;">
                                    Llevan 13 o 14 días hábiles abiertos. Hay que responder en máximo 1-2 días.
                                </p>
                                <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:13px;margin-bottom:18px;">
                                    <thead>
                                        <tr style="background:#fff3e0;">
                                            <th align="left">N°</th>
                                            <th align="left">Tipo</th>
                                            <th align="left">Consumidor</th>
                                            <th align="left">Registrado</th>
                                            <th align="center">Días restantes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approaching as $c)
                                            <tr style="border-bottom:1px solid #eee;">
                                                <td><strong>{{ $c->claim_number }}</strong></td>
                                                <td>{{ $c->claim_type_label }}</td>
                                                <td>{{ $c->full_name }}<br><small style="color:#777;">{{ $c->consumer_email }}</small></td>
                                                <td>{{ $c->created_at->format('d/m/Y') }}</td>
                                                <td align="center" style="color:#e65100;font-weight:700;">
                                                    {{ $c->business_days_remaining }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <div style="margin-top:18px;padding:14px 16px;background:#fff8e1;border:1px solid #ffe082;border-radius:6px;font-size:12px;color:#5d4037;line-height:1.5;">
                                <strong>Recordatorio legal:</strong> el Código de Protección y Defensa del Consumidor establece un plazo máximo de <strong>15 días hábiles</strong> para responder cada reclamo. El incumplimiento puede generar multas de INDECOPI.
                            </div>

                            <p style="margin:18px 0 0;font-size:13px;color:#555;">
                                Ingresa al panel administrativo para responderlos antes del vencimiento.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#fafafa;padding:14px 28px;font-size:11px;color:#888;text-align:center;">
                            {{ $company['name'] }} — Alerta automática del sistema
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
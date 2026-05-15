<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo mensaje de contacto - Brana</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f4;font-family:Arial,Helvetica,sans-serif;color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f4;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#2e7d32;padding:20px 24px;color:#fff;">
                            <h1 style="margin:0;font-size:20px;">Nuevo mensaje de contacto</h1>
                            <p style="margin:4px 0 0;font-size:13px;opacity:.85;">brana.pe — Formulario web</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 16px;font-size:14px;">
                                Has recibido una nueva consulta a través del formulario de la web.
                            </p>

                            <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
                                <tr>
                                    <td style="background:#f1f5f1;width:35%;font-weight:bold;">Nombre completo</td>
                                    <td>{{ $fullName }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#f1f5f1;font-weight:bold;">Correo</td>
                                    <td><a href="mailto:{{ $email }}" style="color:#2e7d32;">{{ $email }}</a></td>
                                </tr>
                                <tr>
                                    <td style="background:#f1f5f1;font-weight:bold;">Celular</td>
                                    <td>{{ $phone }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#f1f5f1;font-weight:bold;">Asunto</td>
                                    <td>{{ $subject }}</td>
                                </tr>
                            </table>

                            <h3 style="margin:24px 0 8px;font-size:15px;color:#2e7d32;">Mensaje</h3>
                            <div style="background:#fafafa;border:1px solid #eee;border-radius:6px;padding:16px;font-size:14px;line-height:1.5;white-space:pre-line;">{{ $body }}</div>

                            <p style="margin:24px 0 0;font-size:12px;color:#777;">
                                Puedes responder directamente a este correo y le llegará al cliente.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:14px 24px;font-size:12px;color:#888;text-align:center;">
                            © {{ date('Y') }} Brana. Mente &amp; Cuerpo en equilibrio natural.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
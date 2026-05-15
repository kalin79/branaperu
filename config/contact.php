<?php

/*
|--------------------------------------------------------------------------
| Configuración del formulario de contacto
|--------------------------------------------------------------------------
|
| Aquí defines a qué correos llegan los mensajes enviados desde la web.
| Los valores se leen desde el .env para que puedas cambiarlos sin tocar
| el código.
|
| CONTACT_MAIL_TO   → destinatario principal (uno solo)
| CONTACT_MAIL_CC   → copias visibles (puedes separar con coma)
| CONTACT_MAIL_BCC  → copias ocultas (puedes separar con coma)
|
| Ejemplo en .env:
|   CONTACT_MAIL_TO=branasac@gmail.com
|   CONTACT_MAIL_CC=ventas@brana.pe,marketing@brana.pe
|   CONTACT_MAIL_BCC=auditoria@brana.pe
|
*/

return [
    'to' => env('CONTACT_MAIL_TO', 'branasac@gmail.com'),

    'cc' => array_filter(array_map('trim', explode(',', (string) env('CONTACT_MAIL_CC', '')))),

    'bcc' => array_filter(array_map('trim', explode(',', (string) env('CONTACT_MAIL_BCC', '')))),
];
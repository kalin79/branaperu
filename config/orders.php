<?php

/*
|--------------------------------------------------------------------------
| Configuración de correos de Órdenes
|--------------------------------------------------------------------------
|
| Cuando un pago de MercadoPago es aprobado, se dispara automáticamente:
|   1. Correo al cliente con el detalle de su pedido.
|   2. Correo al admin (definido aquí) para que sepa que entró una venta.
|
| Variables disponibles en .env:
|   ORDERS_ADMIN_MAIL_TO   destinatario principal (uno)
|   ORDERS_ADMIN_MAIL_CC   copias (separar con coma)
|   ORDERS_ADMIN_MAIL_BCC  copias ocultas (separar con coma)
|
*/

return [
    'admin_to' => env('ORDERS_ADMIN_MAIL_TO', env('CONTACT_MAIL_TO', 'branasac@gmail.com')),
    'admin_cc' => array_filter(array_map('trim', explode(',', (string) env('ORDERS_ADMIN_MAIL_CC', '')))),
    'admin_bcc' => array_filter(array_map('trim', explode(',', (string) env('ORDERS_ADMIN_MAIL_BCC', '')))),
];
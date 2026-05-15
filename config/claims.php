<?php

/*
|--------------------------------------------------------------------------
| Configuración del Libro de Reclamaciones
|--------------------------------------------------------------------------
|
| CLAIMS_MAIL_TO    → destinatario interno (admin) que recibe la notificación
|                     de cada nuevo reclamo
| CLAIMS_MAIL_CC    → copia visible (separar con comas)
| CLAIMS_MAIL_BCC   → copia oculta (separar con comas)
|
| Datos de la empresa (aparecen en correo y en formulario):
| COMPANY_NAME, COMPANY_RUC, COMPANY_ADDRESS
|
*/

return [
    'to' => env('CLAIMS_MAIL_TO', env('CONTACT_MAIL_TO', 'branasac@gmail.com')),
    'cc' => array_filter(array_map('trim', explode(',', (string) env('CLAIMS_MAIL_CC', '')))),
    'bcc' => array_filter(array_map('trim', explode(',', (string) env('CLAIMS_MAIL_BCC', '')))),

    'company' => [
        'name' => env('COMPANY_NAME', 'BRANA PERU SAC'),
        'ruc' => env('COMPANY_RUC', '20603365837'),
        'address' => env('COMPANY_ADDRESS', 'Av. Arnaldo Márquez 1082, Jesús María, Lima'),
    ],
];
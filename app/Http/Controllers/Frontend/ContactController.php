<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactoRecibido;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Mostrar la página de contacto.
     */
    public function index()
    {
        return Inertia::render('Contact', [
            'title_meta' => 'Contáctenos',
            'description_meta' => 'Si deseas más información sobre nuestros productos, resolver una consulta o coordinar una visita, escríbenos.',
        ]);
    }

    /**
     * Procesar el envío del formulario de contacto.
     */
    public function send(ContactRequest $request)
    {
        $data = $request->validated();

        try {
            $to = config('contact.to');
            $cc = array_filter(config('contact.cc', []));
            $bcc = array_filter(config('contact.bcc', []));

            $mail = Mail::to($to);

            if (!empty($cc)) {
                $mail->cc($cc);
            }
            if (!empty($bcc)) {
                $mail->bcc($bcc);
            }

            $mail->send(new ContactoRecibido($data));
        } catch (\Throwable $e) {
            Log::error('Error enviando correo de contacto: ' . $e->getMessage(), [
                'data' => $data,
            ]);

            return back()->withErrors([
                'general' => 'Ocurrió un error enviando tu mensaje. Por favor inténtalo más tarde.',
            ]);
        }

        return back()->with('success', '¡Mensaje enviado! Te responderemos a la brevedad.');
    }
}
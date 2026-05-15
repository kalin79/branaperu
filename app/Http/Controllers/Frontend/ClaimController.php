<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimRequest;
use App\Mail\ClaimReceivedToAdmin;
use App\Mail\ClaimReceivedToCustomer;
use App\Models\Claim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ClaimController extends Controller
{
    /**
     * Mostrar la página del libro de reclamaciones.
     */
    public function index()
    {
        return Inertia::render('Claims/Index', [
            'title_meta' => 'Libro de Reclamaciones',
            'description_meta' => 'En BRANA valoramos tu bienestar y confianza. Registra tu reclamo, queja o sugerencia.',
            'company' => config('claims.company'),
            'document_types' => Claim::getDocumentTypeOptions(),
            'claim_types' => Claim::getTypeOptions(),
        ]);
    }

    /**
     * Registrar un nuevo reclamo / queja.
     */
    public function store(ClaimRequest $request)
    {
        $data = $request->validated();
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr((string) $request->userAgent(), 0, 500);

        try {
            $claim = DB::transaction(function () use ($data) {
                return Claim::create($data);
            });
        } catch (\Throwable $e) {
            Log::error('Error guardando reclamo: ' . $e->getMessage(), ['data' => $data]);

            return back()->withErrors([
                'general' => 'Ocurrió un error al registrar tu reclamo. Por favor inténtalo nuevamente.',
            ])->withInput();
        }

        // Enviar correos (no bloquea el flujo si SMTP falla)
        $this->sendCustomerEmail($claim);
        $this->sendAdminEmail($claim);

        return back()->with('success', "¡Reclamo registrado! Tu número de reclamo es {$claim->claim_number}. Hemos enviado una copia a tu correo.")
            ->with('claim_number', $claim->claim_number);
    }

    protected function sendCustomerEmail(Claim $claim): void
    {
        try {
            Mail::to($claim->consumer_email)->send(new ClaimReceivedToCustomer($claim));
        } catch (\Throwable $e) {
            Log::error('Error enviando correo al cliente (reclamo ' . $claim->claim_number . '): ' . $e->getMessage());
        }
    }

    protected function sendAdminEmail(Claim $claim): void
    {
        try {
            $to = config('claims.to');
            $cc = array_filter(config('claims.cc', []));
            $bcc = array_filter(config('claims.bcc', []));

            $mail = Mail::to($to);
            if (!empty($cc)) {
                $mail->cc($cc);
            }
            if (!empty($bcc)) {
                $mail->bcc($bcc);
            }

            $mail->send(new ClaimReceivedToAdmin($claim));
        } catch (\Throwable $e) {
            Log::error('Error enviando correo al admin (reclamo ' . $claim->claim_number . '): ' . $e->getMessage());
        }
    }
}
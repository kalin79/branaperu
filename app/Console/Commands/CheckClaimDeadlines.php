<?php

namespace App\Console\Commands;

use App\Mail\ClaimsDeadlineAlert;
use App\Models\Claim;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckClaimDeadlines extends Command
{
    protected $signature = 'claims:check-deadlines';

    protected $description = 'Revisa reclamos próximos a vencer (13+ días hábiles) o vencidos y notifica al admin.';

    public function handle(): int
    {
        $openClaims = Claim::query()->open()->orderBy('created_at')->get();

        $approaching = $openClaims->filter(fn(Claim $c) => $c->is_approaching_deadline)->values();
        $overdue = $openClaims->filter(fn(Claim $c) => $c->is_overdue)->values();

        $totalAlert = $approaching->count() + $overdue->count();

        $this->info("Reclamos abiertos: " . $openClaims->count());
        $this->info("Próximos a vencer (≥{$this->warningDays()} días hábiles): {$approaching->count()}");
        $this->info("Vencidos: {$overdue->count()}");

        if ($totalAlert === 0) {
            $this->info('No hay reclamos que requieran alerta. Saliendo.');
            return self::SUCCESS;
        }

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

            $mail->send(new ClaimsDeadlineAlert($approaching, $overdue));

            $this->info("Alerta enviada a {$to}.");
        } catch (\Throwable $e) {
            Log::error('Error enviando alerta de plazos de reclamos: ' . $e->getMessage());
            $this->error('Falló el envío. Revisa storage/logs/laravel.log');
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function warningDays(): int
    {
        return Claim::WARNING_DEADLINE_DAYS;
    }
}
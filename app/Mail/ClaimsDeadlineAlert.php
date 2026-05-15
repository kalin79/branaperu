<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ClaimsDeadlineAlert extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $approaching;
    public Collection $overdue;

    public function __construct(Collection $approaching, Collection $overdue)
    {
        $this->approaching = $approaching;
        $this->overdue = $overdue;
    }

    public function envelope(): Envelope
    {
        $total = $this->approaching->count() + $this->overdue->count();
        $vencidos = $this->overdue->count();

        $subject = $vencidos > 0
            ? "[Reclamos] {$vencidos} VENCIDO(S) — {$total} caso(s) requieren atención"
            : "[Reclamos] {$total} reclamo(s) próximos a vencer";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.claims_deadline_alert',
            with: [
                'approaching' => $this->approaching,
                'overdue' => $this->overdue,
                'company' => config('claims.company'),
            ],
        );
    }
}
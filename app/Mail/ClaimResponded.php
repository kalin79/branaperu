<?php

namespace App\Mail;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimResponded extends Mailable
{
    use Queueable, SerializesModels;

    public Claim $claim;

    public function __construct(Claim $claim)
    {
        $this->claim = $claim;
    }

    public function envelope(): Envelope
    {
        $tipo = $this->claim->claim_type === Claim::TYPE_QUEJA ? 'queja' : 'reclamo';

        return new Envelope(
            subject: "Respuesta a tu {$tipo} N° {$this->claim->claim_number} — Brana",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.claim_responded',
            with: [
                'claim' => $this->claim,
                'responder' => $this->claim->respondedBy,
                'company' => config('claims.company'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
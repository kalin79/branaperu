<?php

namespace App\Mail;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimReceivedToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public Claim $claim;

    public function __construct(Claim $claim)
    {
        $this->claim = $claim;
    }

    public function envelope(): Envelope
    {
        $tipo = $this->claim->claim_type === Claim::TYPE_QUEJA ? 'Queja' : 'Reclamo';

        return new Envelope(
            subject: "[Libro de Reclamaciones] Nuevo {$tipo} — {$this->claim->claim_number}",
            replyTo: [
                new Address($this->claim->consumer_email, $this->claim->full_name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.claim_admin',
            with: [
                'claim' => $this->claim,
                'company' => config('claims.company'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
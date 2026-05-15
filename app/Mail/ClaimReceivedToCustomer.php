<?php

namespace App\Mail;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimReceivedToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public Claim $claim;

    public function __construct(Claim $claim)
    {
        $this->claim = $claim;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hemos recibido tu ' . ($this->claim->claim_type === Claim::TYPE_QUEJA ? 'queja' : 'reclamo') . ' — N° ' . $this->claim->claim_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.claim_customer',
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
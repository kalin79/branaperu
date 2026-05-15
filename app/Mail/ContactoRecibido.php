<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoRecibido extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    /**
     * @param  array  $data  ['full_name','email','phone','subject','message']
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Brana - Contacto] ' . ($this->data['subject'] ?? 'Nueva consulta'),
            // Para poder responder directo al cliente desde Gmail/Outlook
            replyTo: [
                new Address($this->data['email'], $this->data['full_name']),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contacto',
            with: [
                'fullName' => $this->data['full_name'],
                'email' => $this->data['email'],
                'phone' => $this->data['phone'],
                'subject' => $this->data['subject'],
                'body' => $this->data['message'],
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
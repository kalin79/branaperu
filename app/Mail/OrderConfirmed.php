<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pedido confirmado — {$this->order->order_number} | Brana",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_confirmed',
            with: [
                'order' => $this->order,
                'items' => $this->order->items,
                'company' => config('claims.company'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
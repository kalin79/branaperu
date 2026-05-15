<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmedToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        $total = number_format((float) $this->order->final_total, 2);

        return new Envelope(
            subject: "[Nueva venta] {$this->order->order_number} — S/ {$total}",
            replyTo: array_filter([
                $this->order->customer_email
                ? new Address($this->order->customer_email, $this->order->customer_name)
                : null,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_confirmed_admin',
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
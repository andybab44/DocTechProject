<?php

namespace App\Mail;

use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReorderRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Vendor  $vendor
     * @param  array<array{item: \App\Models\InventoryItem, quantity: int}>  $lines
     * @param  string  $reference  PO reference number
     */
    public function __construct(
        public readonly Vendor $vendor,
        public readonly array  $lines,
        public readonly string $reference,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reorder Request {$this->reference} — {$this->vendor->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.reorder-request',
        );
    }
}

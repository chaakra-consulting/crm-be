<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadOfferEmail extends Mailable
{
   use Queueable, SerializesModels;

    /**
     * The data array containing all rich text and variables for the email.
     *
     * @var array
     */
    public $offerData;

    /**
     * Create a new message instance.
     *
     * @param array $offerData
     */
    public function __construct(array $offerData)
    {
        $this->offerData = $offerData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->offerData['emailSubject'] ?? 'New Offers from ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // This points to resources/views/emails/offer-email.blade.php
            view: 'emails.offers', 
            with: $this->offerData,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

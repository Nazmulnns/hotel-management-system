<?php

namespace App\Mail;

use App\Models\SupportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SupportRequestReceived extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public SupportRequest $supportRequest)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('We received your request: :issue', [
                'issue' => Str::limit($this->supportRequest->issue, 40),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.support-requests.received',
            with: [
                'roomLabel' => "{$this->supportRequest->room->floor}{$this->supportRequest->room->room_number}",
            ]
        );
    }
}
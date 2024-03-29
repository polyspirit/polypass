<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Send2FALinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    private string $code;

    public function __construct(string $subject, string $code)
    {
        $this->subject = $subject;
        $this->code = $code;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
            from: new Address(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.2fa',
            with: [
                'token' => $this->code
            ]
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

    public function build()
    {
        return $this->subject('Mail from ' . env('APP_NAME'))
            ->view('email.2fa')
            ->with('code', $this->code);
    }
}

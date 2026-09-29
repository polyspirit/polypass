<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Send2FACodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private string $code)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('signin.2fa_code_subject') . ' — ' . config('app.name'),
            from: new Address(config('mail.from.address'), config('app.name'))
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.2fa-code',
            with: ['code' => $this->code]
        );
    }
}

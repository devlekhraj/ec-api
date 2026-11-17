<?php

declare(strict_types=1);

namespace Admin\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $otp,
        public readonly string $recipientName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Password Reset OTP',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin::emails.otp',
            with: [
                'otp'           => $this->otp,
                'recipientName' => $this->recipientName,
                'expiresInMin'  => 10,
            ],
        );
    }
}

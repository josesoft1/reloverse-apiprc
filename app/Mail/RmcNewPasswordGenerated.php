<?php

namespace App\Mail;

use App\Models\Rmc;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RmcNewPasswordGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public $rmc;
    public $new_password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Rmc $rmc, $new_password)
    {
        $this->rmc = $rmc;
        $this->new_password = $new_password;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Your new PRC password',
            replyTo: [auth('api')->user()->email]
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mail.rmc-new-password-generated',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\DocumentRequest;

class NewDocRequiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dr;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(DocumentRequest $dr)
    {
        $this->dr = $dr;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New document required')->markdown('mail.new-doc-required-mail');
    }
}

<?php

namespace App\Mail;

use App\Models\Relocation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewRelocationJob extends Mailable
{
    use Queueable, SerializesModels;

    public $relocation;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Relocation $relocation)
    {
        $this->relocation = $relocation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New relocation JOB created')->markdown('mail.new-relocation-job');
    }
}

<?php

namespace App\Mail;

use App\Models\RealEstateProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewRealEstateProposal extends Mailable
{
    use Queueable, SerializesModels;
    
    public RealEstateProposal $proposal;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RealEstateProposal $proposal)
    {
        $this->proposal = $proposal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("New real estate proposal")->markdown('mail.new-real-estate-proposal');
    }
}

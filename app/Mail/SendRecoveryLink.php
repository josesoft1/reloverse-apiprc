<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class SendRecoveryLink extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $recovery_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;  
        $recovery_object = new \stdClass();
        $recovery_object->old = $user->password;
        $recovery_object->due = now()->addHours(2);
        $recovery_object->email = $user->email;
        $recovery_object_encoded = Crypt::encryptString(json_encode($recovery_object));
        $this->recovery_link = env('BO_FRONTEND_URL',"https://prcbo.mediacrm.it")."/auth/recovery_from_email?data=".$recovery_object_encoded;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Password reset link")->markdown('mail.send-recovery-link');
    }
}

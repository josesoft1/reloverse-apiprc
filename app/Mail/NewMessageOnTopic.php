<?php

namespace App\Mail;

use App\Models\ReloMessage;
use App\Models\File;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Log;

class NewMessageOnTopic extends Mailable
{
    use Queueable, SerializesModels;

    public ReloMessage $message;
    public $files;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ReloMessage $message, $files)
    {
        $this->message = $message;
        $this->files = $files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("New message on topic: {$this->message->topic->topic}")->markdown('mail.new-message-on-topic');
    }

    public function attachments()
    {
        $attachments = [];
        $this->message->attachments = [];
        $set_attachments = [];
        foreach($this->files as $template_file){
            $file = File::findOrFail($template_file->_id);
            try{
                $attachments[] = Attachment::fromStorageDisk('s3', $file->path)
                ->as($file->name)
                ->withMime($file->mime);
                $set_attachments[] = (object)['name'=>$file->name, 'mime' => $file->mime, '_id' => $file->_id]; 
            }catch(Exception $e){
                Log::error($e);
                throw $e;
            }
            $this->message->attachments = $set_attachments;
            $this->message->save();
        }
        return $attachments;
    }
}

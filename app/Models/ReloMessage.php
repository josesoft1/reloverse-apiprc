<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class ReloMessage extends Model
{
    use SoftDeletes;

    public $appends = ['author'];

    public function relocation(){
        return $this->belongsTo(Relocation::class);
    }

    public function topic(){
        return $this->belongsTo(ReloMessageTopic::class, "relo_message_topic_id");
    }


    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }


    public function getAuthorAttribute(){
        if(!empty($this->user_id)){
            return $this->user->full_name;
        }
        if(!empty($this->employee_id)){
            return $this->employee->full_name;
        }
    }

    public function generateReplyUrl(){
        $url = 'https://prcbo.mediacrm.it/public/view_topic/'.$this->topic->_id.'?signature='.Crypt::encryptString($this->topic->_id);
        return $url;
    }
}

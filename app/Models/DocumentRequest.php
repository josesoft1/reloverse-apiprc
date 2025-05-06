<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Relations\BelongsTo;

class DocumentRequest extends Model
{
    protected $casts = ['completed_at' => 'datetime'];
    
    public function relocation(): BelongsTo { 
        return $this->belongsTo(Relocation::class);
    }


    public function generateUrl(){
        $signature = hash_hmac('sha256', $this->_id, config('app.key'));
        $url = config('app.bo_frontend_url').'/public/document_request/'.$this->_id.'?signature='.$signature;
        return $url;
    }
}

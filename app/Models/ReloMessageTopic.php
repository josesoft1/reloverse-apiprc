<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class ReloMessageTopic extends Model
{
    use HasFactory;

    protected $fillable = ['topic'];

    protected $casts = [
        'closed_at' => 'datetime',
        'waiting' => 'boolean'
    ];

    public function messages(){
        return $this->hasMany(ReloMessage::class);
    }

    public function relocation(){
        return $this->belongsTo(Relocation::class);
    }
}

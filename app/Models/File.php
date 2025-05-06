<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Model;

class File extends Model
{
    use HasFactory;

    protected $casts = ['status' => 'boolean'];
    
    public function relocation(){
        return $this->belongsTo(Relocation::class);
    }
}

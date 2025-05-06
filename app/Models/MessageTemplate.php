<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class MessageTemplate extends Model
{
    use SoftDeletes;

    public function files(){
        return $this->hasMany(File::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Rmc extends Model
{
    use HasFactory;

    public function holdings(){
        return $this->hasMany(Company::class);
    }
}

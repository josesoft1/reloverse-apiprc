<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Consultant extends Model
{
    use SoftDeletes;

    public $appends = ['full_name'];

    
    public function getFullNameAttribute(){
        return "{$this->surname} {$this->name}";
    }
}

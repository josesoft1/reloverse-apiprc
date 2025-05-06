<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ServiceCategory extends Model
{

    protected $fillable = ['name', 'price', 'plannable', 'description', 'status'];
    
}

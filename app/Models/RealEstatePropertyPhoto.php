<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class RealEstatePropertyPhoto extends Model
{
    public function property(){
        return $this->belongsTo(RealEstateProperty::class);
    }
}

<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class RealEstateProperty extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'negotation','geocoded_address','geocoded_address.place_id','price','price','other_price','typology','n_bath','n_bed','n_balcony','n_garage_slots', 'square_meters'];
    protected $casts = ['price'=>'double','other_price'=>'double'];

    public function photos(){
        return $this->hasMany(RealEstatePropertyPhoto::class);
    }

    public function proposal(){
        return $this->hasMany(RealEstateProposal::class);
    }
}

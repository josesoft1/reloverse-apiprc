<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\HasMany;

class RealEstateProposal extends Model
{
    protected $casts = ['rating_feedback'=>'integer'];

    public function relocation(): BelongsTo{
        return $this->belongsTo(Relocation::class);
    }

    public function proposer(): BelongsTo{
        return $this->belongsTo(User::class,'proposer_id');
    }

    public function property(){
        return $this->belongsTo(RealEstateProperty::class, 'real_estate_property_id');
    }
}

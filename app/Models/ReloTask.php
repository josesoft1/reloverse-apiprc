<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class ReloTask extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];

    protected $casts = [
        'date'=>'datetime',
        'planned_by_authority' => 'boolean',
        'confirmed_by_customer' => 'boolean',
        'unnecessary' => 'boolean',
    ];

    public function service(){
        return $this->belongsTo(ReloService::class);
    }

    public function consultant(){
        return $this->belongsTo(Consultant::class);
    }
}

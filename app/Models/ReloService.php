<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class ReloService extends Model
{
    use SoftDeletes;

    protected $casts = [
        'price' => 'double',
        'date' => 'datetime'
    ];

    protected $fillable = ['name', 'price', 'plannable', 'description', 'status'];

    public function relocation(){
        return $this->belongsTo(Relocation::class);
    }

    public function tasks(){
        return $this->hasMany(ReloTask::class,'service_id');
    }
}

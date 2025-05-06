<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $casts = [
        'price' => 'double',
        'date' => 'datetime'
    ];

    protected $fillable = ['name', 'price', 'plannable', 'description', 'status','tasks'];
}

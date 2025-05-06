<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'vat', 'nin', 'address1', 'address2', 'city', 'province','state'];

    public function employees(){
        return $this->hasMany(Employee::class);
    }

    public function rmc(){
        return $this->belongsTo(Rmc::class);
    }
}

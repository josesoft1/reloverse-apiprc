<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Paf extends Model
{
    use HasFactory;
    /*
    protected $fillable = [
        'tenant_name'
    ];
    */

    protected $guarded = [];

    /*
    protected $casts = [
        'date_of_birth' => 'date',
        'vip' => 'boolean',
    ];

    protected $appends = ['full_name'];

    protected $hidden = ['password'];

    public function files()
	{
		return $this->hasMany(File::class);
	}

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function getFullNameAttribute(){
        return trim("{$this->surname} {$this->name}");
    }

    public function messages(){
        return $this->hasMany(ReloMessage::class,'employee_id');
    }
    */
}

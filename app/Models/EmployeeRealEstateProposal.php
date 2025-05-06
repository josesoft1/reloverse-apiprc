<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Model;

class EmployeeRealEstateProposal extends Model
{
    use HasFactory;
    

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}

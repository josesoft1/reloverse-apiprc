<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\HasMany;

class Relocation extends Model
{
    use HasFactory;

    public const STATUS_CREATED = 0;
    public const STATUS_WORKING = 1;
    public const STATUS_COMPLETED = 2;
    public const STATUS_DELETED = 3;
    
    protected $casts = [
        'na.arrival.preparation' => 'date'
    ];
    
    public function employee(): BelongsTo{
        return $this->belongsTo(Employee::class);
    }
    
    public function company(): BelongsTo{
        return $this->belongsTo(Company::class);
    }
    
    public function services(): HasMany{
        return $this->hasMany(ReloService::class);
    }
    public function files() : HasMany{
        return $this->hasMany(File::class);
    }
    
    public function documentRequests() : HasMany{
        return $this->hasMany(DocumentRequest::class, 'relocation_id', '_id');
    }
    
    public function topics() : HasMany{
        return $this->hasMany(ReloMessageTopic::class);
    }

    public function realEstateProposals(): HasMany{
        return $this->hasMany(RealEstateProposal::class);
    }

    public function responsible(){
        return $this->belongsTo(User::class);
    }
}

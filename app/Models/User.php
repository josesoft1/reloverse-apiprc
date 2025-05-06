<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{ 
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 0;
    const ROLE_EMPLOYEE = 1;
    const ROLE_CONS = 2;
    const ROLE_RMC = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attribute that should be append on every models
     */
    protected $appends = [
        'full_name',
        'tawk_secret',
        'employee_email',
        'rmc_email'
    ];

    public function getFullNameAttribute(){
        return "{$this->name} {$this->surname}";
    }

    public function messages(): HasMany{
        return $this->hasMany(ReloMessage::class,'user_id','_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function employee(): BelongsTo{
       return $this->belongsTo(Employee::class);
    }
    
    public function rmc(): BelongsTo{
       return $this->belongsTo(Rmc::class);
    }

    /**
     * Compute the hash for tawk integration
     *
     * @return String|null
     */
    public function getTawkSecretAttribute(){
        if(empty($this->employee_id)){
            return null;
        }else{

        }
        return hash_hmac('sha256',$this->employee->email,'523d80263386b6b0549d4088f295329694d39444');
    }

    public function getEmployeeEmailAttribute(){
        if(empty($this->employee_id)){
            return null;
        }else{

        }
        return $this->employee->email;
    }
    
    public function getRmcEmailAttribute(){
        if(empty($this->rmc_id)){
            return null;
        }else{

        }
        return $this->rmc->email;
    }
}

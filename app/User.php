<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'userid', 'user_type', 'ref_id', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function vendor(){
        return $this->hasOne(Vendor::class, 'vendor_code', 'userid');
    }
    public function buyer(){
        return $this->hasOne(Buyer::class, 'user_id', 'userid');
    }
    public function isVendor(){
        // return $this->vendor != null;
        return $this->user_type == 'vendor' && !is_null($this->ref_id);
    }
    public function extension(){
        return $this->hasOne(UserExtensions::class, 'user_id', 'id');
    }
}

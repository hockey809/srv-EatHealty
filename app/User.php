<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    
    public function data()
    {
        return $this->hasOne('App\Models\CustomerData', 'user_id');
    }
    
    const TYPE_CUSTOMER = 2; // stored in user_types table
    const API_CLIENT = 3;   // stored in user_types table 
    
    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'users';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'confirmed', 'username'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
}

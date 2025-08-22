<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'latitude', 'longitude', 'location', 'is_admin'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_admin' => 'boolean',
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    public function setPasswordAttribute($val)
    {
        $this->attributes['password'] = bcrypt($val);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function getJWTIdentifier() { return $this->getKey(); }

    public function getJWTCustomClaims() { return []; }
}

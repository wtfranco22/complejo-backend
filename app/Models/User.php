<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'name',
        'lastname',
        'dni',
        'email',
        'phone'
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

    protected function name(): Attribute
    {
        return new Attribute(
            /**
             * the attribute is set to all lowercase
             */
            set: fn ($value) => strtolower($value),
            /**
             * convert the first character of each word in a string to uppercase
             */
            get: function ($value) {
                return ucwords($value);
            }
        );
    }

    protected function lastname(): Attribute
    {
        return new Attribute(
            /**
             * the attribute is set to all lowercase
             */
            set: fn ($value) => strtolower($value),
            /**
             * convert the first character of each word in a string to uppercase
             */
            get: function ($value) {
                return ucwords($value);
            }
        );
    }

    /**
     * get the related object
     */
    public function account(): HasOne
    {
        return $this->hasOne(Account::class);
    }

    /**
     * get the related object
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}

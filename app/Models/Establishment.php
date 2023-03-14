<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Establishment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'phone',
        'image_url'
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

    protected function location(): Attribute
    {
        return new Attribute(
            /**
             * the attribute is set to all lowercase
             */
            set: fn ($value) => strtolower($value)
        );
    }

    /**
     * get the related object
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * get the related object
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    /**
     * get the related object
     */
    public function courts(): HasMany
    {
        return $this->hasMany(Court::class);
    }
}

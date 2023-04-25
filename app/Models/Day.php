<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'active',
        'name',
        'description',
        'modified_at'
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

    protected function description(): Attribute
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
    public function hours(): BelongsToMany
    {
        return $this->belongsToMany(Hour::class, 'day_hour')->using(DayHour::class);
    }

    /**
     * get the related object
     */
    public function shifts(): HasManyThrough
    {
        return $this->hasManyThrough(Shift::class, DayHour::class);
    }
}

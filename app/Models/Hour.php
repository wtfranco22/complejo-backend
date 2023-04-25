<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Hour extends Model
{
    use HasFactory;

    protected $fillable = [
        'active',
        'hour',
        'description',
        'modified_at'
    ];

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
    public function days(): BelongsToMany
    {
        return $this->belongsToMany(Day::class, 'day_hour')->using(DayHour::class);
    }

    /**
     * get the related object
     */
    public function shifts(): HasManyThrough
    {
        return $this->hasManyThrough(Shift::class, DayHour::class);
    }
}

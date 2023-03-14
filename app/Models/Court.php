<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'description',
        'colour',
        'created_at'
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

    protected function colour(): Attribute
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
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    /**
     * get the related object
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }
}

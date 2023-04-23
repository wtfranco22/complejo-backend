<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * get the related object
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * get the related object
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }
}

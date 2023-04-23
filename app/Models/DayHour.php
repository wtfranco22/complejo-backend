<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DayHour extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'day_id',
        'hour_id'
    ];

    /**
     * get the related object
     */
    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    /**
     * get the related object
     */
    public function hour(): BelongsTo
    {
        return $this->belongsTo(Hour::class);
    }


    /**
     * get the related object
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }
}

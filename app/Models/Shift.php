<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'active',
        'modified_at',
    ];

    /**
     * get the related object
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
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
    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    /**
     * get the related object
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}

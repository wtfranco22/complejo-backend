<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'available',
        'price',
        'modified_at'
    ];

    /**
     * get the related object
     */
    public function dayHour(): BelongsTo
    {
        return $this->belongsTo(DayHour::class);
    }

    /**
     * get the related object
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * get the related object
     */
    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }
}

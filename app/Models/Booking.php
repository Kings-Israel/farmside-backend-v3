<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'event_details' => 'array',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'forfeited_at' => 'datetime',
    ];

    protected $appends = [
        'status',
    ];

    public function getStatusAttribute(): string
    {
        if ($this->forfeited_at) {
            return 'forfeited';
        }

        if ($this->completed_at) {
            return 'completed';
        }

        if ($this->confirmed_at) {
            return 'confirmed';
        }

        return 'pending';
    }
}

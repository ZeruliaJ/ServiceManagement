<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnerMapping extends Model
{
    protected $fillable = [
        'vehicle_id', 'party_id', 'ownership_type', 'ownership_start_date',
        'ownership_end_date', 'is_current', 'notes'
    ];

    protected $casts = [
        'ownership_start_date' => 'datetime',
        'ownership_end_date' => 'datetime',
        'is_current' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}

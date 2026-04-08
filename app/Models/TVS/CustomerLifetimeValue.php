<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerLifetimeValue extends Model
{
    protected $fillable = [
        'party_id', 'total_vehicles_serviced', 'lifetime_value', 'total_job_cards',
        'first_service_date', 'last_service_date', 'repeat_visits', 'average_visit_value'
    ];

    protected $casts = [
        'lifetime_value' => 'decimal:2',
        'average_visit_value' => 'decimal:2',
        'first_service_date' => 'datetime',
        'last_service_date' => 'datetime',
    ];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}

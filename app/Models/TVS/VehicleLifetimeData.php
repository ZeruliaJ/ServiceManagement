<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleLifetimeData extends Model
{
    protected $fillable = [
        'vehicle_id', 'total_service_visits', 'total_service_cost',
        'total_parts_cost', 'total_labour_cost', 'total_warranty_claims',
        'first_service_date', 'last_service_date', 'average_service_interval_days'
    ];

    protected $casts = [
        'total_service_cost' => 'decimal:2',
        'total_parts_cost' => 'decimal:2',
        'total_labour_cost' => 'decimal:2',
        'total_warranty_claims' => 'decimal:2',
        'first_service_date' => 'datetime',
        'last_service_date' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}

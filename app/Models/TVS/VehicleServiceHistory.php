<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleServiceHistory extends Model
{
    protected $table = 'vehicle_history'; 

    protected $fillable = ['vehicle_id', 'service_number', 'service_date', 'branch_code', 'odometer_reading', 'remarks'];

    protected $casts = [
        'service_date' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}

<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabourOperation extends Model
{
    protected $fillable = [
        'operation_code', 'operation_name', 'description', 'standard_labor_rate',
        'standard_hours', 'vehicle_type_id', 'is_active'
    ];

    protected $casts = [
        'standard_labor_rate' => 'decimal:2',
        'standard_hours' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function jobCardLabour(): HasMany
    {
        return $this->hasMany(JobCardLabour::class, 'operation_id');
    }
}

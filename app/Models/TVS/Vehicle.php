<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'registration_no', 'chassis_no', 'engine_no', 'vehicle_variant_id',
        'vehicle_type_id', 'color', 'sale_type_id', 'sale_date',
        'source_system', 'is_provisional', 'is_validated', 'validation_date',
        'validation_notes'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'validation_date' => 'datetime',
        'is_provisional' => 'boolean',
        'is_validated' => 'boolean',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(VehicleVariant::class, 'vehicle_variant_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function saleType(): BelongsTo
    {
        return $this->belongsTo(SaleType::class);
    }

    public function ownerMappings(): HasMany
    {
        return $this->hasMany(OwnerMapping::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(VehicleRegistration::class);
    }

    public function serviceHistory(): HasMany
    {
        return $this->hasMany(VehicleServiceHistory::class);
    }

    public function warranties(): HasMany
    {
        return $this->hasMany(Warranty::class);
    }

    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class);
    }

    public function gatePasses(): HasMany
    {
        return $this->hasMany(GatePass::class);
    }

    public function lifetimeData(): HasMany
    {
        return $this->hasMany(VehicleLifetimeData::class);
    }

    public function repeatRepairs(): HasMany
    {
        return $this->hasMany(RepeatRepairAnalysis::class);
    }

    public function getCurrentOwner()
    {
        return $this->ownerMappings()
            ->where('is_current', true)
            ->latest()
            ->first();
    }

    public function getActiveWarranty()
    {
        return $this->warranties()
            ->whereHas('warrantyStatus', function($q) {
                $q->where('code', 'Active');
            })
            ->latest()
            ->first();
    }
}

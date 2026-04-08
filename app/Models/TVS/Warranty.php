<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warranty extends Model
{
    protected $fillable = [
        'vehicle_id', 'warranty_start_date', 'warranty_end_date', 'warranty_status_id',
        'kilometers_limit', 'coverage_type', 'terms_conditions', 'source_system'
    ];

    protected $casts = [
        'warranty_start_date' => 'datetime',
        'warranty_end_date' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function warrantyStatus(): BelongsTo
    {
        return $this->belongsTo(WarrantyStatus::class);
    }

    public function validations(): HasMany
    {
        return $this->hasMany(WarrantyValidation::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    public function isActive(): bool
    {
        return $this->warranty_status_id === WarrantyStatus::where('code', 'Active')->first()?->id;
    }

    public function isExpired(): bool
    {
        return now() > $this->warranty_end_date;
    }
}

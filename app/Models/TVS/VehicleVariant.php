<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleVariant extends Model
{
    protected $fillable = ['code', 'model_name', 'vehicle_type_id', 'variant_name', 'description'];

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function freeServiceCoupons(): HasMany
    {
        return $this->hasMany(FreeServiceCoupon::class);
    }
}

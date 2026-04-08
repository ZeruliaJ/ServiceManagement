<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends Model
{
    protected $fillable = ['code', 'name'];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(VehicleVariant::class);
    }

    public function labourOperations(): HasMany
    {
        return $this->hasMany(LabourOperation::class);
    }
}

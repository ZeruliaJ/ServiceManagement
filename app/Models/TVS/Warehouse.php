<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = ['code', 'name', 'location', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function partStock(): HasMany
    {
        return $this->hasMany(PartStock::class);
    }

    public function partReservations(): HasMany
    {
        return $this->hasMany(PartReservation::class);
    }

    public function jobCardParts(): HasMany
    {
        return $this->hasMany(JobCardPart::class);
    }
}

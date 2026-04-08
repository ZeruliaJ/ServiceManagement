<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleType extends Model
{
    protected $fillable = ['code', 'name', 'description'];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}

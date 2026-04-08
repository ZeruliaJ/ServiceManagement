<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarrantyStatus extends Model
{
    protected $fillable = ['code', 'name'];

    public function warranties(): HasMany
    {
        return $this->hasMany(Warranty::class);
    }
}

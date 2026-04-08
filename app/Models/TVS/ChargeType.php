<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChargeType extends Model
{
    protected $fillable = ['code', 'name'];

    public function jobCardParts(): HasMany
    {
        return $this->hasMany(JobCardPart::class);
    }

    public function jobCardLabour(): HasMany
    {
        return $this->hasMany(JobCardLabour::class);
    }
}

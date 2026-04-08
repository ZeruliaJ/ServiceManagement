<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartyType extends Model
{
    protected $fillable = ['code', 'name', 'description'];

    public function parties(): HasMany
    {
        return $this->hasMany(Party::class);
    }
}

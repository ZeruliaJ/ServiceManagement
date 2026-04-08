<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GatePassStatus extends Model
{
    protected $fillable = ['code', 'name'];

    public function gatePasses(): HasMany
    {
        return $this->hasMany(GatePass::class);
    }
}

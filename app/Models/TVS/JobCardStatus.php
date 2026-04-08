<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobCardStatus extends Model
{
    protected $fillable = ['code', 'name'];

    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class);
    }
}

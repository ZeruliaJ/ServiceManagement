<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'branch_code', 'branch_name', 'region', 'zone', 'district', 'town',
        'address', 'phone', 'email', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jobCardMetrics(): HasMany
    {
        return $this->hasMany(JobCardMetric::class);
    }

    public function dailySummaries(): HasMany
    {
        return $this->hasMany(DailyBranchSummary::class);
    }
}

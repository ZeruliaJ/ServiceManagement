<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyBranchSummary extends Model
{
    protected $fillable = [
        'branch_id', 'summary_date', 'pending_count', 'in_progress_count',
        'completed_count', 'delivered_count', 'daily_revenue', 'warranty_claims_value',
        'free_services_count'
    ];

    protected $casts = [
        'summary_date' => 'datetime',
        'daily_revenue' => 'decimal:2',
        'warranty_claims_value' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}

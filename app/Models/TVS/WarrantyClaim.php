<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarrantyClaim extends Model
{
    protected $fillable = [
        'warranty_id', 'job_card_id', 'claim_date', 'claim_amount', 'claim_status',
        'claim_reason', 'rejection_reason', 'approval_date', 'approved_by'
    ];

    protected $casts = [
        'claim_date' => 'datetime',
        'approval_date' => 'datetime',
        'claim_amount' => 'decimal:2',
    ];

    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class);
    }

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }
}

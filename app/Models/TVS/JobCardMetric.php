<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCardMetric extends Model
{
    protected $fillable = [
        'job_card_id', 'branch_id', 'check_in_date', 'job_open_date',
        'completion_date', 'delivery_date', 'gate_out_date',
        'tat_check_in_to_open', 'tat_open_to_completion',
        'tat_completion_to_delivery', 'tat_delivery_to_gate_out', 'tat_total'
    ];

    protected $casts = [
        'check_in_date' => 'datetime',
        'job_open_date' => 'datetime',
        'completion_date' => 'datetime',
        'delivery_date' => 'datetime',
        'gate_out_date' => 'datetime',
    ];

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}

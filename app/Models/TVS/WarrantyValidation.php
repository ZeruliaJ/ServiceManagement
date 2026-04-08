<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarrantyValidation extends Model
{
    protected $fillable = [
        'warranty_id', 'job_card_id', 'validation_date', 'validation_status',
        'validation_reason', 'claim_eligible'
    ];

    protected $casts = [
        'validation_date' => 'datetime',
        'claim_eligible' => 'boolean',
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

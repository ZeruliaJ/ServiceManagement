<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCardSignature extends Model
{
    protected $fillable = [
        'job_card_id', 'signature_type', 'signature_data', 'signed_by_name',
        'signed_by_id', 'signed_date', 'notes'
    ];

    protected $casts = [
        'signed_date' => 'datetime',
    ];

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }
}

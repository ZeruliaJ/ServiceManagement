<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCardLabour extends Model
{
    protected $table = 'job_card_labour';
    protected $fillable = [
        'job_card_id', 'operation_name', 'technician_id', 'hours', 'rate',
        'amount', 'charge_type_id', 'remarks'
    ];

    protected $casts = [
        'hours' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(LabourOperation::class, 'operation_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function chargeType(): BelongsTo
    {
        return $this->belongsTo(ChargeType::class);
    }
}

<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatePass extends Model
{
    protected $fillable = [
        'gate_pass_no', 'job_card_id', 'vehicle_id', 'gate_pass_status_id',
        'customer_name', 'customer_id_type', 'customer_id_no', 'gate_pass_generated_date',
        'generated_by', 'gate_pass_used_date', 'used_by', 'authorization_notes', 'qr_code'
    ];

    protected $casts = [
        'gate_pass_generated_date' => 'datetime',
        'gate_pass_used_date' => 'datetime',
    ];

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(GatePassStatus::class, 'gate_pass_status_id');
    }
}

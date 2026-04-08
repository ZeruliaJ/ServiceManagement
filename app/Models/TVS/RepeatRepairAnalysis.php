<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepeatRepairAnalysis extends Model
{
    protected $fillable = [
        'vehicle_id', 'defect_type', 'repeat_count', 'first_occurrence_date',
        'last_occurrence_date', 'total_cost', 'analysis_notes'
    ];

    protected $casts = [
        'first_occurrence_date' => 'datetime',
        'last_occurrence_date' => 'datetime',
        'total_cost' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}

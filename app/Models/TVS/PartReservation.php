<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartReservation extends Model
{
    protected $fillable = [
        'part_id', 'warehouse_id', 'job_card_id', 'quantity_reserved',
        'reservation_status', 'reservation_date', 'expected_fulfillment_date',
        'fulfillment_date', 'notes'
    ];

    protected $casts = [
        'reservation_date' => 'datetime',
        'expected_fulfillment_date' => 'datetime',
        'fulfillment_date' => 'datetime',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }
}

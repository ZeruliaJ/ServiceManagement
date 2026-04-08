<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartStock extends Model
{
    protected $fillable = [
        'part_id', 'warehouse_id', 'quantity_on_hand', 'quantity_reserved',
        'reorder_level', 'reorder_quantity', 'last_stock_check'
    ];

    protected $casts = [
        'last_stock_check' => 'datetime',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function getAvailableQuantity(): int
    {
        return $this->quantity_on_hand - $this->quantity_reserved;
    }

    public function isLowStock(): bool
    {
        return $this->getAvailableQuantity() <= $this->reorder_level;
    }
}

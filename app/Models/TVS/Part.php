<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    protected $fillable = [
        'part_code', 'part_name', 'description', 'category', 'unit_price',
        'unit_of_measure', 'is_active', 'specifications'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function stock(): HasMany
    {
        return $this->hasMany(PartStock::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(PartReservation::class);
    }

    public function jobCardParts(): HasMany
    {
        return $this->hasMany(JobCardPart::class);
    }

    public function getAvailableQuantity($warehouseId): int
    {
        $stock = $this->stock()
            ->where('warehouse_id', $warehouseId)
            ->first();

        if (!$stock) {
            return 0;
        }

        return $stock->quantity_on_hand - $stock->quantity_reserved;
    }
}

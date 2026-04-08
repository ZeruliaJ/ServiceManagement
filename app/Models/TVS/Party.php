<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Party extends Model
{
    protected $fillable = [
        'party_type_id', 'code', 'name', 'phone', 'email', 'tax_id',
        'address', 'city', 'region', 'district', 'town', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function partyType(): BelongsTo
    {
        return $this->belongsTo(PartyType::class);
    }

    public function ownerMappings(): HasMany
    {
        return $this->hasMany(OwnerMapping::class);
    }

    public function customerJobCards(): HasMany
    {
        return $this->hasMany(JobCard::class, 'customer_party_id');
    }

    public function billToJobCards(): HasMany
    {
        return $this->hasMany(JobCard::class, 'bill_to_party_id');
    }

    public function customerLifetimeValues(): HasMany
    {
        return $this->hasMany(CustomerLifetimeValue::class);
    }
}

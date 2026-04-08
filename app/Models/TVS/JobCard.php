<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class JobCard extends Model
{
    protected $fillable = [
        'job_card_no', 'vehicle_id', 'service_type_id', 'job_card_status_id',
        'customer_party_id', 'bill_to_party_id', 'free_service_coupon_id',
        'check_in_date', 'odometer_in', 'odometer_out', 'fuel_level_in',
        'fuel_level_out', 'customer_complaints', 'estimated_delivery_date',
        'actual_delivery_date', 'priority', 'assigned_technician_id',
        'supervisor_notes', 'technician_remarks'
    ];

    protected $casts = [
        'check_in_date' => 'datetime',
        'estimated_delivery_date' => 'datetime',
        'actual_delivery_date' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(JobCardStatus::class, 'job_card_status_id');
    }

    public function customerParty(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'customer_party_id');
    }

    public function billToParty(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'bill_to_party_id');
    }

    public function freeServiceCoupon(): BelongsTo
    {
        return $this->belongsTo(FreeServiceCoupon::class);
    }

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function standardChecks(): HasMany
    {
        return $this->hasMany(JobCardStandardCheck::class);
    }

    public function afterTrialChecks(): HasMany
    {
        return $this->hasMany(JobCardAfterTrialCheck::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(JobCardPart::class);
    }

    public function labour(): HasMany
    {
        return $this->hasMany(JobCardLabour::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(JobCardSignature::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(JobCardPayment::class, 'id', 'job_card_id');
    }

    public function gatePass(): HasMany
    {
        return $this->hasMany(GatePass::class);
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(JobCardMetric::class);
    }

    public function warrantyValidations(): HasMany
    {
        return $this->hasMany(WarrantyValidation::class);
    }

    public function warrantyClaims(): HasMany
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    public function partReservations(): HasMany
    {
        return $this->hasMany(PartReservation::class);
    }
}

<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;

class JobCard extends Model
{
   protected $fillable = [
    'job_card_number', 'customer_id', 'vehicle_id', 'dealer_id',
    'odometer_reading', 'fuel_level', 'service_type', 'free_service_number',
    'customer_complaints', 'estimated_delivery', 'technician_id', 'supervisor_id',
    'status', 'internal_notes', 'customer_signature_data', 'supervisor_signature_data',
    'customer_signed_by', 'supervisor_signed_by', 'customer_consent',
    'delivery_customer_name', 'created_by', 'assigned_to',
];
   protected $casts = [
    'estimated_delivery' => 'datetime',
    'delivery_date'      => 'datetime',
    'completion_date'    => 'datetime',
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
        return $this->belongsTo(Party::class, 'customer_id');
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

     public function gatePass(): HasOne
{
    return $this->hasOne(GatePass::class);
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
    public function createdBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'created_by');
}

}

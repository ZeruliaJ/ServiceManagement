<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCardPayment extends Model
{
    protected $fillable = [
        'job_card_id', 'parts_total', 'labour_total', 'subtotal', 'tax_amount',
        'discount_amount', 'grand_total', 'payment_status_id', 'payment_mode_id',
        'amount_paid', 'balance_amount', 'invoice_no', 'receipt_no', 'payment_date',
        'paid_by', 'notes'
    ];

    protected $casts = [
        'parts_total' => 'decimal:2',
        'labour_total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }

    public function paymentStatus(): BelongsTo
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function getBalanceAttribute(): float
    {
        return $this->grand_total - $this->amount_paid;
    }
}

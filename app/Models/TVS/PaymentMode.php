<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMode extends Model
{
    protected $fillable = ['code', 'name'];

    public function jobCardPayments(): HasMany
    {
        return $this->hasMany(JobCardPayment::class);
    }
}

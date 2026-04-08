<?php

namespace App\Models\TVS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Technician extends Model
{
    protected $fillable = [
        'user_id', 'employee_code', 'specialization', 'hourly_rate',
        'is_supervisor', 'is_active'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_supervisor' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobCardLabour(): HasMany
    {
        return $this->hasMany(JobCardLabour::class);
    }
}

<?php
namespace App\Models\TVS; 
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'contact',
        'location',
        'branch',
        'registration_no',
        'status',
    ];

    // Relationships (optional but recommended)
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function serviceHistory()
    {
        return $this->hasMany(ServiceHistory::class);
    }
}
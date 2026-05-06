<?php
namespace App\Models\TVS; 
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
    'customer_code', 'first_name', 'last_name', 'email',
    'phone_number', 'alternate_phone', 'address_line1', 'address_line2',
    'city', 'state', 'pincode', 'customer_type', 'status',
    'registration_date', 'notes',
];

    // Relationships (optional but recommended)
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

  /*  public function serviceHistory()
    {
        return $this->hasMany(ServiceHistory::class);
    } */
        
}
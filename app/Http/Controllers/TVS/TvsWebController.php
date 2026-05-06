<?php

namespace App\Http\Controllers\TVS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\TVS\Warehouse;
use App\Models\TVS\Customer;

class TvsWebController extends Controller
{

private function generateJobCardNumber()
{
    $year   = date('Y');
    $month  = date('m');
    $prefix = "JC-{$year}{$month}-";

    $last = \App\Models\TVS\JobCard::where('job_card_number', 'like', "{$prefix}%")
        ->orderBy('id', 'desc')
        ->value('job_card_number');

    if ($last) {
        $lastNumber = intval(substr($last, strlen($prefix)));
        $newNumber  = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
}


    public function dashboard()
    {
        return view('tvs.dashboard', ['title' => 'TVS Service – Dashboard']);
    }
public function storeJobCard(Request $request)
{
    // Find vehicle
    $vehicle = \App\Models\TVS\Vehicle::where('chassis_number', $request->chassis_number)->first();

    // Find customer
    $customer = \App\Models\TVS\Customer::where('phone_number', $request->customer_phone)->first();

    // Generate job card number
    $jobCardNo = $this->generateJobCardNumber();

    // Get default open status
    $defaultStatus = \App\Models\TVS\JobCardStatus::first();

    $jobCard = \App\Models\TVS\JobCard::create([
    'job_card_number'          => $jobCardNo,
    'customer_id'              => $customer?->id,
    'vehicle_id'               => $vehicle?->id,
    'dealer_id'                => 1, // default or get from vehicle
    'odometer_reading'         => $request->odometer_in,
    'fuel_level'               => $request->fuel_level_in,
    'service_type'             => $request->service_type_id,
    'customer_complaints'      => $request->customer_complaints,
    'estimated_delivery'       => $request->estimated_delivery_date,
    'technician_id'            => $request->assigned_technician_id ?? null,
    'supervisor_id'            => $request->supervisor_id ?? null,
    'status'                   => 'pending',
    'internal_notes'           => $request->supervisor_notes ?? null,
    'customer_signature_data'  => $request->customer_signature ?? null,
    'supervisor_signature_data'=> $request->supervisor_signature ?? null,
    'customer_signed_by'       => $request->customer_signed_by ?? null,
    'supervisor_signed_by'     => $request->supervisor_name ?? null,
    'customer_consent'         => $request->customer_consent ? 1 : 0,
    'created_by'               => auth()->id(),
]);

    return response()->json([
        'success'  => true,
        'redirect' => route('tvs.job-cards'),
        'job_card_no' => $jobCard->job_card_no,
    ]);
}
 
    public function vehicles()
    {
        return view('tvs.vehicles.index', ['title' => 'Vehicle Management']);
    }

    public function vehicleShow($id)
    {
        return view('tvs.vehicles.show', ['title' => 'Vehicle Detail', 'vehicleId' => $id]);
    }

    public function parties()
    {
        return view('tvs.parties.index', ['title' => 'Customer / Party Management']);
    }

    public function partyCreate()
    {
        return view('tvs.parties.create', ['title' => 'New Customer / Party']);
    }

    public function partyShow($id)
    {
        return view('tvs.parties.show', ['title' => 'Party Detail', 'partyId' => $id]);
    }

    public function jobCards()
{
    $jobCards = \App\Models\TVS\JobCard::latest()->paginate(20);
    
    return view('tvs.job-cards.index', [
        'title'    => 'Job Cards',
        'jobCards' => $jobCards,
    ]);
}

   public function jobCardCreate()
{
   $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
    return view('tvs.job-cards.create', [
        'title' => 'New Job Card – Reception',
        'warehouses' => $warehouses,
        'technicians' => \App\Models\User::where('role', 'technician')->orderBy('full_name')->get(),
        'supervisors' => \App\Models\User::where('role', 'supervisor')->orderBy('full_name')->get(), 
    ]);
}

    public function jobCardShow($id)
    {
        return view('tvs.job-cards.show', ['title' => 'Job Card Detail', 'jobCardId' => $id]);
    }

    public function gatePasses()
    {
        return view('tvs.gate-passes.index', ['title' => 'Gate Pass Management']);
    }

    public function warranties()
    {
        return view('tvs.warranties.index', ['title' => 'Warranty Management']);
    }

    public function reports()
    {
        return view('tvs.reports.index', ['title' => 'Reports & Analytics']);
    }
    public function searchCustomer(Request $request)
{
    $phone = $request->input('phone');

    $customer = Customer::where('phone_number', $phone)
                ->orWhere('alternate_phone', $phone)
                ->first();

    if ($customer) {
        return response()->json([
            'success'  => true,
            'customer' => $customer
        ]);
    }

    return response()->json(['success' => false]);
}
}

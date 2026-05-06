<?php

namespace App\Http\Controllers;

use App\Models\TVS\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CustomerController extends Controller
{
  public function index()
{
    $customers = Customer::withCount(['vehicles'])
        ->with(['vehicles' => function($q) {
            $q->select('id', 'customer_id', 'vehicle_model', 'last_service_date')
              ->orderBy('last_service_date', 'desc');
        }])
        ->latest()
        ->get();

    $title = 'Customers';
    return view('customers.index', compact('customers', 'title'));
}
    public function create()
    {
        $title = 'Add Customer';
        return view('customers.create', compact('title'));
    }

   public function store(Request $request)
{
     try{
    $validated = $request->validate([
        'first_name' => 'required',
        'last_name' => 'required',
        'phone_number' => 'required|digits:9',
        'alternate_phone' => 'nullable|digits:9',
        'email' => 'nullable|email',
    ]);
}catch (\Illuminate\Validation\ValidationException $e) {
        dd($e->errors()); // add this
    }

   Customer::create([
    'customer_code'     => $request->customer_code,
    'first_name'        => $validated['first_name'],
    'last_name'         => $validated['last_name'],
    'phone_number'      => '+255' . $validated['phone_number'],
    'alternate_phone'   => isset($validated['alternate_phone'])
                            ? '+255' . $validated['alternate_phone']
                            : null,
    'email'             => $validated['email'] ?? null,
    'address_line1'     => $request->address_line1,
    'address_line2'     => $request->address_line2,
    'city'              => $request->town,      // town → city
    'pincode'           => $request->district,  // district → pincode
    'state'             => $request->region,    // region → state
    'customer_type'     => $request->customer_type,
    'status'            => $request->status,
    'registration_date' => now(),
    'notes'             => $request->notes,
]);

    return redirect()->route('customers.index')
        ->with('success', 'Customer created successfully');
}

  public function show(Customer $customer)
{
    if (request()->ajax()) {
        return view('customers.partials.show', compact('customer'));
    }
    return view('customers.show', compact('customer'));
}

    public function edit(Customer $customer)
    {
        $title = 'Edit Customer';
        return view('customers.edit', compact('customer', 'title'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully');
    }
  public function generateCustomerCode(Request $request)
{
    $city = $request->city ?? 'DAR';
    $cityCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $city), 0, 3));
    if (empty($cityCode)) $cityCode = 'DAR';

    $prefix = 'CGS-' . $cityCode . '-';

    $last = DB::table('customers')
        ->where('customer_code', 'like', $prefix . '%')
        ->orderBy('customer_code', 'desc')
        ->value('customer_code');

    if ($last) {
        preg_match('/([A-Z])(\d+)$/', $last, $matches);
        $letter = $matches[1] ?? 'A';
        $number = isset($matches[2]) ? (int)$matches[2] + 1 : 1;
    } else {
        $letter = 'A'; // always start at A, not random
        $number = 1;
    }

    // loop until a unique code is found
    do {
        $customerCode = $prefix . $letter . str_pad($number, 5, '0', STR_PAD_LEFT);
        $exists = DB::table('customers')->where('customer_code', $customerCode)->exists();
        if ($exists) $number++;
    } while ($exists);

    return response()->json([
        'success'       => true,
        'customer_code' => $customerCode,
    ]);
}

}
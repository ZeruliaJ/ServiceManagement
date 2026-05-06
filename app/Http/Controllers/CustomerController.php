<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount(['vehicles', 'serviceHistory'])
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
        $request->validate([
            'name' => 'required',
            'contact' => 'nullable',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully');
    }

    public function show(Customer $customer)
    {
        $title = 'Customer Details';
        return view('customers.show', compact('customer', 'title'));
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
}
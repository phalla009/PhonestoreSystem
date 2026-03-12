<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Show list of customers
    public function index()
    {
        $customers = Customer::withCount('orders')->get();

        foreach ($customers as $customer) {
            $customer->status = $customer->orders_count > 0 ? 'Active' : 'Inactive';
        }

        return view('customers.index', compact('customers'));
    }

    // Show create form
    public function create()
    {
        return view('customers.create');
    }

    // Store new customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'gender'   => 'required|string|in:male,female',
            'phone'    => 'required|string|max:20|unique:customers,phone',
            'email'    => 'required|email|max:255|unique:customers,email',
            'password' => 'required|string|min:8|confirmed',
            'status'   => 'required|in:active,inactive',
        ], [
            'phone.unique' => 'This phone number already exists.',
            'email.unique' => 'This email address already exists.',
        ]);

        Customer::create([
            'name'     => $validated['name'],
            'gender'   => $validated['gender'],
            'phone'    => $validated['phone'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
            'status'   => $validated['status'],
        ]);

        return redirect()->route('customers.create')->with('success', 'Customer added successfully.');
    }

    // Show a customer
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    // Show edit form
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    // Update customer
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'gender'   => 'required|string|in:male,female',
            'phone'    => 'required|string|max:20|unique:customers,phone,' . $id,
            'email'    => 'required|email|max:255|unique:customers,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'phone.unique' => 'This phone number already exists.',
            'email.unique' => 'This email address already exists.',
        ]);

        $customer         = Customer::findOrFail($id);
        $customer->name   = $validated['name'];
        $customer->gender = $validated['gender'];
        $customer->phone  = $validated['phone'];
        $customer->email  = $validated['email'];

        if (!empty($validated['password'])) {
            $customer->password = bcrypt($validated['password']);
        }

        if ($request->has('status')) {
            $customer->status = $request->input('status');
        }

        $customer->save();
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    // Delete customer
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
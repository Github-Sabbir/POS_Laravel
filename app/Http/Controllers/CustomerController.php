<?php

namespace App\Http\Controllers;

use App\Models\{Customer, CustomerPayment};
use Illuminate\Http\Request;
class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index', ['customers' => Customer::latest()->paginate(20)]);
    }
    public function create()
    {
        return view('customers.form', ['customer' => new Customer()]);
    }
    public function store(Request $r)
    {
        $d = $r->validate(['name' => 'required|max:255', 'phone' => 'nullable|max:50', 'email' => 'nullable|email', 'address' => 'nullable', 'opening_due' => 'nullable|numeric|min:0', 'status' => 'required|in:active,inactive']);
        Customer::create($d);
        return redirect()->route('customers.index')->with('success', 'Customer saved.');
    }
    public function edit(Customer $customer)
    {
        return view('customers.form', compact('customer'));
    }
    public function update(Request $r, Customer $customer)
    {
        $d = $r->validate(['name' => 'required|max:255', 'phone' => 'nullable|max:50', 'email' => 'nullable|email', 'address' => 'nullable', 'opening_due' => 'nullable|numeric|min:0', 'status' => 'required|in:active,inactive']);
        $customer->update($d);
        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }
    public function destroy(Customer $customer)
    {
        if ($customer->sales()->exists()) {
            return back()->withErrors(['customer' => 'This customer has sales history and cannot be deleted. Set it inactive instead.']);
        }
        $customer->delete();
        return back()->with('success', 'Customer deleted.');
    }
    public function payment(Request $r, Customer $customer)
    {
        $d = $r->validate(['amount' => 'required|numeric|min:.01', 'payment_method' => 'required|in:cash,card,mobile,other', 'note' => 'nullable']);
        if ($d['amount'] > $customer->current_due) {
            return back()->withErrors(['amount' => 'Payment cannot exceed current customer due.']);
        }
        CustomerPayment::create(['customer_id' => $customer->id, 'user_id' => $r->user()->id, 'amount' => $d['amount'], 'payment_method' => $d['payment_method'], 'note' => $d['note'] ?? null, 'paid_at' => now()]);
        return back()->with('success', 'Customer payment recorded.');
    }
}

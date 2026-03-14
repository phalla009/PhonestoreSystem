<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;

class PaymentController extends Controller
{
    /**
     * Display a listing of completed orders with their payments.
     */
    public function index(Request $request)
    {
        // ✅ Base query: all completed orders with relations
        $baseQuery = Order::with(['customer', 'product', 'payments'])
            ->where('status', 'completed');

        // ✅ Apply customer name search if provided
        if ($request->filled('customer_name')) {
            $baseQuery->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        $allCompleted = $baseQuery->latest('orders.created_at')->get();

        // ✅ Split into: paid orders (has payment records) vs unpaid (no payment record yet)
        //    This fixes orders showing "No Payment Record / No actions" unexpectedly
        $orders       = $allCompleted->filter(fn($o) => $o->payments->isNotEmpty())->values();
        $unpaidOrders = $allCompleted->filter(fn($o) => $o->payments->isEmpty())->values();

        return view('payments.index', compact('orders', 'unpaidOrders'));
    }

    /**
     * Show the payment form for a given order.
     */
    public function payment(Order $order)
    {
        return view('payments.payment', compact('order'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        // ✅ Column is 'amount' — confirmed by Blade view using payments->sum('amount')
        Payment::create([
            'order_id'       => $order->id,
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'paid_at'        => now(),
        ]);

        $order->status = 'completed';
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Payment completed and order status updated.');
    }

    /**
     * Display a single payment's details.
     */
    public function show(Payment $payment)
    {
        $payment->load(['order.customer', 'order.product']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the edit form for a payment.
     */
    public function edit(Payment $payment)
    {
        $payment->load(['order.customer', 'order.product']);
        return view('payments.edit', compact('payment'));
    }

    /**
     * Update an existing payment.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        // ✅ Column is 'amount'
        $payment->update([
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    /**
     * Delete a payment record.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}
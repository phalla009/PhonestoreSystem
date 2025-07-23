<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;

class PaymentController extends Controller
{
   public function index(Request $request)
    {
        $query = Payment::with(['order.customer', 'order.product']);

        if ($request->filled('customer_name')) {
            $query->whereHas('order.customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        $payments = $query->get();

        return view('payments.index', compact('payments'));
    }

    
    public function payment(Order $order)
    {
        return view('payments.payment', compact('order'));
    }

  public function store(Request $request)
    {
        // Validate payment input
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        // Get order and calculate total
        $order = Order::findOrFail($validated['order_id']);
        $quantity = $order->quantity;
        $totalAmount = $quantity * $validated['amount'];

        // Create the payment
        Payment::create([
            'order_id'       => $order->id,
            'amount'         => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'paid_at'        => now(),
        ]);

        // Update order status
        $order->status = 'completed';
        $order->save();

        // Redirect back with success
        return redirect()->route('orders.index')->with('success', 'Payment completed and order status updated.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.customer', 'order.product']);
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $payment->load(['order.customer', 'order.product']);
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        $payment->update([
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
    // public function search(Request $request)
    // {
    //     $query = Payment::with(['order.customer', 'order.product']);

    //     if ($request->filled('customer_name')) {
    //         $query->whereHas('order.customer', function ($q) use ($request) {
    //             $q->where('name', 'like', '%' . $request->customer_name . '%');
    //         });
    //     }

    //     $payment = $query->get()->map(function ($payment) {
    //         return [
    //             'id' => $payment->id,
    //             'amount' => $payment->amount,
    //             'order_number' => $payment->order->order_number ?? null,
    //             'customer_name' => $payment->order->customer->name ?? null,
    //             'product_name' => $payment->order->product->name ?? null,
    //         ];
    //     });

    //     return response()->json($payment);
    // }



}

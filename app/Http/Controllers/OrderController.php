<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = Customer::all();

        $query = Order::with(['customer', 'product'])
            ->withSum('payments', 'amount');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->get();

        return view('orders.index', compact('orders', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $products  = Product::where('status', 'active')->get();
        return view('orders.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id'  => 'required|exists:products,id',
            'quantity'    => 'required|integer|min:1',
            'status'      => 'required|in:pending,completed,cancelled',
            'order_date'  => 'required|date',
            'note'        => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->status !== 'active') {
            return redirect()->back()->withErrors([
                'product_id' => 'The selected product is inactive and cannot be ordered.'
            ])->withInput();
        }

        if ($product->stock < $request->quantity) {
            return redirect()->back()->withErrors([
                'quantity' => 'The order quantity exceeds available stock (' . $product->stock . ').'
            ])->withInput();
        }

        $totalAmount = $product->price * $request->quantity;

        $lastOrder   = Order::where('order_number', 'like', 'ORD-KR%')
                            ->orderBy('id', 'desc')
                            ->first();
        $newNumber   = $lastOrder ? intval(substr($lastOrder->order_number, 6)) + 1 : 1;
        $orderNumber = 'ORD-KR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        Order::create([
            'order_number' => $orderNumber,
            'customer_id'  => $request->customer_id,
            'product_id'   => $request->product_id,
            'quantity'     => $request->quantity,
            'total_amount' => $totalAmount,
            'status'       => $request->status,
            'order_date'   => $request->order_date,
            'note'         => $request->note,
        ]);

        $product->stock -= $request->quantity;
        $product->save();

        return redirect()->route('orders.create')->with('success', 'Order added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['customer', 'product'])
                    ->withSum('payments', 'amount')
                    ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order     = Order::findOrFail($id);
        $customers = Customer::all();
        $products  = Product::all();
        return view('orders.edit', compact('order', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order          = Order::findOrFail($id);
        $oldQuantity    = $order->quantity;
        $product        = Product::findOrFail($order->product_id);
        $newQuantity    = $request->quantity;
        $availableStock = $product->stock + $oldQuantity;

        if ($newQuantity > $availableStock) {
            return redirect()->back()->withErrors([
                'quantity' => 'The ordered quantity exceeds available stock'
            ])->withInput();
        }

        $product->stock = $availableStock - $newQuantity;
        $product->save();

        $order->quantity     = $newQuantity;
        $order->total_amount = $newQuantity * $product->price;
        $order->customer_id  = $request->customer_id;
        $order->status       = $request->status;
        $order->order_date   = $request->order_date;
        $order->note         = $request->note;
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    /**
     * Bulk delete selected orders.
     */
    public function bulkDestroy(Request $request)
    {
        Order::whereIn('id', $request->ids)->delete();
        return redirect()->route('orders.index')->with('success', 'Selected orders deleted successfully.');
    }

    /**
     * Show payment form inside modal.
     */
    public function payment($id)
    {
        $order = Order::with(['customer', 'product'])->findOrFail($id);
        return view('payments.payment', compact('order'));
    }

    /**
     * Process payment for an order.
     */
    public function pay(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'payment_notes'  => 'nullable|string',
        ]);

        $order                 = Order::findOrFail($id);
        $order->payment_method = $request->payment_method;
        $order->payment_notes  = $request->payment_notes;
        $order->status         = 'completed';
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Payment processed successfully.');
    }

    /**
     * Return a partial HTML invoice fragment for single order print.
     * Route: GET /orders/{order}/invoice-partial
     */
    public function invoicePartial(Order $order)
    {
        $order->load(['customer', 'product']);
        return view('orders.invoice_partial', compact('order'));
    }

    /**
     * Return a combined 80mm receipt invoice for multiple selected orders.
     * Route: GET /orders/invoice-combined?ids[]=1&ids[]=2
     */
    public function invoiceCombined(Request $request)
    {
        $ids = $request->input('ids', []);

        $orders = Order::with(['customer', 'product'])
                       ->whereIn('id', $ids)
                       ->get();

        return view('orders.invoice_combined', compact('orders'));
    }
}
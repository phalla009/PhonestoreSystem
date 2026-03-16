<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $totalItems = $products->sum('stock');
        $lowStockItems = $products->filter(function ($product) {
            return $product->stock >= 1 && $product->stock <= 10;
        })->count();
        $outOfStockItems = $products->where('stock', '<=', 0)->count();
        $inventoryValue = $products->sum(function ($product) {
            return $product->stock * $product->price;
        });

        return view('inventorys.index', compact(
            'products',
            'totalItems',
            'lowStockItems',
            'outOfStockItems',
            'inventoryValue'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('inventorys.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|in:in,out,adjustment',
            'quantity'   => 'required|integer|min:1',
            'reference'  => 'nullable|string|max:100',
            'note'       => 'nullable|string|max:500',
        ]);

        $product  = Product::findOrFail($validated['product_id']);
        $quantity = (int) $validated['quantity'];

        switch ($validated['type']) {
            case 'in':
                $product->stock += $quantity;
                break;

            case 'out':
                if ($product->stock < $quantity) {
                    return back()
                        ->withInput()
                        ->withErrors(['quantity' => 'Not enough stock. Available: ' . $product->stock]);
                }
                $product->stock -= $quantity;
                break;

            case 'adjustment':
                $product->stock = $quantity;
                break;
        }

        $product->save();

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Stock updated successfully for "' . $product->name . '".');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);

        return view('inventorys.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);

        return view('inventorys.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'type'      => 'required|in:in,out,adjustment',
            'quantity'  => 'required|integer|min:1',
            'reference' => 'nullable|string|max:100',
            'note'      => 'nullable|string|max:500',
        ]);

        $product  = Product::findOrFail($id);
        $quantity = (int) $validated['quantity'];

        switch ($validated['type']) {
            case 'in':
                $product->stock += $quantity;
                break;

            case 'out':
                if ($product->stock < $quantity) {
                    return back()
                        ->withInput()
                        ->withErrors(['quantity' => 'Not enough stock. Available: ' . $product->stock]);
                }
                $product->stock -= $quantity;
                break;

            case 'adjustment':
                $product->stock = $quantity;
                break;
        }

        $product->save();

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Stock updated successfully for "' . $product->name . '".');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
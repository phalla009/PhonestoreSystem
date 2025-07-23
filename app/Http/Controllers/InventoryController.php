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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

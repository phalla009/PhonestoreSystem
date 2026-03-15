<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    /**
     * Display a list of products with their generated barcodes.
     * Supports searching by product name or SKU.
     */
    public function scan()
    {
        $products = Product::orderBy('name')->get();

        $productMap = [];
        foreach ($products as $p) {
            $sku = $p->sku ?? (string) $p->id;
            $productMap[$sku] = [
                'id'    => $p->id,
                'name'  => $p->name,
                'sku'   => $sku,
                'price' => number_format($p->price, 2),
            ];
        }

        return view('barcodes.scan', compact('productMap'));
    }

    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        $products = $query->orderBy('name')->get();

        // Build a SKU-keyed map for the client-side barcode scanner
        $productMap = [];
        foreach ($products as $p) {
            $sku = $p->sku ?? (string) $p->id;
            $productMap[$sku] = [
                'name'  => $p->name,
                'sku'   => $sku,
                'price' => number_format($p->price, 2),
            ];
        }

        return view('barcodes.index', compact('products', 'productMap'));
    }
    
}
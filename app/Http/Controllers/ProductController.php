<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with(['category', 'images']);

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/products'), $filename);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $filename,
                ]);
            }
        }

        return redirect()->route('products.create')->with('success', 'Product added successfully.');
    }

    public function show(string $id)
    {
        $product = Product::with('images')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function edit(string $id)
    {
        $product = Product::with('images')->findOrFail($id);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:product_images,id',
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);

        // Delete selected images
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = ProductImage::find($imageId);
                if ($image && $image->product_id == $product->id) {
                    $imagePath = public_path('images/products/' . $image->image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $image->delete();
                }
            }
        }

        // Add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/products'), $filename);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $filename,
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(string $id)
    {
        $product = Product::with('images')->findOrFail($id);

        foreach ($product->images as $image) {
            $imagePath = public_path('images/products/' . $image->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $image->delete();
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'The product and its images deleted successfully.');
    }

    public function inventory()
    {
        $products = Product::all();

        $totalItems = $products->count();
        $lowStockItems = $products->where('stock', '<=', function ($query) {
            $query->select('min_stock')
                  ->from('products')
                  ->whereColumn('id', 'products.id');
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
}

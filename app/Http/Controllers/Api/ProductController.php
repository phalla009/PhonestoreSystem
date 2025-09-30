<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // List all products with category name and images
    public function index()
    {
        $products = Product::with(['category', 'images'])->get()->map(function ($product) {
            return [
                'id'            => $product->id,
                'name'          => $product->name,
                'price'         => $product->price,
                'description'   => $product->description,
                'status'        => $product->status,
                'category'      => $product->category ? $product->category->name : null,
                'images'        => $product->images->pluck('image'),
            ];
        });

        return response()->json($products);
    }

    // Show single product with category and images
    public function show(Product $product)
    {
        $product->load(['category', 'images']);
        
        return response()->json([
            'id'            => $product->id,
            'name'          => $product->name,
            'price'         => $product->price,
            'description'   => $product->description,
            'status'        => $product->status,
            'category'      => $product->category ? $product->category->name : null,
            'images'        => $product->images->pluck('image'),
        ]);
    }
    // Show product by ID
    public function showById($id)
    {
        $product = Product::with(['category', 'images'])->find($id);

        if (!$product) {
            return response()->json([
                'error'   => true,
                'message' => 'Product not found'
            ], 404);
        }
        return response()->json([
            'id'            => $product->id,
            'name'          => $product->name,
            'price'         => $product->price,
            'description'   => $product->description,
            'status'        => $product->status,
            'category'      => $product->category ? $product->category->name : null,
            'images'        => $product->images->pluck('image'),
        ]);
    }

    // Store new product with images
    public function store(Request $request)
    {
        $request->validate([
            
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'description' => 'nullable|string',
            'status'      => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::create($request->all());

        // Save multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('images/products'), $filename);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $filename,
                ]);
            }
        }

        $product->load(['category', 'images']);

        return response()->json([
            'id'            => $product->id,
            'name'          => $product->name,
            'price'         => $product->price,
            'description'   => $product->description,
            'status'        => $product->status,
            'category'      => $product->category ? $product->category->name : null,
            'images'        => $product->images->pluck('image'),
        ], 201);
    }

    // Update product (images can be handled separately if needed)
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'sometimes|string|max:255',
            'price'       => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'status'      => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $product->update($request->all());

        $product->load(['category', 'images']);

        return response()->json([
            'id'            => $product->id,
            'name'          => $product->name,
            'price'         => $product->price,
            'description'   => $product->description,
            'status'        => $product->status,
            'category'      => $product->category ? $product->category->name : null,
            'images'        => $product->images->pluck('image'),
        ]);
    }

    // Delete product and optionally its images
    public function destroy(Product $product)
    {
        // Delete product images from storage
        foreach ($product->images as $img) {
            $path = public_path('images/products/'.$img->image);
            if (file_exists($path)) {
                unlink($path);
            }
            $img->delete();
        }

        $product->delete();

        return response()->json(null, 204);
    }
}

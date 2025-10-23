<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // List all categories
    public function index()
    {
        $categories = Category::all()->map(function ($category) {
            return [
                'id'          => $category->id,
                'name'        => $category->name,
                'description' => $category->description,
                
            ];
        });

        return response()->json($categories);
    }

    // Show single category
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'error'   => true,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'id'          => $category->id,
            'name'        => $category->name,
            'description' => $category->description,
           
        ]);
    }

    // Store new category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'id'          => $category->id,
            'name'        => $category->name,
            'description' => $category->description,
            
        ], 201);
    }

    // Update category
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'error'   => true,
                'message' => 'Category not found',
            ], 404);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($validated);

        return response()->json([
            'id'          => $category->id,
            'name'        => $category->name,
            'description' => $category->description,
            
        ]);
    }

    // Delete category
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'error'   => true,
                'message' => 'Category not found',
            ], 404);
        }

        $category->delete();

        return response()->json(null, 204);
    }
}

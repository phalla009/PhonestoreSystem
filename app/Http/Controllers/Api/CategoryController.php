<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // List all active categories
    public function index()
    {
        $categories = Category::where('status', 'active')
            ->get()
            ->map(function ($category) {
                return [
                    'id'          => $category->id,
                    'name'        => $category->name,
                    'description' => $category->description,
                    'status'      => $category->status,
                ];
            });

        return response()->json($categories);
    }

    // Show single active category
    public function show($id)
    {
        $category = Category::where('status', 'active')->find($id);

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
            'status'      => $category->status,
        ]);
    }

    // Store new category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'status'      => 'nullable|in:active,inactive',
        ]);

        // Default to active if not provided
        $validated['status'] = $validated['status'] ?? 'active';

        $category = Category::create($validated);

        return response()->json([
            'id'          => $category->id,
            'name'        => $category->name,
            'description' => $category->description,
            'status'      => $category->status,
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
            'status'      => 'nullable|in:active,inactive',
        ]);

        $category->update($validated);

        return response()->json([
            'id'          => $category->id,
            'name'        => $category->name,
            'description' => $category->description,
            'status'      => $category->status,
        ]);
    }

    // Delete category
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'error'   => true,
                'message' => 'Category not found.',
            ], 404);
        }

        $category->delete();

        return response()->json(null, 204);
    }
}
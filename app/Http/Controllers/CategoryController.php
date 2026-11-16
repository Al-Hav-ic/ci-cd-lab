<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->withCount('products')
            ->with('parent:id,name')
            ->orderBy('sort_order')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ], 200);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load([
            'parent:id,name',
            'children:id,name,parent_id',
            'products' => static fn ($query) => $query->withCount('reviews'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $category,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255', 'unique:categories,name'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $category = Category::create($validated);

        return response()->json([
            'status' => 'created',
            'data' => $category,
        ], 201);
    }
}

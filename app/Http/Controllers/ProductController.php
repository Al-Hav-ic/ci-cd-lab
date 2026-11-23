<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with([
                'category:id,name,slug',
                'reviews' => static fn ($query) => $query->where('is_approved', true),
            ])
            ->when(
                $request->filled('category_id'),
                static fn ($query) => $query->where('category_id', $request->integer('category_id')),
            )
            ->when(
                $request->filled('search'),
                static fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%'),
            )
            ->when(
                $request->boolean('active_only', true),
                static fn ($query) => $query->where('is_active', true),
            )
            ->orderBy('name')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'count' => $products->total(),
            'data' => $products,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'sku' => ['required', 'string', 'max:32', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->authorize('create', Product::class);
        $validated['user_id'] = $request->user()->id;

        $product = Product::create($validated);
        $product->load('category:id,name,slug');

        return response()->json([
            'status' => 'created',
            'data' => $product,
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load([
            'category:id,name,slug',
            'reviews' => static fn ($query) => $query->where('is_approved', true),
            'reviews.customer.user:id,name',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],
            'sku' => ['sometimes', 'string', 'max:32', 'unique:products,sku,'.$product->id],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock_qty' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $product->update($validated);
        $product->load('category:id,name,slug');

        return response()->json([
            'status' => 'updated',
            'data' => $product,
        ], 200);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json([
            'status' => 'deleted',
        ], 200);
    }
}

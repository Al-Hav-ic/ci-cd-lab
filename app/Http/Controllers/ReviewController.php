<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reviews = Review::query()
            ->with([
                'product:id,name,price',
                'customer.user:id,name',
            ])
            ->when(
                $request->filled('product_id'),
                static fn ($query) => $query->where('product_id', $request->integer('product_id')),
            )
            ->when(
                $request->boolean('approved_only', false),
                static fn ($query) => $query->where('is_approved', true),
            )
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $reviews,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'text' => ['nullable', 'string', 'max:2000'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        $review = Review::create($validated);
        $review->load('customer.user:id,name', 'product:id,name');

        return response()->json([
            'status' => 'created',
            'data' => $review,
        ], 201);
    }
}

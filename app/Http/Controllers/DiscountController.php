<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index(): JsonResponse
    {
        $discounts = Discount::query()
            ->withCount('orders')
            ->orderBy('valid_to')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $discounts,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64', 'unique:discounts,code'],
            'percent' => ['required', 'numeric', 'between:0,100'],
            'valid_from' => ['required', 'date'],
            'valid_to' => ['required', 'date', 'after_or_equal:valid_from'],
            'usage_limit' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $discount = Discount::create($validated);

        return response()->json([
            'status' => 'created',
            'data' => $discount,
        ], 201);
    }
}

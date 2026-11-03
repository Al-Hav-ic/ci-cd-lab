<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function summary(): JsonResponse
    {
        $items = [
            ['product_id' => 1, 'quantity' => 2, 'unit_price' => 24999.50],
            ['product_id' => 3, 'quantity' => 1, 'unit_price' => 1299.00],
        ];

        $total = array_reduce(
            $items,
            static fn (float $carry, array $item): float => $carry + $item['quantity'] * $item['unit_price'],
            0.0,
        );

        return response()->json([
            'status' => 'success',
            'items' => $items,
            'total' => round($total, 2),
            'currency' => 'UAH',
        ], 200);
    }
}

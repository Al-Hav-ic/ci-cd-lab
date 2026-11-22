<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        $carts = Cart::query()
            ->with(['items.product:id,name,price', 'customer.user:id,name,email'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $carts,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'session_id' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:32'],
        ]);

        $cart = Cart::create($validated);

        return response()->json([
            'status' => 'created',
            'data' => $cart,
        ], 201);
    }

    public function show(Cart $cart): JsonResponse
    {
        $cart->load(['items.product:id,name,price,stock_qty', 'customer.user:id,name,email']);

        return response()->json([
            'status' => 'success',
            'data' => $cart,
            'total' => round($cart->items->sum(static fn (CartItem $item): float => $item->getSubtotal()), 2),
        ], 200);
    }

    public function addItem(Request $request, Cart $cart): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail((int) $validated['product_id']);

        $item = $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'quantity' => $validated['quantity'],
                'unit_price' => $product->price,
            ],
        );

        $item->load('product:id,name,price');

        return response()->json([
            'status' => 'saved',
            'data' => $item,
        ], 201);
    }

    public function updateItem(Request $request, Cart $cart, CartItem $item): JsonResponse
    {
        abort_unless($item->cart_id === $cart->id, 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item->update($validated);

        return response()->json([
            'status' => 'updated',
            'data' => $item,
        ], 200);
    }

    public function destroyItem(Cart $cart, CartItem $item): JsonResponse
    {
        abort_unless($item->cart_id === $cart->id, 404);

        $item->delete();

        return response()->json([
            'status' => 'deleted',
        ], 200);
    }
}

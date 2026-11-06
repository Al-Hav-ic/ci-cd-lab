<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Список замовлень із жадібним завантаженням усіх зв'язків.
     */
    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->with([
                'customer.user:id,name,email',
                'items.product:id,name',
                'payment',
                'discounts:id,code,percent',
            ])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ], 200);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load([
            'customer.user:id,name,email',
            'items.product:id,name,price',
            'payment',
            'discounts:id,code,percent',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ], 200);
    }

    /**
     * Створення замовлення з позиціями та синхронізацією знижок (M:M).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'discount_ids' => ['nullable', 'array'],
            'discount_ids.*' => ['integer', 'exists:discounts,id'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
        ]);

        $order = DB::transaction(function () use ($validated): Order {
            $order = Order::create([
                'number' => 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'customer_id' => $validated['customer_id'],
                'status' => 'pending',
                'delivery_address' => $validated['delivery_address'] ?? null,
            ]);

            foreach ($validated['items'] as $row) {
                $product = Product::findOrFail((int) $row['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $row['quantity'],
                    'unit_price' => $product->price,
                ]);
            }

            $order->discounts()->sync($validated['discount_ids'] ?? []);
            $this->recalculateTotals($order);

            return $order;
        });

        $order->load(['customer.user:id,name,email', 'items', 'payment', 'discounts']);

        return response()->json([
            'status' => 'created',
            'data' => $order,
        ], 201);
    }

    /**
     * Синхронізація зв'язку M:M: залишає лише передані знижки.
     */
    public function syncDiscounts(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'discount_ids' => ['present', 'array'],
            'discount_ids.*' => ['integer', 'exists:discounts,id'],
        ]);

        $order->discounts()->sync($validated['discount_ids']);
        $this->recalculateTotals($order);
        $order->load(['items', 'discounts:id,code,percent']);

        return response()->json([
            'status' => 'synced',
            'data' => $order,
        ], 200);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,shipped,completed,cancelled'],
        ]);

        $order->update($validated);

        return response()->json([
            'status' => 'updated',
            'data' => $order,
        ], 200);
    }

    private function recalculateTotals(Order $order): void
    {
        $order->loadMissing('items', 'discounts');

        $subtotal = $order->items->sum(
            static fn (OrderItem $item): float => $item->quantity * $item->unit_price,
        );

        $percent = min(100.0, (float) $order->discounts->sum('percent'));
        $discountAmount = round($subtotal * $percent / 100, 2);

        $order->update([
            'total_amount' => round($subtotal - $discountAmount, 2),
            'discount_amount' => $discountAmount,
        ]);
    }
}

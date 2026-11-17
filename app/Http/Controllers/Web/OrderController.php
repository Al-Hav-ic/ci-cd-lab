<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Веб-CRUD замовлень.
 *
 * Демонструє обидва типи зв'язків через HTML-форми:
 *   1:M — покупець (select), M:M — знижки (група чекбоксів + sync()).
 */
class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with(['customer.user:id,name', 'discounts:id,code,percent'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('manage.orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        $order->load(['customer.user:id,name,email', 'items.product:id,name', 'payment', 'discounts']);

        return view('manage.orders.show', ['order' => $order]);
    }

    public function create(): View
    {
        $customers = Customer::query()->with('user:id,name')->get();
        $products = Product::query()->where('is_active', true)->orderBy('name')->get();
        $discounts = Discount::query()->where('is_active', true)->orderBy('code')->get();

        return view('manage.orders.create', [
            'customers' => $customers,
            'products' => $products,
            'discounts' => $discounts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
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

            $product = Product::findOrFail((int) $validated['product_id']);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => (int) $validated['quantity'],
                'unit_price' => $product->price,
            ]);

            // Синхронізація зв'язку M:M з проміжною таблицею discount_order
            $order->discounts()->sync($validated['discount_ids'] ?? []);
            $this->recalculateTotals($order);

            return $order;
        });

        return redirect()
            ->route('manage.orders.show', $order->id)
            ->with('success', 'Замовлення '.$order->number.' успішно створено!');
    }

    public function edit(Order $order): View
    {
        $order->load(['items.product:id,name', 'discounts:id,code,percent']);
        $customers = Customer::query()->with('user:id,name')->get();
        $discounts = Discount::query()->orderBy('code')->get();

        return view('manage.orders.edit', [
            'order' => $order,
            'customers' => $customers,
            'discounts' => $discounts,
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'discount_ids' => ['nullable', 'array'],
            'discount_ids.*' => ['integer', 'exists:discounts,id'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:pending,paid,shipped,completed,cancelled'],
        ]);

        DB::transaction(function () use ($order, $validated): void {
            $order->update([
                'customer_id' => $validated['customer_id'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'status' => $validated['status'],
            ]);

            $order->discounts()->sync($validated['discount_ids'] ?? []);
            $this->recalculateTotals($order);
        });

        return redirect()
            ->route('manage.orders.show', $order->id)
            ->with('success', 'Замовлення успішно оновлено!');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->discounts()->detach();
        $order->items()->delete();
        $order->delete();

        return redirect()
            ->route('manage.orders.index')
            ->with('success', 'Замовлення успішно видалено!');
    }

    /**
     * Перерахунок підсумків: сума позицій мінус сумарний відсоток знижок (M:M).
     */
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

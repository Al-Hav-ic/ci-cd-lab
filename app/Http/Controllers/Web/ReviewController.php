<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Веб-CRUD відгуків (Review: два зв'язки 1:M — Product і Customer).
 */
class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::query()
            ->with(['product:id,name', 'customer.user:id,name'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('manage.reviews.index', ['reviews' => $reviews]);
    }

    public function create(): View
    {
        $products = Product::query()->orderBy('name')->get();
        $customers = Customer::query()->with('user:id,name')->get();

        return view('manage.reviews.create', ['products' => $products, 'customers' => $customers]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'text' => ['required', 'string', 'min:10', 'max:1000'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        $validated['is_approved'] = $request->boolean('is_approved');

        Review::create($validated);

        return redirect()
            ->route('manage.reviews.index')
            ->with('success', 'Відгук успішно додано!');
    }

    public function edit(Review $review): View
    {
        $products = Product::query()->orderBy('name')->get();
        $customers = Customer::query()->with('user:id,name')->get();

        return view('manage.reviews.edit', ['review' => $review, 'products' => $products, 'customers' => $customers]);
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'text' => ['required', 'string', 'min:10', 'max:1000'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        $validated['is_approved'] = $request->boolean('is_approved');

        $review->update($validated);

        return redirect()
            ->route('manage.reviews.index')
            ->with('success', 'Відгук успішно оновлено!');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()
            ->route('manage.reviews.index')
            ->with('success', 'Відгук успішно видалено!');
    }
}

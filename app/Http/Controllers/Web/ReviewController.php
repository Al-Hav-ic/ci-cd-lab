<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Веб-CRUD відгуків (Review: два зв'язки 1:M — Product і Customer).
 *
 * Модерація (зауваження рев'ю лаб. 7):
 *   - новий відгук завжди створюється зі статусом «на модерації»
 *     (is_approved = false), незалежно від того, що надіслав клієнт;
 *   - змінювати статус схвалення може лише користувач із роллю
 *     admin/moderator (User::isModerator()).
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
        ]);

        // Унікальність (product_id, customer_id) перевіряємо валідацією,
        // інакше БД повертає 500 замість повідомлення про помилку.
        $this->ensureUniqueReview((int) $validated['product_id'], (int) $validated['customer_id']);

        // Новий відгук завжди потрапляє на модерацію.
        $validated['is_approved'] = false;

        Review::create($validated);

        return redirect()
            ->route('manage.reviews.index')
            ->with('success', 'Відгук додано та відправлено на модерацію!');
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
        ]);

        $this->ensureUniqueReview(
            (int) $validated['product_id'],
            (int) $validated['customer_id'],
            $review->id,
        );

        // Схвалювати відгук може лише модератор; решта користувачів
        // не можуть змінити статус навіть підмінивши поле у формі.
        $validated['is_approved'] = $request->user()?->isModerator()
            ? $request->boolean('is_approved')
            : $review->is_approved;

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

    /**
     * Перевірка складеної унікальності reviews(product_id, customer_id).
     *
     * @throws ValidationException
     */
    private function ensureUniqueReview(int $productId, int $customerId, ?int $ignoreId = null): void
    {
        $exists = Review::query()
            ->where('product_id', $productId)
            ->where('customer_id', $customerId)
            ->when($ignoreId !== null, static fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'customer_id' => 'Цей покупець уже залишив відгук про обраний товар.',
            ]);
        }
    }
}

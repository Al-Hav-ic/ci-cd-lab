<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Веб-CRUD знижок (M:M до Order через проміжну таблицю discount_order).
 */
class DiscountController extends Controller
{
    public function index(): View
    {
        $discounts = Discount::query()
            ->withCount('orders')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('manage.discounts.index', ['discounts' => $discounts]);
    }

    public function create(): View
    {
        return view('manage.discounts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32', 'unique:discounts,code'],
            'percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'valid_from' => ['required', 'date'],
            'valid_to' => ['required', 'date', 'after_or_equal:valid_from'],
            'usage_limit' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Discount::create($validated);

        return redirect()
            ->route('manage.discounts.index')
            ->with('success', 'Знижку успішно створено!');
    }

    public function edit(Discount $discount): View
    {
        return view('manage.discounts.edit', ['discount' => $discount]);
    }

    public function update(Request $request, Discount $discount): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32', Rule::unique('discounts', 'code')->ignore($discount->id)],
            'percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'valid_from' => ['required', 'date'],
            'valid_to' => ['required', 'date', 'after_or_equal:valid_from'],
            'usage_limit' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $discount->update($validated);

        return redirect()
            ->route('manage.discounts.index')
            ->with('success', 'Знижку успішно оновлено!');
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        // Прибираємо зв'язки M:M у проміжній таблиці перед видаленням.
        $discount->orders()->detach();
        $discount->delete();

        return redirect()
            ->route('manage.discounts.index')
            ->with('success', 'Знижку успішно видалено!');
    }
}

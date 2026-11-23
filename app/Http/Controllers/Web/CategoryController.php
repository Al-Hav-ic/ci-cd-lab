<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Веб-CRUD довідника категорій (1:M до Product).
 */
class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->paginate(10);

        return view('manage.categories.index', ['categories' => $categories]);
    }

    public function create(): View
    {
        return view('manage.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        Category::create($validated);

        return redirect()
            ->route('manage.categories.index')
            ->with('success', 'Категорію успішно створено!');
    }

    public function edit(Category $category): View
    {
        return view('manage.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()
            ->route('manage.categories.index')
            ->with('success', 'Категорію успішно оновлено!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Цілісність: не даємо видалити категорію, у якої є товари (1:M).
        if ($category->products()->exists()) {
            return redirect()
                ->route('manage.categories.index')
                ->with('error', 'Не можна видалити категорію, що містить товари. Спочатку перемістіть або видаліть їх.');
        }

        $category->delete();

        return redirect()
            ->route('manage.categories.index')
            ->with('success', 'Категорію успішно видалено!');
    }
}

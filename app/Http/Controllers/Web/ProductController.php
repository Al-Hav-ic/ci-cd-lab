<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Веб-CRUD головної сутності Product (лаб. 7).
 *
 * На відміну від API-контролера лаб. 5, методи повертають Blade-представлення:
 * index/show — сторінки каталогу, create/edit — HTML-форми з CSRF-захистом.
 */
class ProductController extends Controller
{
    public function index(): View
    {
        // Жадібне завантаження зв'язку 1:M, реалізованого в лаб. 6.
        $products = Product::query()
            ->with(['category:id,name', 'reviews:id,product_id,rating,text'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('shop.index', ['products' => $products]);
    }

    public function show(Product $product): View
    {
        $product->load(['category:id,name', 'reviews.customer.user:id,name']);

        return view('shop.show', ['product' => $product]);
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('shop.create', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'sku' => ['required', 'string', 'max:64', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Запис прив'язується до поточного користувача — власник для ProductPolicy.
        $validated['user_id'] = auth()->id();

        Product::create($validated);

        return redirect()
            ->route('shop.index')
            ->with('success', 'Товар успішно створено!');
    }

    public function edit(Product $product): View
    {
        // Політика: редагувати дозволено лише власнику запису (інакше — 403).
        $this->authorize('update', $product);

        $categories = Category::query()->orderBy('name')->get();

        return view('shop.edit', ['product' => $product, 'categories' => $categories]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            // ignore(): власний поточний SKU не вважається конфліктом унікальності
            'sku' => ['required', 'string', 'max:64', Rule::unique('products', 'sku')->ignore($product->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $product->update($validated);

        return redirect()
            ->route('shop.index')
            ->with('success', 'Товар успішно оновлено!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()
            ->route('shop.index')
            ->with('success', 'Товар успішно видалено!');
    }
}

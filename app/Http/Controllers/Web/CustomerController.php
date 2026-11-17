<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Веб-CRUD покупців (Customer 1:1 User з лаб. 6).
 */
class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()
            ->with('user:id,name,email')
            ->withCount('orders')
            ->orderBy('id')
            ->paginate(10);

        return view('manage.customers.index', ['customers' => $customers]);
    }

    public function create(): View
    {
        $users = User::query()->orderBy('name')->get();

        return view('manage.customers.create', ['users' => $users]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:customers,user_id'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:255'],
            'bonus_points' => ['required', 'integer', 'min:0'],
            'loyalty_level' => ['required', 'in:basic,silver,gold'],
            'newsletter_opt_in' => ['nullable', 'boolean'],
        ]);

        $validated['newsletter_opt_in'] = $request->boolean('newsletter_opt_in');

        Customer::create($validated);

        return redirect()
            ->route('manage.customers.index')
            ->with('success', 'Покупця успішно створено!');
    }

    public function edit(Customer $customer): View
    {
        $users = User::query()->orderBy('name')->get();

        return view('manage.customers.edit', ['customer' => $customer, 'users' => $users]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => [
                'required', 'integer', 'exists:users,id',
                'unique:customers,user_id,'.$customer->id,
            ],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:255'],
            'bonus_points' => ['required', 'integer', 'min:0'],
            'loyalty_level' => ['required', 'in:basic,silver,gold'],
            'newsletter_opt_in' => ['nullable', 'boolean'],
        ]);

        $validated['newsletter_opt_in'] = $request->boolean('newsletter_opt_in');

        $customer->update($validated);

        return redirect()
            ->route('manage.customers.index')
            ->with('success', 'Покупця успішно оновлено!');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->orders()->exists()) {
            return redirect()
                ->route('manage.customers.index')
                ->with('error', 'Не можна видалити покупця, що має замовлення.');
        }

        $customer->delete();

        return redirect()
            ->route('manage.customers.index')
            ->with('success', 'Покупця успішно видалено!');
    }
}

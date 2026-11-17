<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Сесійна автентифікація вебінтерфейсу (лаб. 8).
 *
 * На відміну від Api\AuthController (токени Sanctum), тут стан користувача
 * зберігається у серверній сесії; вхід захищено від Session Fixation
 * через regenerate(), вихід — через invalidate() + regenerateToken().
 */
class WebAuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Регенерація ID сесії для захисту від атаки Session Fixation.
            $request->session()->regenerate();

            return redirect()
                ->intended(route('shop.index'))
                ->with('success', 'Ви успішно увійшли в систему!');
        }

        return back()
            ->withErrors(['email' => 'Надані облікові дані не збігаються з нашими записами.'])
            ->onlyInput('email');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('shop.index')
            ->with('success', 'Реєстрацію успішно завершено!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'Ви вийшли із системи.');
    }
}

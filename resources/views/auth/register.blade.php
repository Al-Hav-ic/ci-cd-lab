@extends('layouts.app')

@section('title', 'Реєстрація')

@section('content')
    <div class="card mx-auto shadow-sm" style="max-width: 450px;">
        <div class="card-header bg-white">
            <h3 class="mb-0">Реєстрація нового користувача</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <x-form.input name="name" label="Ім'я" required autofocus />
                <x-form.input name="email" label="Електронна пошта" type="email" required />
                <x-form.input name="password" label="Пароль (мін. 8 символів)" type="password" required />
                <x-form.input name="password_confirmation" label="Підтвердження пароля" type="password" required />
                <button type="submit" class="btn btn-primary w-100">Зареєструватися</button>
            </form>
            <p class="text-center text-muted small mt-3 mb-0">
                Вже маєте акаунт? <a href="{{ route('login') }}">Увійти</a>
            </p>
        </div>
    </div>
@endsection

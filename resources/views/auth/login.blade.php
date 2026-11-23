@extends('layouts.app')

@section('title', 'Вхід у систему')

@section('content')
    <div class="card mx-auto shadow-sm" style="max-width: 450px;">
        <div class="card-header bg-white">
            <h3 class="mb-0">Вхід у систему</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <x-form.input name="email" label="Електронна пошта" type="email" required autofocus />
                <x-form.input name="password" label="Пароль" type="password" required />
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Запам'ятати мене</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Увійти</button>
            </form>
            <p class="text-center text-muted small mt-3 mb-0">
                Немає облікового запису? <a href="{{ route('register') }}">Зареєструватися</a>
            </p>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Додати покупця')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white"><h2 class="h4 mb-0">Новий покупець</h2></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif
            <form action="{{ route('manage.customers.store') }}" method="POST">
                @csrf
                <!-- Зв'язок 1:1 з User: вибір користувача -->
                <div class="mb-3">
                    <label for="user_id" class="form-label">Користувач (1:1)</label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                        <option value="">-- Оберіть користувача --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <x-form.input name="phone" label="Телефон"/>
                <x-form.input name="address" label="Адреса доставки"/>
                <x-form.input name="bonus_points" label="Бонусні бали" type="number" value="0"/>
                <div class="mb-3">
                    <label for="loyalty_level" class="form-label">Рівень лояльності</label>
                    <select name="loyalty_level" id="loyalty_level" class="form-select @error('loyalty_level') is-invalid @enderror">
                        <option value="basic" {{ old('loyalty_level') === 'basic' ? 'selected' : '' }}>basic</option>
                        <option value="silver" {{ old('loyalty_level') === 'silver' ? 'selected' : '' }}>silver</option>
                        <option value="gold" {{ old('loyalty_level') === 'gold' ? 'selected' : '' }}>gold</option>
                    </select>
                    @error('loyalty_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="newsletter_opt_in" id="newsletter_opt_in" value="1"
                           {{ old('newsletter_opt_in') ? 'checked' : '' }}>
                    <label class="form-check-label" for="newsletter_opt_in">Підписаний на розсилку</label>
                </div>
                <button type="submit" class="btn btn-success mt-3">Зберегти</button>
                <a href="{{ route('manage.customers.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection

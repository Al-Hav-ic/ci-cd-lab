@extends('layouts.app')

@section('title', 'Редагувати покупця')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white"><h2 class="h4 mb-0">Покупець #{{ $customer->id }}</h2></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif
            <form action="{{ route('manage.customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="user_id" class="form-label">Користувач (1:1)</label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                        <option value="">-- Оберіть користувача --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $customer->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <x-form.input name="phone" label="Телефон" :value="$customer->phone"/>
                <x-form.input name="address" label="Адреса доставки" :value="$customer->address"/>
                <x-form.input name="bonus_points" label="Бонусні бали" type="number" :value="$customer->bonus_points"/>
                <div class="mb-3">
                    <label for="loyalty_level" class="form-label">Рівень лояльності</label>
                    <select name="loyalty_level" id="loyalty_level" class="form-select @error('loyalty_level') is-invalid @enderror">
                        <option value="basic" {{ old('loyalty_level', $customer->loyalty_level) === 'basic' ? 'selected' : '' }}>basic</option>
                        <option value="silver" {{ old('loyalty_level', $customer->loyalty_level) === 'silver' ? 'selected' : '' }}>silver</option>
                        <option value="gold" {{ old('loyalty_level', $customer->loyalty_level) === 'gold' ? 'selected' : '' }}>gold</option>
                    </select>
                    @error('loyalty_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="newsletter_opt_in" id="newsletter_opt_in" value="1"
                           {{ old('newsletter_opt_in', $customer->newsletter_opt_in) ? 'checked' : '' }}>
                    <label class="form-check-label" for="newsletter_opt_in">Підписаний на розсилку</label>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Оновити</button>
                <a href="{{ route('manage.customers.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection

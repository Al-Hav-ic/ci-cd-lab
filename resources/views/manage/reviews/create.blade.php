@extends('layouts.app')

@section('title', 'Додати відгук')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white"><h2 class="h4 mb-0">Новий відгук</h2></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif
            <form action="{{ route('manage.reviews.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="product_id" class="form-label">Товар (1:M)</label>
                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror">
                        <option value="">-- Оберіть товар --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Покупець (1:M)</label>
                    <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                        <option value="">-- Оберіть покупця --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->user?->name ?? 'Customer #'.$customer->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <x-form.input name="rating" label="Оцінка (1–5)" type="number" min="1" max="5"/>
                <div class="mb-3">
                    <label for="text" class="form-label">Текст відгуку</label>
                    <textarea name="text" id="text" rows="3"
                              class="form-control @error('text') is-invalid @enderror">{{ old('text') }}</textarea>
                    @error('text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="alert alert-info">
                    Новий відгук буде опубліковано після перевірки модератором:
                    статус схвалення встановлюється автоматично.
                </div>
                <button type="submit" class="btn btn-success mt-3">Зберегти</button>
                <a href="{{ route('manage.reviews.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection

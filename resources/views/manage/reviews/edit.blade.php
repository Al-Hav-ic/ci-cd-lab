@extends('layouts.app')

@section('title', 'Редагувати відгук')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white"><h2 class="h4 mb-0">Відгук #{{ $review->id }}</h2></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif
            <form action="{{ route('manage.reviews.update', $review->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="product_id" class="form-label">Товар (1:M)</label>
                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror">
                        <option value="">-- Оберіть товар --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                    {{ old('product_id', $review->product_id) == $product->id ? 'selected' : '' }}>
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
                            <option value="{{ $customer->id }}"
                                    {{ old('customer_id', $review->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->user?->name ?? 'Customer #'.$customer->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <x-form.input name="rating" label="Оцінка (1–5)" type="number" min="1" max="5" :value="$review->rating"/>
                <div class="mb-3">
                    <label for="text" class="form-label">Текст відгуку</label>
                    <textarea name="text" id="text" rows="3"
                              class="form-control @error('text') is-invalid @enderror">{{ old('text', $review->text) }}</textarea>
                    @error('text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_approved" id="is_approved" value="1"
                           {{ old('is_approved', $review->is_approved) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_approved">Схвалено модератором</label>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Оновити</button>
                <a href="{{ route('manage.reviews.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Редагувати товар')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h2 class="h4 mb-0">Редагування товару #{{ $product->id }}</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif

            <form action="{{ route('shop.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')
                <x-form.input name="name" label="Назва товару" :value="$product->name" required/>
                <x-form.input name="sku" label="Артикул / Код (SKU)" :value="$product->sku" required/>
                <x-form.input name="price" label="Ціна, грн" type="number" step="0.01" :value="$product->price"/>
                <x-form.input name="stock_qty" label="Залишок, шт." type="number" :value="$product->stock_qty"/>

                <!-- Вибір категорії 1:M -->
                <div class="mb-3">
                    <label for="category_id" class="form-label">Категорія</label>
                    <select name="category_id" id="category_id"
                            class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">-- Оберіть категорію --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Опис</label>
                    <textarea name="description" id="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Товар активний</label>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Оновити</button>
                <a href="{{ route('shop.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Нове замовлення')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white"><h2 class="h4 mb-0">Створення замовлення</h2></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif

            <form action="{{ route('manage.orders.store') }}" method="POST">
                @csrf
                <!-- Зв'язок 1:M: покупець -->
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Покупець (1:M)</label>
                    <select name="customer_id" id="customer_id"
                            class="form-select @error('customer_id') is-invalid @enderror">
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

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="product_id" class="form-label">Товар (перша позиція)</label>
                        <select name="product_id" id="product_id"
                                class="form-select @error('product_id') is-invalid @enderror">
                            <option value="">-- Оберіть товар --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} — {{ number_format($product->price, 2) }} грн
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="quantity" class="form-label">Кількість</label>
                        <input type="number" name="quantity" id="quantity" min="1" max="99" value="{{ old('quantity', 1) }}"
                               class="form-control @error('quantity') is-invalid @enderror">
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Зв'язок M:M: знижки (група чекбоксів) -->
                <div class="mb-3">
                    <label class="form-label d-block">Знижки (M:M — проміжна таблиця discount_order)</label>
                    @forelse ($discounts as $discount)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="discount_ids[]"
                                   id="discount_{{ $discount->id }}" value="{{ $discount->id }}"
                                   {{ in_array($discount->id, old('discount_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="discount_{{ $discount->id }}">
                                {{ $discount->code }} ({{ $discount->percent }}%)
                            </label>
                        </div>
                    @empty
                        <span class="text-muted small">Активних знижок немає.</span>
                    @endforelse
                    @error('discount_ids')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <x-form.input name="delivery_address" label="Адреса доставки"/>

                <button type="submit" class="btn btn-success mt-3">Зберегти</button>
                <a href="{{ route('manage.orders.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection

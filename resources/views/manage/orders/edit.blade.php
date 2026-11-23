@extends('layouts.app')

@section('title', 'Редагувати замовлення')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white"><h2 class="h4 mb-0">Редагування замовлення {{ $order->number }}</h2></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif

            <form action="{{ route('manage.orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Зв'язок 1:M: покупець -->
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Покупець (1:M)</label>
                    <select name="customer_id" id="customer_id"
                            class="form-select @error('customer_id') is-invalid @enderror">
                        <option value="">-- Оберіть покупця --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                    {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->user?->name ?? 'Customer #'.$customer->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Зв'язок M:M: знижки (поточні відмічені через pluck('id')) -->
                <div class="mb-3">
                    <label class="form-label d-block">Знижки (M:M)</label>
                    @foreach ($discounts as $discount)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="discount_ids[]"
                                   id="discount_{{ $discount->id }}" value="{{ $discount->id }}"
                                   {{ in_array($discount->id, old('discount_ids', $order->discounts->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label class="form-check-label" for="discount_{{ $discount->id }}">
                                {{ $discount->code }} ({{ $discount->percent }}%)
                            </label>
                        </div>
                    @endforeach
                    @error('discount_ids')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <x-form.input name="delivery_address" label="Адреса доставки" :value="$order->delivery_address"/>

                <div class="mb-3">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach (['pending', 'paid', 'shipped', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ old('status', $order->status) === $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary mt-3">Оновити</button>
                <a href="{{ route('manage.orders.show', $order->id) }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection

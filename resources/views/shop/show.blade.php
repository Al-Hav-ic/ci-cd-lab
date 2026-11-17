@extends('layouts.app')

@section('title', 'Товар: '.$product->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ $product->name }}</h1>
        <div>
            <a href="{{ route('shop.edit', $product->id) }}" class="btn btn-warning">Редагувати</a>
            <a href="{{ route('shop.index') }}" class="btn btn-secondary">До каталогу</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Основні властивості</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Артикул (SKU)</span><span class="fw-bold">{{ $product->sku }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Категорія (1:M)</span><span>{{ $product->category->name ?? 'Немає' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Ціна</span><span>{{ number_format($product->price, 2) }} грн</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Залишок на полицях</span><span>{{ $product->stock_qty }} шт.</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Статус</span>
                        <span>{{ $product->is_active ? 'активний' : 'приховано' }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Відгуки покупців (1:M)</div>
                <ul class="list-group list-group-flush">
                    @forelse ($product->reviews as $review)
                        <li class="list-group-item">
                            <span class="badge bg-warning text-dark">{{ $review->rating }}/5</span>
                            {{ $review->text }}
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Відгуків ще немає.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white fw-bold">Опис</div>
        <div class="card-body">{{ $product->description ?? 'Опис відсутній.' }}</div>
    </div>
@endsection

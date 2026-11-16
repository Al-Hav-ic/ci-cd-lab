@extends('layouts.app')

@section('title', 'Каталог товарів')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Каталог товарів</h1>
        <a href="{{ route('shop.create') }}" class="btn btn-primary">Додати новий запис</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Назва</th>
            <th>Категорія</th>
            <th>Ціна, грн</th>
            <th>Залишок</th>
            <th>Статус</th>
            <th>Дії</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                {{-- Жадібне завантаження: категорія отримана одним додатковим запитом --}}
                <td>{{ $product->category->name ?? 'Немає' }}</td>
                <td>{{ number_format($product->price, 2) }}</td>
                <td>{{ $product->stock_qty }}</td>
                <td>
                    @if ($product->is_active)
                        <span class="badge bg-success">активний</span>
                    @else
                        <span class="badge bg-secondary">приховано</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('shop.show', $product->id) }}" class="btn btn-sm btn-info">Перегляд</a>
                    <a href="{{ route('shop.edit', $product->id) }}" class="btn btn-sm btn-warning">Редаг.</a>
                    <form action="{{ route('shop.destroy', $product->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Дійсно видалити?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Видалити</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Записів не знайдено.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $products->links('pagination::bootstrap-5') }}
@endsection

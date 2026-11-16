@extends('layouts.app')

@section('title', 'Категорії')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Категорії</h1>
        <a href="{{ route('manage.categories.create') }}" class="btn btn-primary">Додати категорію</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Назва</th>
            <th>Товарів (1:M)</th>
            <th>Статус</th>
            <th>Дії</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->products_count }}</td>
                <td>{{ $category->is_active ? 'активна' : 'приховано' }}</td>
                <td>
                    <a href="{{ route('manage.categories.edit', $category->id) }}" class="btn btn-sm btn-warning">Редаг.</a>
                    <form action="{{ route('manage.categories.destroy', $category->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Дійсно видалити?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Видалити</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center">Записів не знайдено.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $categories->links('pagination::bootstrap-5') }}
@endsection

@extends('layouts.app')

@section('title', 'Відгуки')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Відгуки</h1>
        <a href="{{ route('manage.reviews.create') }}" class="btn btn-primary">Додати відгук</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Товар (1:M)</th>
            <th>Покупець (1:M)</th>
            <th>Оцінка</th>
            <th>Текст</th>
            <th>Дії</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($reviews as $review)
            <tr>
                <td>{{ $review->id }}</td>
                <td>{{ $review->product?->name ?? '—' }}</td>
                <td>{{ $review->customer?->user?->name ?? '—' }}</td>
                <td><span class="badge bg-warning text-dark">{{ $review->rating }}/5</span></td>
                <td class="small">{{ \Illuminate\Support\Str::limit($review->text, 60) }}</td>
                <td>
                    <a href="{{ route('manage.reviews.edit', $review->id) }}" class="btn btn-sm btn-warning">Редаг.</a>
                    <form action="{{ route('manage.reviews.destroy', $review->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Дійсно видалити?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Видалити</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">Записів не знайдено.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $reviews->links('pagination::bootstrap-5') }}
@endsection

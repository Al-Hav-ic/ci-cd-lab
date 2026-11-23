@extends('layouts.app')

@section('title', 'Знижки')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Знижки</h1>
        <a href="{{ route('manage.discounts.create') }}" class="btn btn-primary">Додати знижку</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Код</th>
            <th>%,</th>
            <th>Діє з / до</th>
            <th>Ліміт / Використано</th>
            <th>Замовлень (M:M)</th>
            <th>Дії</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($discounts as $discount)
            <tr>
                <td>{{ $discount->id }}</td>
                <td><span class="badge bg-secondary">{{ $discount->code }}</span></td>
                <td>{{ $discount->percent }}%</td>
                <td>{{ $discount->valid_from?->format('d.m.Y') }} — {{ $discount->valid_to?->format('d.m.Y') }}</td>
                <td>{{ $discount->used_count }} / {{ $discount->usage_limit }}</td>
                <td>{{ $discount->orders_count }}</td>
                <td>
                    <a href="{{ route('manage.discounts.edit', $discount->id) }}" class="btn btn-sm btn-warning">Редаг.</a>
                    <form action="{{ route('manage.discounts.destroy', $discount->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Дійсно видалити?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Видалити</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center">Записів не знайдено.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $discounts->links('pagination::bootstrap-5') }}
@endsection

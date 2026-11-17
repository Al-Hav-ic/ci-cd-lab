@extends('layouts.app')

@section('title', 'Покупці')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Покупці</h1>
        <a href="{{ route('manage.customers.create') }}" class="btn btn-primary">Додати покупця</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Користувач (1:1)</th>
            <th>Телефон</th>
            <th>Бонуси</th>
            <th>Рівень</th>
            <th>Замовлень</th>
            <th>Дії</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($customers as $customer)
            <tr>
                <td>{{ $customer->id }}</td>
                <td>{{ $customer->user->name ?? '—' }} <span class="text-muted small">{{ $customer->user->email ?? '' }}</span></td>
                <td>{{ $customer->phone ?? '—' }}</td>
                <td>{{ $customer->bonus_points }}</td>
                <td><span class="badge bg-info text-dark">{{ $customer->loyalty_level }}</span></td>
                <td>{{ $customer->orders_count }}</td>
                <td>
                    <a href="{{ route('manage.customers.edit', $customer->id) }}" class="btn btn-sm btn-warning">Редаг.</a>
                    <form action="{{ route('manage.customers.destroy', $customer->id) }}" method="POST" class="d-inline"
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

    {{ $customers->links('pagination::bootstrap-5') }}
@endsection

@extends('layouts.app')

@section('title', 'Замовлення')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Замовлення</h1>
        <a href="{{ route('manage.orders.create') }}" class="btn btn-primary">Нове замовлення</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
        <tr>
            <th>Номер</th>
            <th>Покупець (1:M)</th>
            <th>Знижки (M:M)</th>
            <th>Сума, грн</th>
            <th>Статус</th>
            <th>Дії</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($orders as $order)
            <tr>
                <td>{{ $order->number }}</td>
                <td>{{ $order->customer?->user?->name ?? '—' }}</td>
                <td>
                    @forelse ($order->discounts as $discount)
                        <span class="badge bg-secondary">{{ $discount->code }} {{ $discount->percent }}%</span>
                    @empty
                        <span class="text-muted small">—</span>
                    @endforelse
                </td>
                <td>{{ number_format($order->total_amount, 2) }}</td>
                <td><span class="badge bg-primary">{{ $order->status }}</span></td>
                <td>
                    <a href="{{ route('manage.orders.show', $order->id) }}" class="btn btn-sm btn-info">Перегляд</a>
                    <a href="{{ route('manage.orders.edit', $order->id) }}" class="btn btn-sm btn-warning">Редаг.</a>
                    <form action="{{ route('manage.orders.destroy', $order->id) }}" method="POST" class="d-inline"
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

    {{ $orders->links('pagination::bootstrap-5') }}
@endsection

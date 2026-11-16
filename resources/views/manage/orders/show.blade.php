@extends('layouts.app')

@section('title', 'Замовлення '.$order->number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Замовлення {{ $order->number }}</h1>
        <div>
            <a href="{{ route('manage.orders.edit', $order->id) }}" class="btn btn-warning">Редагувати</a>
            <a href="{{ route('manage.orders.index') }}" class="btn btn-secondary">До списку</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Параметри</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Покупець (1:M)</span><span>{{ $order->customer?->user?->name ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Статус</span><span class="badge bg-primary">{{ $order->status }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Адреса доставки</span><span>{{ $order->delivery_address ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Знижка (M:M)</span>
                        <span>
                            @forelse ($order->discounts as $discount)
                                {{ $discount->code }} ({{ $discount->percent }}%)
                            @empty
                                —
                            @endforelse
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Сума знижки</span><span>{{ number_format($order->discount_amount, 2) }} грн</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Разом до сплати</span><span class="fw-bold">{{ number_format($order->total_amount, 2) }} грн</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Позиції замовлення (композиція)</div>
                <table class="table mb-0">
                    <thead>
                    <tr><th>Товар</th><th>К-сть</th><th>Ціна</th><th>Сума</th></tr>
                    </thead>
                    <tbody>
                    @forelse ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Позицій немає.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

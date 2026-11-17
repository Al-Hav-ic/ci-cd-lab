@extends('layouts.app')

@section('title', '403 — Доступ заборонено')

@section('content')
    <div class="text-center py-5">
        <h1 class="display-1 fw-bold text-danger">403</h1>
        <h2 class="mb-3">Forbidden — доступ заборонено</h2>
        <p class="lead text-muted">
            У вас недостатньо прав для виконання цієї дії над записом.
            Операції зміни дозволені лише власнику (ProductPolicy).
        </p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary">Повернутися до каталогу</a>
    </div>
@endsection

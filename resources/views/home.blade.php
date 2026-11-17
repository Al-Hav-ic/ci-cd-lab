@extends('layouts.app')

@section('title', 'Головна сторінка')

@section('content')
    <div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
        <h1 class="display-5 fw-bold">Система продажу товарів у супермаркеті</h1>
        <p class="col-md-8 fs-4">
            Лабораторна робота №7: користувацький інтерфейс на шаблонізаторі Blade
            у режимі Server-Side Rendering. Каталог, категорії, покупці, знижки,
            замовлення та відгуки керуються через HTML-форми з CSRF-захистом.
        </p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg">Перейти до каталогу</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Каталог товарів</h5>
                    <p class="card-text">Головна сутність: Product. Зв'язок 1:M з категоріями.</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Відкрити</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Замовлення</h5>
                    <p class="card-text">1:M (покупець) та M:M (знижки) через форми.</p>
                    <a href="{{ route('manage.orders.index') }}" class="btn btn-outline-primary">Відкрити</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Довідники</h5>
                    <p class="card-text">Категорії, покупці, знижки, відгуки.</p>
                    <a href="{{ route('manage.categories.index') }}" class="btn btn-outline-primary">Відкрити</a>
                </div>
            </div>
        </div>
    </div>
@endsection

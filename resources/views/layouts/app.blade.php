<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Система продажу товарів у супермаркеті')</title>
    <!-- Bootstrap 5 через CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Супермаркет</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('shop.index') }}">Каталог</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('manage.categories.index') }}">Категорії</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('manage.customers.index') }}">Покупці</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('manage.discounts.index') }}">Знижки</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('manage.orders.index') }}">Замовлення</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('manage.reviews.index') }}">Відгуки</a></li>
            </ul>
            <span class="navbar-text small text-white-50">Blade · SSR · Лаб. 7</span>
        </div>
    </div>
</nav>
<div class="container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

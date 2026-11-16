@extends('layouts.app')

@section('title', 'Редагувати знижку')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white"><h2 class="h4 mb-0">Знижка #{{ $discount->id }}</h2></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif
            <form action="{{ route('manage.discounts.update', $discount->id) }}" method="POST">
                @csrf
                @method('PUT')
                <x-form.input name="code" label="Код" :value="$discount->code" required/>
                <x-form.input name="percent" label="Відсоток, %" type="number" step="0.01" :value="$discount->percent"/>
                <x-form.input name="valid_from" label="Діє з" type="date" :value="$discount->valid_from?->format('Y-m-d')"/>
                <x-form.input name="valid_to" label="Діє до" type="date" :value="$discount->valid_to?->format('Y-m-d')"/>
                <x-form.input name="usage_limit" label="Ліміт використань" type="number" :value="$discount->usage_limit"/>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $discount->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Активна</label>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Оновити</button>
                <a href="{{ route('manage.discounts.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection

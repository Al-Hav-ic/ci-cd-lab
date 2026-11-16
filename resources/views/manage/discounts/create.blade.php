@extends('layouts.app')

@section('title', 'Додати знижку')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white"><h2 class="h4 mb-0">Нова знижка</h2></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif
            <form action="{{ route('manage.discounts.store') }}" method="POST">
                @csrf
                <x-form.input name="code" label="Код (напр. SAVE-2026)" required/>
                <x-form.input name="percent" label="Відсоток, %" type="number" step="0.01"/>
                <x-form.input name="valid_from" label="Діє з" type="date"/>
                <x-form.input name="valid_to" label="Діє до" type="date"/>
                <x-form.input name="usage_limit" label="Ліміт використань" type="number"/>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Активна</label>
                </div>
                <button type="submit" class="btn btn-success mt-3">Зберегти</button>
                <a href="{{ route('manage.discounts.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Редагувати категорію')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white"><h2 class="h4 mb-0">Категорія #{{ $category->id }}</h2></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif
            <form action="{{ route('manage.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <x-form.input name="name" label="Назва категорії" :value="$category->name" required/>
                <div class="mb-3">
                    <label for="description" class="form-label">Опис</label>
                    <textarea name="description" id="description" rows="2"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Активна</label>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Оновити</button>
                <a href="{{ route('manage.categories.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection

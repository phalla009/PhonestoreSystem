@extends('layouts.master')

@section('pageTitle')
    KR System PhoneStore
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
@endsection

@section('content')
    <div class="modal-content" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
        <a href="{{ route('categories.index') }}" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-mobile-alt"></i> Edit Brand</h2>

        <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="name">Brand Name:</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $category->name) }}"
                        placeholder="Enter category name"
                    >
                    @error('name')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="description">Description:</label>
                    <textarea 
                        id="description"
                        name="description"
                        placeholder="Enter description"
                    >{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button class="btn btn-update" type="submit">
                    <i class="fas fa-save"></i> Update Brand
                </button>
            </div>
        </form>
    </div>
@endsection

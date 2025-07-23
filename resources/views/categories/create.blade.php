@extends('layouts.master')

@section('pageTitle')
    Add Category
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
@endsection

@section('content')
    @if(session('success'))
        <div id="successMessage" class="custom-success">
            <i class="fas fa-check-circle" style="color: green; margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="modal-content" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
      <a href="{{ route('categories.index') }}" class="btn btn-back">
             <i class="fas fa-chevron-left"></i> Back
     </a>
      <h2><i class="fas fa-tags"></i> Add New Category</h2>
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="name">Category Name:</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Enter category name">
                    @error('name')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="description">Description:</label>
                    <textarea style="width: 100%;" id="description" type="text" name="description" value="{{ old('description') }}" placeholder="Enter description"></textarea>
                </div>
            </div>
            <div style="text-align: right; margin-top: 1rem;">
               <button id="submitCategory" type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Add Category
                </button>
            </div>
            <div style="text-align: right; margin-top: 1rem;">
               <button id="cancel" type="button" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('cancel').addEventListener('click', function () {
        this.closest('form').reset();
    });
    </script>
@endsection

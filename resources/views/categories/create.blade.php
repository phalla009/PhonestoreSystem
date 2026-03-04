@extends('layouts.master')

@section('pageTitle')
    Add New Categories
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>

    <style>
        #loading-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.85);
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 99999;
        }
        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 60px; height: 60px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        #loading-text { margin-top: 15px; font-size: 16px; color: #333; }
    </style>
@endsection

@section('content')

    {{-- Loading Overlay --}}
    <div id="loading-overlay">
        <div class="spinner"></div>
        <div id="loading-text">Going back...</div>
    </div>

    @if(session('success'))
        <div id="successMessage" class="custom-success">
            <i class="fas fa-check-circle" style="color: green; margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="modal-content" role="dialog" aria-labelledby="modalTitle" aria-modal="true">

        {{-- Back Button → show loading then navigate --}}
        <a href="{{ route('categories.index') }}"
           id="backBtn"
           class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-mobile-alt"></i> Add New Brand</h2>

        <form action="{{ route('categories.store') }}" method="POST" id="categoryForm">
            @csrf
            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="name">Brand Name:</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Enter category name">
                    @error('name')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="description">Description:</label>
                    <textarea style="width: 100%;" id="description" name="description" placeholder="Enter description">{{ old('description') }}</textarea>
                </div>
            </div>
            <div style="text-align: right; margin-top: 1rem;">
                <button id="submitCategory" type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Add New Brand
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
        const overlay = document.getElementById('loading-overlay');

        // Back button → show loading then navigate
        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            overlay.style.display = 'flex';
            const href = this.getAttribute('href');
            window.location.href = href;
        });

        // Submit button → show loading then submit
        document.getElementById('categoryForm').addEventListener('submit', function() {
            document.getElementById('loading-text').textContent = 'Saving...';
            overlay.style.display = 'flex';
        });

        // Cancel → reset form only
        document.getElementById('cancel').addEventListener('click', function() {
            document.getElementById('categoryForm').reset();
        });
    </script>

@endsection
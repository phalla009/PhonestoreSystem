@extends('layouts.master')

@section('pageTitle')
   Edited Categories
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

    <div class="modal-content" role="dialog" aria-labelledby="modalTitle" aria-modal="true">

        {{-- FIX: បន្ថែម id="backBtn" --}}
        <a href="{{ route('categories.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-mobile-alt"></i> Edit Brand</h2>

        {{-- FIX: បន្ថែម id="categoryForm" --}}
        <form id="categoryForm" action="{{ route('categories.update', $category->id) }}" method="POST">
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

    <script>
        const overlay = document.getElementById('loading-overlay');

        // Back button → show loading then navigate
        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            overlay.style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });

        // Submit form → show loading then submit
        document.getElementById('categoryForm').addEventListener('submit', function() {
            document.getElementById('loading-text').textContent = 'Updating...';
            overlay.style.display = 'flex';
        });
    </script>

@endsection
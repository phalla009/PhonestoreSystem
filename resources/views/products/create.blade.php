@extends('layouts.master')

@section('pageTitle')
    Add New Product
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
        <div id="loading-text">Loading...</div>
    </div>

    @if(session('success'))
        <div id="successMessage" class="custom-success">
            <div class="success-content">
                <span class="success-icon">✔</span>
                <span class="success-text">{{ session('success') }}</span>
            </div>
            <div class="progress-bar"></div>
        </div>
    @endif

    <div class="modal-content" role="main">

        {{-- Back Button --}}
        <a href="{{ route('products.index') }}" id="backBtn" class="btn btn-back" aria-label="Back to product list">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-box-open"></i> Add New Product</h2>

        <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <!-- Product Name & Brand -->
            <div class="form-row" style="display: flex; gap: 20px; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 250px;">
                    <label for="name">Product Name:</label>
                    <input id="name" type="text" name="name" placeholder="Enter product name" value="{{ old('name') }}">
                    @error('name')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group" style="flex: 1; min-width: 250px;">
                    <label for="category_id">Brand:</label>
                    <select id="category_id" name="category_id">
                        <option value="">Select Brand</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Price & Stock -->
            <div class="form-row" style="display: flex; gap: 20px; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 250px;">
                    <label for="price">Price:</label>
                    <input id="price" type="number" name="price" step="0.01" value="{{ old('price') }}">
                    @error('price')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group" style="flex: 1; min-width: 250px;">
                    <label for="stock">Stock:</label>
                    <input id="stock" type="number" name="stock" value="{{ old('stock') }}">
                    @error('stock')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Images & Status -->
            <div class="form-row" style="display: flex; gap: 20px; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 250px;">
                    <label for="images">Product Images:</label>
                    <input id="images" type="file" name="images[]" accept="image/*" multiple>
                    @error('images.*')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group" style="flex: 1; min-width: 250px;">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="">Select Status</option>
                        <option value="active"   {{ old('status') == 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Add to POS -->
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input id="add_to_pos" type="checkbox" name="add_to_pos" value="1"
                        {{ old('add_to_pos') ? 'checked' : '' }}
                        style="width: 18px; height: 18px; cursor: pointer; accent-color: #3498db;">
                    <span>Add to POS</span>
                </label>
                @error('add_to_pos')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" placeholder="Enter description"
                        style="height: 180px; resize: none;">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div style="text-align: right; margin-top: 0.6rem;">
                <button class="btn btn-success" type="submit" aria-label="Add Product">
                    <i class="fas fa-save"></i> Add Product
                </button>
                <button id="cancel" type="button" class="btn btn-cancel" aria-label="Cancel and reset form">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>

    <script>
        const overlay     = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        // Back button → show loading then navigate
        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            loadingText.textContent = 'Going back...';
            overlay.style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });

        // Submit form → show loading
        document.getElementById('productForm').addEventListener('submit', function() {
            loadingText.textContent = 'Saving...';
            overlay.style.display = 'flex';
        });

        // Cancel → reset form only
        document.getElementById('cancel').addEventListener('click', function() {
            document.getElementById('productForm').reset();
        });
    </script>

@endsection
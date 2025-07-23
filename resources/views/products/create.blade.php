@extends('layouts.master')

@section('pageTitle')
    Add Product
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
@endsection

@section('content')
    @if(session('success'))
        <div id="successMessage" class="custom-success" role="alert" aria-live="polite">
            <i class="fas fa-check-circle" style="color: green; margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="modal-content" role="main">
        <a href="{{ route('products.index') }}" class="btn btn-back" aria-label="Back to product list">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2><i class="fas fa-box-open"></i> Add New Product</h2>

        <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="form-group">
                <label for="name">Product Name:</label>
                <input id="name" type="text" name="name" placeholder="Enter product name" value="{{ old('name') }}">
                @error('name')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
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

            <div class="form-group">
                <label for="price">Price:</label>
                <input id="price" type="number" name="price" step="0.01" value="{{ old('price') }}">
                @error('price')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="stock">Stock:</label>
                <input id="stock" type="number" name="stock" value="{{ old('stock') }}">
                @error('stock')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

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
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" placeholder="Enter description">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div style="text-align: right; margin-top: 1rem;">
                <button class="btn btn-success" type="submit" aria-label="Add Product">
                    <i class="fas fa-save"></i> Add Product
                </button>
                <button id="cancel" type="button" class="btn btn-cancel" aria-label="Cancel and reset form">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>

        <script>
            document.getElementById('cancel').addEventListener('click', function () {
                document.getElementById('productForm').reset();
            });
        </script>
    </div>
@endsection

@extends('layouts.master')

@section('pageTitle')
    Edit Product
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        /* Optional styling for the image and remove checkbox */
        .image-wrapper {
            position: relative;
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 15px;
            text-align: center;
        }
        .image-wrapper img {
            border-radius: 8px;
            object-fit: cover;
            width: 120px;
            height: 100px;
            display: block;
            margin-bottom: 5px;
        }
        .image-wrapper label {
            cursor: pointer;
            font-size: 0.9rem;
            color: #d9534f; /* Bootstrap danger color */
            user-select: none;
        }
        .image-wrapper input[type="checkbox"] {
            margin-right: 5px;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
<div class="modal-content">
    <a href="{{ route('products.index') }}" class="btn btn-back">
        <i class="fas fa-chevron-left"></i> Back
    </a>

    <h2><i class="fas fa-box-open"></i> Edit Product</h2>

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Product Name:</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}">
            @error('name')
                <p class="text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>Brand:</label>
            <select name="category_id">
                <option value="">Select Brand</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>Price:</label>
            <input type="number" name="price" step="0.01" value="{{ old('price', $product->price) }}">
            @error('price')
                <p class="text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>Stock:</label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}">
            @error('stock')
                <p class="text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label>Current Images:</label>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($product->images as $image)
                    <div class="image-wrapper">
                        <img src="{{ asset('images/products/' . $image->image) }}" alt="Product Image">
                        <label>
                            <input type="checkbox" name="delete_images[]" value="{{ $image->id }}">
                            Remove
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-row" style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div class="form-group">
                <label>Add More Images:</label>
                <input type="file" name="images[]" multiple accept="image/*">
                @error('images')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Status:</label>
                <select name="status">
                    <option value="">Select Status</option>
                    <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>   
        <div class="form-group">
            <label>Description:</label>
            <textarea name="description">{{ old('description', $product->description) }}</textarea>
        </div>

        <div style="text-align: right;">
            <button class="btn btn-update" type="submit">
                <i class="fas fa-save"></i> Update Product
            </button>
        </div>
    </form>
</div>
@endsection

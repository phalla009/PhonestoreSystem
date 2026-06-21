@extends('layouts.master')

@section('pageTitle')
    Products Listing
@endsection

@section('headerBlock')
<link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
<link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
<script src="{{ URL::asset('js/form.js') }}"></script>
<script src="{{ URL::asset('js/delete_form.js') }}"></script>
<style>
    .status-active   { color: green; background-color: transparent !important; }
    .status-inactive { color: red;   background-color: transparent !important; }
    .form-group select { border-radius: 24px; }
</style>
@endsection

@section('content')

{{-- ❌ លុប: #loading-overlay duplicate — master layout មានហើយ --}}
{{-- ❌ លុប: #logout-confirm duplicate  — master layout មានហើយ --}}

@if(session('success'))
<div id="successMessage" class="custom-success">
    <div class="success-content">
        <span class="success-icon">✔</span>
        <span class="success-text">{{ session('success') }}</span>
    </div>
    <div class="progress-bar"></div>
</div>
@endif

<div class="content-section" id="products">
    <h2><i class="fas fa-box-open"></i> Products Management</h2>

    <div class="filter-section">
        <h4>Filter Products</h4>
        <form id="filterForm" method="GET" action="{{ route('products.index') }}">
            <div class="filter-controls" style="display:flex; align-items:center; gap:10px; margin-top:-5px;">

                {{-- Category --}}
                <div class="form-group" style="min-width:200px;">
                    <select name="category_id" onchange="showLoading('Filtering...'); this.form.submit()">
                        <option value="">All Brands</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Search --}}
                <div class="form-group" style="position:relative;">
                    <i class="fas fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#aaa;font-size:14px;pointer-events:none;"></i>
                    <input type="text" name="search"
                        value="{{ request('search') }}"
                        placeholder="Search Products..."
                        id="searchInput"
                        style="border-radius:24px; padding-left:38px;">
                </div>

                {{-- Add --}}
                <a href="{{ route('products.create') }}"
                   class="btn btn-primary page-link-loading"
                   data-loading-text="Loading form...">
                    <i class="fas fa-circle-plus"></i> Add New Product
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Products</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productsTable">
                @forelse ($products as $product)
                    <tr>
                        <td data-label="No">#{{ $loop->iteration }}</td>
                        <td data-label="Name">{{ $product->name }}</td>
                        <td data-label="Brand">{{ $product->category->name ?? 'N/A' }}</td>
                        <td data-label="Price">${{ number_format($product->price, 2) }}</td>
                        <td data-label="Status">
                            @if(strtolower($product->status) === 'active')
                                <i class="fas fa-check-circle" style="color:green;margin-right:5px;"></i>
                                <span class="status-active">Active</span>
                            @elseif(strtolower($product->status) === 'inactive')
                                <i class="fas fa-times-circle" style="color:red;margin-right:5px;"></i>
                                <span class="status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="{{ route('products.show', $product->id) }}"
                                   class="action-btn show-btn page-link-loading"
                                   data-loading-text="Loading details..."
                                   title="View Details">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="{{ route('products.edit', $product->id) }}"
                                   class="action-btn edit-btn page-link-loading"
                                   data-loading-text="Loading editor..."
                                   title="Edit Product">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button type="button"
                                    class="action-btn delete-btn openDeleteModal"
                                    data-action="{{ route('products.destroy', $product->id) }}"
                                    title="Delete Product">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;" id="found">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Delete Modal --}}
<x-delete-modal />
<script>

    // Search Enter
    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') showLoading('Searching...');
    });

</script>

@endsection
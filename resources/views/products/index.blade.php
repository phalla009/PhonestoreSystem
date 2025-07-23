@extends('layouts.master')

@section('pageTitle')
    KR System PhoneStore
@endsection

@section('headerBlock')
<link rel="stylesheet" href="{{URL::asset('css/main.css')}}">
<script src="{{ URL::asset('js/form.js')}}"></script>
<link rel="stylesheet" href="{{URL::asset('css/delete_form.css')}}">
<script src="{{ URL::asset('js/delete_form.js')}}"></script>

<style>
    .status-active {
        color: green;
        background-color: transparent !important;
    }
    .status-inactive {
        color: red;
        background-color: transparent !important;
    }
    .form-group select {
        border-radius: 24px;
    }
    .action-buttons{
        margin-left: -30px;
    }
</style>
@endsection

@section('content')
    @if(session('success'))
        <div id="successMessage" class="custom-success">
            <i class="fas fa-check-circle" ></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="main-content" id="products">
        <div class="content-section" id="products">
            <h2><i class="fas fa-box-open"></i> Products Management</h2>
            <div class="filter-section">
                <h4>Filter Products</h4>
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="filter-controls" style="display:flex; align-items:center; gap: 10px;">
                        <div class="form-group" style="min-width: 200px;">
                            <select name="category_id" onchange="this.form.submit()">
                                <option value="">All Brands</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTable">
                        @forelse ($products as $product)
                            <tr>
                                <td data-label="No">{{ $product->id }}</td>
                                <td data-label="Name">{{ $product->name }}</td>
                                <td data-label="Brand">{{ $product->category->name ?? 'N/A' }}</td>
                                <td data-label="Price">${{ number_format($product->price, 2) }}</td>
                                <td data-label="Stock">{{ $product->stock }}</td>
                                <td data-label="Status">
                                    <span class="status-{{ str_replace(' ', '-', strtolower($product->status)) }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td data-label="Actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('products.show', $product->id) }}" class="action-btn show-btn"><i class="fas fa-eye"></i> Show</a>
                                        <a href="{{ route('products.edit', $product->id) }}" class="action-btn edit-btn"><i class="fas fa-pen-to-square"></i> Edit</a>
                                        <button type="button" class="action-btn delete-btn openDeleteModal"
                                            data-action="{{ route('products.destroy', $product->id) }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;" id="found">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <span class="close" id="deleteModalClose">&times;</span>
            <h3>
                <i class="fas fa-trash-alt" style="color: #e74c3c; margin-right: 10px;"></i>
                Confirm Delete
            </h3>
            <p>Are you sure you want to delete this record?</p>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="button" id="cancelDelete" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
            </form>
        </div>
    </div>
@endsection

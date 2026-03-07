@extends('layouts.master')

@section('pageTitle')
    Products Listing
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
    /* Loading Overlay */
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
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="content-section" id="products">
        <h2><i class="fas fa-box-open"></i> Products Management</h2>
        <div class="filter-section">
            <h4>Filter Products</h4>
            <form id="filterForm" method="GET" action="{{ route('products.index') }}">
                <div class="filter-controls" style="display:flex; align-items:center; gap: 10px;">

                    {{-- Category Dropdown --}}
                    <div class="form-group" style="min-width: 200px;">
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

                    {{-- Search Input --}}
                   <div class="form-group" style="position: relative;">
                        <i class="fas fa-search" style="
                            position: absolute;
                            left: 14px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: #aaa;
                            font-size: 14px;
                            pointer-events: none;
                        "></i>
                        <input type="text" name="search"
                            value="{{ request('search') }}"
                            placeholder="Search Products..."
                            id="searchInput"
                            style="border-radius: 24px; padding-left: 38px;">
                    </div>

                    {{-- FIX: បន្ថែម class nav-link-loading --}}
                    <a href="{{ route('products.create') }}"
                       class="btn btn-primary nav-link-loading"
                       data-loading-text="Loading add...">
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
                        <th>Producs</th>
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
                            <td data-label="No">#{{ $product->id }}</td>
                            <td data-label="Name">{{ $product->name }}</td>
                            <td data-label="Brand">{{ $product->category->name ?? 'N/A' }}</td>
                            <td data-label="Price">${{ number_format($product->price, 2) }}</td>
                            <td data-label="Stock">{{ $product->stock }}</td>
                            <td data-label="Status">
                                @if(strtolower($product->status) === 'active')
                                    <i class="fas fa-check-circle" style="color: green; margin-right: 5px;"></i>
                                    <span class="status-active">{{ ucfirst($product->status) }}</span>
                                @elseif(strtolower($product->status) === 'inactive')
                                    <i class="fas fa-times-circle" style="color: red; margin-right: 5px;"></i>
                                    <span class="status-inactive">{{ ucfirst($product->status) }}</span>
                                @endif
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    {{-- FIX: បន្ថែម class nav-link-loading + data-loading-text --}}
                                    <a href="{{ route('products.show', $product->id) }}"
                                       class="action-btn show-btn nav-link-loading"
                                       data-loading-text="Loading details...">
                                        <i class="fas fa-eye"></i> Show
                                    </a>
                                    <a href="{{ route('products.edit', $product->id) }}"
                                       class="action-btn edit-btn nav-link-loading"
                                       data-loading-text="Loading editor...">
                                        <i class="fas fa-pen-to-square"></i> Edit
                                    </a>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="delete-modal-box">
            <div class="delete-modal-icon">
                <i class="fas fa-trash-alt"></i>
            </div>

            <button class="delete-modal-close" id="deleteModalClose" aria-label="Close">&times;</button>

            <h3>Delete Record?</h3>
            <p>This action <strong>cannot be undone.</strong> Are you sure you want to permanently delete this record?</p>

            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="delete-modal-actions">
                    <button type="button" id="cancelDelete" class="delete-btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="delete-btn-confirm">
                        <i class="fas fa-trash-alt"></i> Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const overlay     = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        function showLoading(message) {
            loadingText.textContent = message || 'Loading...';
            overlay.style.display = 'flex';
        }

        // Add / Show / Edit links
        document.querySelectorAll('.nav-link-loading').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                const msg  = this.getAttribute('data-loading-text') || 'Loading...';
                if (href && href !== '#') {
                    showLoading(msg);
                    window.location.href = href;
                }
            });
        });

        // Search input → submit on Enter
        document.getElementById('searchInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                showLoading('Searching...');
            }
        });

        // Delete button → open modal
        document.querySelectorAll('.openDeleteModal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                document.getElementById('deleteForm').setAttribute('action', action);
                document.getElementById('deleteConfirmModal').style.display = 'flex';
            });
        });

        // Close modal
        document.getElementById('deleteModalClose').addEventListener('click', function() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });

        // Confirm delete → show loading then submit
        document.getElementById('confirmDeleteBtn').addEventListener('click', function(e) {
            e.preventDefault();
            showLoading('Deleting...');
            setTimeout(function() {
                document.getElementById('deleteForm').submit();
            }, 300);
        });

        // Auto-hide success message
        const successMsg = document.getElementById('successMessage');
        if (successMsg) {
            setTimeout(function() {
                successMsg.style.transition = 'opacity 0.5s';
                successMsg.style.opacity = '0';
                setTimeout(function() { successMsg.style.display = 'none'; }, 500);
            }, 3000);
        }
    </script>

@endsection
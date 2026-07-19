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

    /* Import modal */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.5);
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
        background: #fff;
        padding: 24px;
        border-radius: 10px;
        width: 420px;
        max-width: 90%;
        box-shadow: 0 10px 30px rgba(0,0,0,.2);
    }
    .modal-box h3 {
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .modal-box .hint {
        font-size: 13px;
        color: #777;
        margin: 10px 0 0 0;
        line-height: 1.5;
    }
    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 18px;
    }
    .import-file-input {
        width: 100%;
        padding: 8px;
        border: 1px dashed #ccc;
        border-radius: 8px;
    }
    .import-errors {
        background: #fdecea;
        border: 1px solid #f5c2c0;
        color: #a12622;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
    }
    .btn-secondary-export{
        color: white;
        transition: all 0.2s ease;
        background: repeating-linear-gradient(
            -45deg,
            #ff2020,
            #ff2020 2px,
            #ff2020 2px,
            rgb(148, 0, 0) 4px,
            rgb(9, 9, 9) 4px
        );
    }
    
    .btn-secondary-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px #ff0000;
    }
    .btn-secondary-import{
        color: white;
        transition: all 0.2s ease;
        background: repeating-linear-gradient(
            -45deg,
            #0008ff,
            #0008ff 2px,
            #0008ff 2px,
            rgb(0, 12, 148) 4px,
            rgb(9, 9, 9) 4px
        );
    }
    .btn-secondary-import:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px #0008ff;
    }
    .import-errors ul {
        margin: 8px 0 0 18px;
        padding: 0;
    }
    .filter-controls {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

/* Buttons stay a sane size, search grows to fill space */
.filter-controls .btn {
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .filter-controls {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-controls .btn,
    .filter-controls .form-group {
        width: 100%;
        max-width: 100%;
        margin-left: 0 !important;
    }

    .filter-controls a.btn,
    .filter-controls button.btn {
        justify-content: center;
        text-align: center;
    }

    .filter-controls .form-group[style*="min-width:300px"] {
        min-width: 100% !important;
    }

    .modal-box {
        width: 90%;
        padding: 16px;
    }

    .action-buttons {
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
    }
}

@media (max-width: 480px) {
    .btn i {
        margin-right: 4px;
    }

    .filter-controls .btn {
        font-size: 14px;
        padding: 8px 12px;
    }
}
</style>
@endsection

@section('content')
@if(session('success'))
<div id="successMessage" class="custom-success">
    <div class="success-content">
        <span class="success-icon">✔</span>
        <span class="success-text">{{ session('success') }}</span>
    </div>
    <div class="progress-bar"></div>
</div>
@endif

@if(session('error'))
<div class="import-errors">
    <strong>{{ session('error') }}</strong>
    @if(session('import_errors'))
        <ul>
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    @endif
</div>
@endif

<div class="content-section" id="products">
    <h2><i class="fas fa-box-open"></i> Products Management</h2>

    <div class="filter-section">

        <form id="filterForm" method="GET" action="{{ route('products.index') }}">
            <div class="filter-controls">
                {{-- Add — pushed to the right --}}
                <a href="{{ route('products.create') }}"
                    class="btn btn-primary page-link-loading"
                    data-loading-text="Loading form..."
                    style="white-space:nowrap;">
                    <i class="fas fa-circle-plus"></i> Add New Product
                </a>

                {{-- Import Excel --}}
                <button type="button" class="btn btn-secondary-import" style="white-space:nowrap;"
                        onclick="document.getElementById('importModal').classList.add('active')">
                    <i class="fas fa-file-excel"></i> Import Products
                </button>

                {{-- Export Excel --}}
                <a href="{{ route('products.export', request()->query()) }}"
                class="btn btn-secondary-export"
                style="white-space:nowrap;">
                    <i class="fas fa-file-export"></i> Export Products
                </a>

                {{-- Category --}}
                <div class="form-group" style="max-width:200px;">
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
                <div class="form-group" style="position:relative; min-width:300px;">
                    <i class="fas fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#aaa;font-size:14px;pointer-events:none;"></i>
                    <input type="text" name="search"
                        value="{{ request('search') }}"
                        placeholder="Search Products..."
                        id="searchInput"
                        style="border-radius:24px; padding-left:38px;">
                </div>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ceated At</th>
                    <th>Products</th>
                    <th>Brand</th>
                    <th>Price</th>
                    {{-- <th>Status</th> --}}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productsTable">
                @forelse ($products as $product)
                    <tr>
                        <td data-label="No">#{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                        <td data-label="Create Date">
                            {{ $product->created_at?->timezone('Asia/Phnom_Penh')->format('d M, Y h:i A') ?? 'N/A' }}
                        </td>
                        <td data-label="Name">{{ $product->name }}</td>
                        <td data-label="Brand">{{ $product->category->name ?? 'N/A' }}</td>
                        <td data-label="Price">${{ number_format($product->price, 2) }}</td>
                        {{-- <td data-label="Status">
                            @if(strtolower($product->status) === 'active')
                                <i class="fas fa-check-circle" style="color:green;margin-right:5px;"></i>
                                <span class="status-active">Active</span>
                            @elseif(strtolower($product->status) === 'inactive')
                                <i class="fas fa-times-circle" style="color:red;margin-right:5px;"></i>
                                <span class="status-inactive">Inactive</span>
                            @endif
                        </td> --}}
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

    {{-- Pagination --}}
    @if ($products->hasPages())
    <nav aria-label="Page navigation" class="products-pagination">
        <ul class="pagination-list">

            {{-- Previous --}}
            @if ($products->onFirstPage())
                <li class="page-btn disabled">
                    <span><i class="fa fa-angle-left"></i></span>
                </li>
            @else
                <li class="page-btn">
                    <a href="{{ $products->previousPageUrl() }}" class="page-link-loading" data-loading-text="Loading...">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </li>
            @endif

            {{-- Page numbers --}}
            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if ($page == $products->currentPage())
                    <li class="page-btn active"><span>{{ $page }}</span></li>
                @else
                    <li class="page-btn">
                        <a href="{{ $url }}" class="page-link-loading" data-loading-text="Loading...">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($products->hasMorePages())
                <li class="page-btn">
                    <a href="{{ $products->nextPageUrl() }}" class="page-link-loading" data-loading-text="Loading...">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            @else
                <li class="page-btn disabled"><span><i class="fa fa-angle-right"></i></span></li>
            @endif

        </ul>
    </nav>
    @endif
</div>

{{-- Delete Modal --}}
<x-delete-modal />

{{-- Import Excel Modal --}}
<div id="importModal" class="modal-overlay">
    <div class="modal-box">
        <h3><i class="fas fa-file-excel" style="color:#217346;"></i> Import Products</h3>

        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="import-file-input" required>

            <p class="hint">
                Required columns: <strong>name, price, stock, category_id</strong><br>
                Optional columns: status, description, add_to_pos<br>
                @if(Route::has('products.import-template'))
                    <a href="{{ route('products.import-template') }}">Download sample template</a>
                @endif
            </p>

            <div class="modal-actions">
                <button type="submit" class="btn btn-secondary-import" onclick="showLoading('Importing...')">
                    <i class="fas fa-upload"></i> Upload
                </button>
                <button type="button" class="btn btn-cancel" onclick="document.getElementById('importModal').classList.remove('active')">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>

    // Search Enter
    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') showLoading('Searching...');
    });

    // Reopen import modal automatically if there were import errors
    @if(session('error') && session('import_errors'))
        document.getElementById('importModal').classList.add('active');
    @endif

    // Close modal when clicking outside the box
    document.getElementById('importModal').addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('active');
    });

</script>

@endsection
@extends('layouts.master')

@section('pageTitle')
    Inventory Listing
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .stock-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            width: fit-content;
        }
        .stock-in  { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .stock-low { background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
        .stock-out { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    </style>
@endsection

@section('content')

{{-- ❌ លុប: #logout-confirm  — master layout មានហើយ --}}
{{-- ❌ លុប: logout JS        — master layout មានហើយ --}}

<div class="content-section" id="inventory">
    <h2><i class="fas fa-boxes"></i> Inventory Management</h2>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card">
            <h3>{{ $totalItems }}</h3>
            <p>Total Stocks</p>
        </div>
        <div class="stat-card">
            <h3>{{ $lowStockItems }}</h3>
            <p>Low Stock Products</p>
        </div>
        <div class="stat-card">
            <h3>{{ $outOfStockItems }}</h3>
            <p>Out of Stocks</p>
        </div>
        <div class="stat-card">
            <h3>${{ number_format($inventoryValue, 2) }}</h3>
            <p>Inventory Value</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container" role="region" aria-label="Inventory products table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Products</th>
                    <th>SKU</th>
                    <th>Current Stock</th>
                    <th>Status</th>
                    <th>Updated Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td data-label="No">#{{ $loop->iteration }}</td>
                        <td data-label="Product">{{ $product->name }}</td>
                        <td data-label="SKU">{{ $product->sku ?? '-' }}</td>
                        <td data-label="Current Stock">{{ $product->stock }}</td>
                        <td data-label="Status">
                            @if($product->stock <= 0)
                                <span class="stock-badge stock-out">Out of Stock</span>
                            @elseif($product->stock <= $product->min_stock)
                                <span class="stock-badge stock-low">Low Stock</span>
                            @else
                                <span class="stock-badge stock-in">In Stock</span>
                            @endif
                        </td>
                        <td data-label="Updated Date">{{ $product->updated_at->timezone('Asia/Phnom_Penh')->format('Y-m-d H:i') }}</td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="{{ route('inventory.edit', $product->id) }}"
                                   class="action-btn edit-btn page-link-loading"
                                   data-loading-text="Opening editor..."
                                   title="Edit stock">
                                    <i class="fas fa-pen"></i>
                                </a>
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

<script>
    // ✅ reuse overlay ពី master layout
    function showLoading(msg) {
        const ov = document.getElementById('loading-overlay');
        const lt = document.getElementById('loading-text');
        if (!ov) return;
        if (lt) lt.textContent = msg || 'Loading...';
        ov.style.display = 'flex';
    }

    // Page nav links — class "page-link-loading"
    document.querySelectorAll('.page-link-loading').forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const msg  = this.getAttribute('data-loading-text') || 'Loading...';
            if (href && href !== '#' && href !== 'javascript:void(0)') {
                e.preventDefault();
                showLoading(msg);
                window.location.href = href;
            }
        });
    });
</script>

@endsection
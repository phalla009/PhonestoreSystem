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
        .stock-in     { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .stock-low    { background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
        .stock-out    { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    </style>
@endsection

@section('content')

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

    {{-- Logout Confirm --}}
    <div id="logout-confirm">
        <div class="confirm-box">
            <div class="icon-container">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <p>Are you sure you want to logout?</p>
            <button id="confirm-yes">Yes, Logout!</button>
            <button id="confirm-no">No, Keep it!</button>
        </div>
    </div>

    <script>
        const logoutLink    = document.getElementById('logout-link');
        const logoutConfirm = document.getElementById('logout-confirm');
        const confirmYes    = document.getElementById('confirm-yes');
        const confirmNo     = document.getElementById('confirm-no');
        const logoutForm    = document.getElementById('logout-form');

        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            logoutConfirm.style.display = 'flex';
        });
        confirmYes.addEventListener('click', function() { logoutForm.submit(); });
        confirmNo.addEventListener('click',  function() { logoutConfirm.style.display = 'none'; });
    </script>

@endsection
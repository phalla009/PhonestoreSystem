@extends('layouts.master') 
@section('pageTitle')
    Inventory Management
@endsection

@section('headerBlock')
 <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
 <script src="{{ URL::asset('js/form.js') }}"></script>
 <style>
    .text-warning { color: orange; }
    .text-success { color: rgb(5, 199, 5); }
    .text-danger { color: red; }


 </style>
@endsection

@section('content')
<div class="content-section" id="inventory">
    <h2><i class="fas fa-boxes"></i> Inventory Management</h2>

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

    <div class="table-container" role="region" aria-label="Inventory products table">
        <table>
            <thead>
                <tr>
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
                        <td data-label="Product">{{ $product->name }}</td>
                        <td data-label="SKU">{{ $product->name . ' PPLT ' . str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td data-label="Current Stock">{{ $product->stock }}</td>
                        <td data-label="Status">
                            @if($product->stock <= 0)
                                <span class="text-danger">Out of Stock</span>
                            @elseif($product->stock <= $product->min_stock)
                                <span class="text-warning">Low Stock</span>
                            @else
                                <span class="text-success">In Stock</span>
                            @endif
                        </td>
                        <td data-label="Updated Date">{{ $product->updated_at->timezone('Asia/Phnom_Penh')->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;" id="found">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

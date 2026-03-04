@extends('layouts.master')

@section('pageTitle')
    Add New Orders
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .text-danger { color: red; margin-top: 0.25rem; font-size: 0.9rem; }

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
            <i class="fas fa-check-circle" style="color: green; margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="modal-content">
        <a href="{{ route('orders.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h2><i class="fas fa-shopping-cart"></i> Add New Order</h2>

        <form id="orderForm" action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Customer:</label>
                <select name="customer_id">
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Product:</label>
                <select name="product_id">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Quantity:</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity') }}">
                @error('quantity')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Status:</label>
                <select name="status" required>
                    <option value="pending"   {{ old('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Order Date:</label>
                <input type="date" name="order_date" value="{{ old('order_date') }}">
                @error('order_date')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="note" placeholder="Optional note...">{{ old('note') }}</textarea>
            </div>

            <div style="text-align: right; margin-top: 1rem;">
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Add Order
                </button>
                <button id="cancelBtn" type="button" class="btn btn-cancel">
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
        document.getElementById('orderForm').addEventListener('submit', function() {
            loadingText.textContent = 'Saving...';
            overlay.style.display = 'flex';
        });

        // Cancel → reset form only
        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('orderForm').reset();
        });
    </script>

@endsection
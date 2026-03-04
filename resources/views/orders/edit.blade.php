@extends('layouts.master')

@section('pageTitle')
    Edited Orders
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
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
            {{ session('success') }}
        </div>
    @endif

    <div class="modal-content">
        <a href="{{ route('orders.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h2><i class="fas fa-shopping-cart"></i> Edit Order</h2>

        <form id="orderForm" action="{{ route('orders.update', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Customer:</label>
                <select name="customer_id">
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ (old('customer_id', $order->customer_id) == $customer->id) ? 'selected' : '' }}>
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
                        <option value="{{ $product->id }}" {{ (old('product_id', $order->product_id) == $product->id) ? 'selected' : '' }}>
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
                <input type="number" name="quantity" min="1" value="{{ old('quantity', $order->quantity) }}">
                @if($errors->has('quantity'))
                    <p class="text-danger mt-1">{{ $errors->first('quantity') }}</p>
                @endif
            </div>

            <div class="form-group">
                <label>Status:</label>
                <select name="status">
                    <option value="pending"   {{ (old('status', $order->status) == 'pending')   ? 'selected' : '' }}>Pending</option>
                    {{-- <option value="completed" {{ (old('status', $order->status) == 'completed') ? 'selected' : '' }}>Completed</option> --}}
                    <option value="cancelled" {{ (old('status', $order->status) == 'cancelled') ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Order Date:</label>
                <input type="date" name="order_date" value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}">
                @error('order_date')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group" style="width: 100%;">
                <label>Description:</label>
                <textarea name="note">{{ old('note', $order->note) }}</textarea>
            </div>

            <div style="text-align: right;">
                <button class="btn btn-update" type="submit">
                    <i class="fas fa-save"></i> Update Order
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
            loadingText.textContent = 'Updating...';
            overlay.style.display = 'flex';
        });
    </script>

@endsection
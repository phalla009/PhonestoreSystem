@extends('layouts.master')

@section('pageTitle')
    KR System PhoneStore
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .text-danger {
            color: red;
            margin-top: 0.25rem;
            font-size: 0.9rem;
        }
    </style>
@endsection

@section('content')
    @if(session('success'))
        <div id="successMessage" class="custom-success">
            <i class="fas fa-check-circle" style="color: green; margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="modal-content">
        <a href="{{ route('orders.index') }}" class="btn btn-back">
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
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
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
                    <textarea name="note"  placeholder="Optional note...">{{ old('note') }}</textarea>
                </div>
            <div style="text-align: right; margin-top: 1rem;">
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Add Order
                </button>
                <button id="cancelCategory" type="button" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('cancelCategory').addEventListener('click', function () {
            document.getElementById('orderForm').reset();
        });
    </script>
@endsection

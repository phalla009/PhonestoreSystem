@extends('layouts.master')

@section('pageTitle')
    Edited Orders
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
@endsection

@section('content')
    @if(session('success'))
        <div id="successMessage" class="custom-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="modal-content">
        <a href="{{ route('orders.index') }}" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h2><i class="fas fa-shopping-cart"></i> EditOrder</h2>
        <form action="{{ route('orders.update', $order->id) }}" method="POST" enctype="multipart/form-data">
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
                    @if ($errors->has('quantity'))
                        <p class="text-danger mt-1">{{ $errors->first('quantity') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" >
                        <option value="pending" {{ (old('status', $order->status) == 'pending') ? 'selected' : '' }}>Pending</option>
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
@endsection

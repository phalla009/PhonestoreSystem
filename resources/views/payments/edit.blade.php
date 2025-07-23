@extends('layouts.master')

@section('pageTitle')
    Edit Payment
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
@endsection

@section('content')
<div class="modal-content">
    <a href="{{ route('payments.index') }}" class="btn btn-back">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <h2><i class="fas fa-pen-to-square"></i> Edit Payment</h2>
    <form action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Order Number:</label>
            <input type="text" value="{{ $payment->order->order_number ?? 'N/A' }}" disabled class="form-control">
        </div>

        <div class="form-group">
            <label>Customer:</label>
            <input type="text" value="{{ $payment->order->customer->name ?? 'N/A' }}" disabled class="form-control">
        </div>

        <div class="form-group">
            <label>Product:</label>
            <input type="text" value="{{ $payment->order->product->name ?? 'N/A' }}" disabled class="form-control">
        </div>

        <div class="form-group">
            <label for="amount">Amount Paid ($):</label>
            <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                   value="{{ old('amount', $payment->amount) }}"
                   class="form-control" required>
        </div>

        <div class="form-group">
            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="cash" {{ $payment->payment_method === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="card" {{ $payment->payment_method === 'card' ? 'selected' : '' }}>Card</option>
                <option value="bank" {{ $payment->payment_method === 'bank' ? 'selected' : '' }}>Bank </option>
            </select>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-update">
                <i class="fas fa-save"></i> Update Payment
            </button>
        </div>
    </form>
</div>
@endsection

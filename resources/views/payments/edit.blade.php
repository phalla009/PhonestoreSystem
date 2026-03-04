@extends('layouts.master')

@section('pageTitle')
   Edited Payments
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

    <div class="modal-content">
        <a href="{{ route('payments.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h2><i class="fas fa-pen-to-square"></i> Edit Payment</h2>

        <form id="paymentForm" action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
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
                    <option value="bank" {{ $payment->payment_method === 'bank' ? 'selected' : '' }}>Bank</option>
                </select>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-update">
                    <i class="fas fa-save"></i> Update Payment
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
        document.getElementById('paymentForm').addEventListener('submit', function() {
            loadingText.textContent = 'Updating...';
            overlay.style.display = 'flex';
        });
    </script>

@endsection
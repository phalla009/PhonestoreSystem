@extends('layouts.master')

@section('pageTitle')
   Show Payments
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .payment-details {
            max-width: 100%;
            height: 650px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 30px 40px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
        }
        .payment-details h2 { text-align: center; margin-bottom: 30px; color: #34495e; font-weight: 700; font-size: 2rem; }
        .row { display: flex; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; }
        .row p { flex: 1 1 30%; margin: 0 10px 10px 0; font-size: 1.1rem; white-space: nowrap; }
        .row p strong { color: #2980b9; margin-right: 5px; }
        @media (max-width: 600px) { .row p { flex: 1 1 100%; white-space: normal; } }

        /* Loading Overlay */
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
        <div id="loading-text">Going back...</div>
    </div>

    @if(session('success'))
        <div id="successMessage" class="custom-success" style="max-width:700px; margin: 20px auto; padding:10px; background:#d4edda; color:#155724; border-radius:5px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="payment-details" role="main" aria-label="Payment Details">
        <a href="{{ route('payments.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h2><i class="fas fa-credit-card"></i> Payment Details</h2>

        <div class="row">
            <p><strong>Payment ID:</strong> {{ $payment->id }}</p>
            <p><strong>Order Number:</strong> {{ $payment->order->order_number ?? 'N/A' }}</p>
            <p><strong>Customer:</strong> {{ $payment->order->customer->name ?? 'N/A' }}</p>
        </div>

        <div class="row">
            <p><strong>Product:</strong> {{ $payment->order->product->name ?? 'N/A' }}</p>
            <p><strong>Amount Paid:</strong> ${{ number_format($payment->amount, 2) }}</p>
            <p><strong>Payment Date:</strong> {{
                $payment->paid_at
                    ? \Carbon\Carbon::parse($payment->paid_at)->timezone('Asia/Phnom_Penh')->format('d-m-Y H:i')
                    : \Carbon\Carbon::now('Asia/Phnom_Penh')->format('d-m-Y H:i')
            }}</p>
        </div>

        <div class="row">
            <p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method ?? 'N/A') }}</p>
        </div>
    </div>

    <script>
        // Back button → show loading then navigate
        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loading-overlay').style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });
    </script>

@endsection
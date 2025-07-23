@extends('layouts.master')

@section('pageTitle')
    KR System PhoneStore
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

        .payment-details h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #34495e;
            font-weight: 700;
            font-size: 2rem;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .row p {
            flex: 1 1 30%; 
            margin: 0 10px 10px 0;
            font-size: 1.1rem;
            white-space: nowrap;
        }

        .row p strong {
            color: #2980b9;
            margin-right: 5px;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .row p {
                flex: 1 1 100%;
                white-space: normal;
            }
        }
    </style>
@endsection

@section('content')

    @if(session('success'))
        <div id="successMessage" class="custom-success" style="max-width:700px; margin: 20px auto; padding:10px; background:#d4edda; color:#155724; border-radius:5px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="payment-details" role="main" aria-label="Payment Details">
         <a href="{{ route('payments.index') }}" class="btn btn-back">
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

@endsection

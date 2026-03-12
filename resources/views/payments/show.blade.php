@extends('layouts.master')

@section('pageTitle')
    Payments Details
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

        .info-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .info-row-1 {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .info-box {
            padding: 14px 18px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .info-box:hover {
            border-color: #a5b4fc;
            box-shadow: 0 4px 12px rgba(99,102,241,0.08);
        }

        .info-label {
            font-size: 11px;
            font-weight: 700;
            color: #4338ca;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-label i {
            font-size: 11px;
            color: #6366f1;
        }

        .info-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }
    </style>
@endsection

@section('content')

    {{-- Loading Overlay --}}
    <div id="loading-overlay">
        <div class="spinner"></div>
        <div id="loading-text">Going back...</div>
    </div>

    @if(session('success'))
        <div id="successMessage" class="custom-success">
            <i class="fas fa-check-circle" style="color: green; margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="modal-content" role="main" aria-label="Payment Details">
        <a href="{{ route('payments.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-credit-card"></i> Payment Details</h2>

        {{-- Row 1: Payment ID, Order Number, Customer --}}
        <div class="info-row">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-hashtag"></i> Payment ID</div>
                <div class="info-value">{{ $payment->id }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-receipt"></i> Order Number</div>
                <div class="info-value">{{ $payment->order->order_number ?? 'N/A' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-user"></i> Customer</div>
                <div class="info-value">{{ $payment->order->customer->name ?? 'N/A' }}</div>
            </div>
        </div>

        {{-- Row 2: Product, Amount Paid, Payment Date --}}
        <div class="info-row">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-box"></i> Product</div>
                <div class="info-value">{{ $payment->order->product->name ?? 'N/A' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-dollar-sign"></i> Amount Paid</div>
                <div class="info-value">${{ number_format($payment->amount, 2) }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-calendar-alt"></i> Payment Date</div>
                <div class="info-value">
                    {{
                        $payment->paid_at
                            ? \Carbon\Carbon::parse($payment->paid_at)->timezone('Asia/Phnom_Penh')->format('d-m-Y H:i')
                            : \Carbon\Carbon::now('Asia/Phnom_Penh')->format('d-m-Y H:i')
                    }}
                </div>
            </div>
        </div>

        {{-- Row 3: Payment Method --}}
        <div class="info-row-1">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-credit-card"></i> Payment Method</div>
                <div class="info-value">{{ ucfirst($payment->payment_method ?? 'N/A') }}</div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loading-overlay').style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });
    </script>

@endsection
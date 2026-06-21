@extends('layouts.master')

@section('pageTitle')
    Orders Details
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

        .info-row-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
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
            white-space: pre-wrap;
            display: flex;
            align-items: center;
            
        }

        .status-badge {
            display: inline-block;
            padding: 0px;
            border-radius: 24px;
            font-size: 12px;
        }
        .status-pending   { background: none; color: #a16207; border: none;}
        .status-completed { background: none; color: #16a34a; border: none; }
        .status-cancelled { background: none; color: #dc2626; border: none; }
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

    <div class="modal-content" role="main" aria-label="Order Details">
        <a href="{{ route('orders.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-shopping-cart"></i> Order Details</h2>

        {{-- Row 1: Order ID, Customer, Status --}}
        <div class="info-row">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-hashtag"></i> Order ID</div>
                <div class="info-value">{{ $order->order_number }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-user"></i> Customer</div>
                <div class="info-value">{{ optional($order->customer)->name ?? 'N/A' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-circle"></i> Status</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower($order->status) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Row 2: Product, Quantity, Order Date --}}
        <div class="info-row">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-box"></i> Product</div>
                <div class="info-value">{{ optional($order->product)->name ?? 'N/A' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-sort-numeric-up"></i> Quantity</div>
                <div class="info-value">{{ $order->quantity }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-calendar-alt"></i> Order Date</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}</div>
            </div>
        </div>

        {{-- Row 3: Cost Amount, Selling Amount, Note --}}
        <div class="info-row">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-dollar-sign"></i> Cost Amount</div>
                <div class="info-value">${{ number_format(($order->product->price ?? 0) * $order->quantity, 2) }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-cash-register"></i> Selling Amount</div>
                <div class="info-value">${{ number_format($order->total_amount ?? 0, 2) }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-sticky-note"></i> Note</div>
                <div class="info-value">{{ $order->note ?? 'N/A' }}</div>
            </div>
        </div>

        {{-- Row 4: Created & Updated --}}
        <div class="info-row-2">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-calendar-plus"></i> Created</div>
                <div class="info-value">{{ $order->created_at->setTimezone('Asia/Phnom_Penh')->format('M d, Y \a\t g:i A') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-clock"></i> Last Updated</div>
                <div class="info-value">{{ $order->updated_at->setTimezone('Asia/Phnom_Penh')->format('M d, Y \a\t g:i A') }}</div>
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
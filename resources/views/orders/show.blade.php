@extends('layouts.master')

@section('pageTitle')
    Show Orders
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .details-card {
            max-width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 30px 40px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
        }

        .details-card h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #34495e;
            font-weight: 700;
            font-size: 2rem;
        }

        .details-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .details-group {
            flex: 1 1 30%;
            margin-bottom: 20px;
        }

        .details-group label {
            font-weight: bold;
            color: #2980b9;
            display: block;
            margin-bottom: 5px;
        }

        .details-group div {
            font-size: 1.1rem;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
            text-transform: capitalize;
        }

        .status-pending {
            background-color: #ffc107;
        }

        .status-completed {
            background-color: #28a745;
        }

        .status-cancelled {
            background-color: #dc3545;
        }

        .note-display {
            white-space: pre-wrap;
        }

        @media (max-width: 900px) {
            .details-group {
                flex: 1 1 45%;
            }
        }

        @media (max-width: 600px) {
            .details-group {
                flex: 1 1 100%;
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

    <div class="details-card" role="main" aria-label="Order Details">
        <a href="{{ route('orders.index') }}" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h2><i class="fas fa-shopping-cart"></i> Order Details</h2>

        <div class="details-row">
            <div class="details-group">
                <label>Order ID:</label>
                <div>{{ $order->order_number }}</div>
            </div>
            <div class="details-group">
                <label>Customer:</label>
                <div>{{ optional($order->customer)->name ?? 'N/A' }}</div>
            </div>
            <div class="details-group">
                <label>Status:</label>
                <div>
                    <span class="status-badge status-{{ strtolower($order->status) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="details-row">
            <div class="details-group">
                <label>Product:</label>
                <div>{{ optional($order->product)->name ?? 'N/A' }}</div>
            </div>
            <div class="details-group">
                <label>Quantity:</label>
                <div>{{ $order->quantity }}</div>
            </div>
            <div class="details-group">
                <label>Order Date:</label>
                <div>{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}</div>
            </div>
        </div>

        <div class="details-row">
            <div class="details-group">
                <label>Cost Amount:</label>
                <div>${{ number_format($order->total_amount ?? 0, 2) }}</div>
            </div>
            <div class="details-group">
                <label>Selling Amount:</label>
                <div>${{ number_format($order->payments_sum_amount ?? 0, 2) }}</div>
            </div>
            <div class="details-group">
                <label>Note:</label>
                <div class="note-display">{{ $order->note ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="details-row">
            <div class="details-group">
                <label>Created:</label>
                <div>{{ $order->created_at->setTimezone('Asia/Phnom_Penh')->format('M d, Y \a\t g:i A') }}</div>
            </div>
            <div class="details-group">
                <label>Last Updated:</label>
                <div>{{ $order->updated_at->setTimezone('Asia/Phnom_Penh')->format('M d, Y \a\t g:i A') }}</div>
            </div>
        </div>
    </div>
@endsection

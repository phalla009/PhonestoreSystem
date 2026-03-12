@extends('layouts.master')

@section('pageTitle')
    Customers Details
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
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .info-row-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            width: fit-content;
        }
        .status-active   { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .status-inactive { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    </style>
@endsection

@section('content')

    {{-- Loading Overlay --}}
    <div id="loading-overlay">
        <div class="spinner"></div>
        <div id="loading-text">Going back...</div>
    </div>

    <div class="modal-content" role="main" aria-label="Customer Details">
        <a href="{{ route('customers.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-user"></i> Customer Details</h2>

        {{-- Row 1: Name & Gender --}}
        <div class="info-row">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-user"></i> Customer Name</div>
                <div class="info-value">{{ $customer->name }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-venus-mars"></i> Gender</div>
                <div class="info-value">{{ ucfirst($customer->gender) }}</div>
            </div>
        </div>

        {{-- Row 2: Phone, Email, Status --}}
        <div class="info-row-3">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-phone"></i> Phone</div>
                <div class="info-value">{{ $customer->phone }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                <div class="info-value">{{ $customer->email ?? 'No Email' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-circle"></i> Status</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower($customer->status) }}">
                        {{ ucfirst($customer->status) }}
                    </span>
                </div>
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
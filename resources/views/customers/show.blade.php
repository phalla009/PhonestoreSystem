@extends('layouts.master')

@section('pageTitle')
    Show Customers
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
        .details-card h2 { text-align: center; margin-bottom: 30px; color: #34495e; font-weight: 700; font-size: 2rem; }
        .details-row { display: flex; flex-wrap: wrap; justify-content: space-between; margin-bottom: 20px; }
        .details-group { flex: 1 1 45%; margin-bottom: 20px; }
        .details-group label { font-weight: bold; color: #2980b9; display: block; margin-bottom: 5px; }
        .details-group div { font-size: 1.1rem; }
        .status-badge { padding: 5px 10px; border-radius: 4px; font-weight: bold; text-transform: capitalize; background-color: #f1f1f1; color: #2c3e50; }
        .status-active   { background-color: #27ae60; color: #fff; }
        .status-inactive { background-color: #c0392b; color: #fff; }
        @media (max-width: 600px) { .details-group { flex: 1 1 100%; } }

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

    <div class="details-card" role="main" aria-label="Customer Details">
        <a href="{{ route('customers.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h2><i class="fas fa-user"></i> Customer Details</h2>

        <div class="details-row">
            <div class="details-group">
                <label>Customer Name:</label>
                <div>{{ $customer->name }}</div>
            </div>
            <div class="details-group">
                <label>Gender:</label>
                <div>{{ ucfirst($customer->gender) }}</div>
            </div>
        </div>

        <div class="details-row">
            <div class="details-group">
                <label>Phone:</label>
                <div>{{ $customer->phone }}</div>
            </div>
           <div class="details-group">
                <label>Email:</label>
                <div>{{ $customer->email ?? 'No Email' }}</div>
            </div>
            <div class="details-group">
                <label>Status:</label>
                <div>
                    <span class="status-badge status-{{ strtolower($customer->status) }}">
                        {{ ucfirst($customer->status) }}
                    </span>
                </div>
            </div>
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
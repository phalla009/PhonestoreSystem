@extends('layouts.master')

@section('pageTitle')
    Report
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
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
        <div id="loading-text">Going back...</div>
    </div>

    <div class="content-section">

        {{-- Back Button --}}
        <a href="{{ route('reports.index') }}" id="backBtn" class="btn btn-back" style="margin-bottom: 16px; display: inline-block;">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2>{{ ucfirst($type) }} Report</h2>
        <p>From {{ $start->format('M d, Y') }} to {{ $end->format('M d, Y') }}</p>

        {{-- Sales Report --}}
        @if($type === 'sales')
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $row)
                        <tr>
                            <td>{{ $row->date }}</td>
                            <td>${{ number_format($row->total ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        {{-- Financial Report --}}
        @elseif($type === 'financial')
            <h4>Financial Report ({{ $start->format('M d, Y') }} - {{ $end->format('M d, Y') }})</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sold Quantity</th>
                        <th>Revenue ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                            <td>{{ $row->sold_qty ?? 0 }}</td>
                            <td>${{ number_format($row->revenue ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        {{-- Inventory Report --}}
        @elseif($type === 'inventory')
            @php
                $grandTotalUSD = 0;
                $totalQty      = 0;
                $rate          = 4100; // 1 USD = 4100 KHR
            @endphp
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price ($)</th>
                        <th>Total ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $row)
                        @php
                            $qty   = $row->quantity ?? $row->stock ?? 0;
                            $total = $qty * $row->price;
                            $grandTotalUSD += $total;
                            $totalQty      += $qty;
                        @endphp
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{{ $qty }}</td>
                            <td>${{ number_format($row->price, 2) }}</td>
                            <td>${{ number_format($total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total Qty:</th>
                        <th>{{ $totalQty }}</th>
                        <th style="text-align:right;">Grand Total (USD):</th>
                        <th>${{ number_format($grandTotalUSD, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align:right;">Grand Total (KHR):</th>
                        <th>៛{{ number_format($grandTotalUSD * $rate) }}</th>
                    </tr>
                </tfoot>
            </table>

        {{-- Customer Report --}}
        @elseif($type === 'customer')
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>
                            <th class="text-center">Total Qty</th>
                            <th class="text-right">Total Price</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td>{{ ucfirst($row->gender) }}</td>
                                <td class="text-center">{{ number_format($row->total_qty ?? 0) }}</td>
                                <td class="text-right">${{ number_format($row->total_price ?? 0, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #e3f2fd; color: #0d47a1; font-weight: bold;">
                            <td colspan="2" class="text-right">Grand Total:</td>
                            <td class="text-center">{{ number_format($results->sum('total_qty')) }}</td>
                            <td class="text-right">${{ number_format($results->sum('total_price'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

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
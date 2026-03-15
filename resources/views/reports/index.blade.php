@extends('layouts.master')

@section('pageTitle')
    Reports Listing
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/report.js') }}"></script>
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
        <div id="loading-text">Generating report...</div>
    </div>

    <div class="content-section" id="report">
        <h2><i class="fas fa-chart-line"></i> Reports & Analytics</h2>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>${{ number_format($salesThisMonth ?? 0, 2) }}</h3>
                <p>This Month Sales</p>
            </div>
            <div class="stat-card">
                <h3>{{ $ordersThisMonth ?? 0 }}</h3>
                <p>Orders This Month</p>
            </div>
            <div class="stat-card">
                <h3>{{ $newCustomers ?? 0 }}</h3>
                <p>New Customers</p>
            </div>
            <div class="stat-card">
                <h3>{{ $customerSatisfaction ?? 0 }}%</h3>
                <p>Customer Satisfaction</p>
            </div>
        </div>

        <form id="reportForm" method="POST" action="{{ route('reports.generate') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Report Type:</label>
                    <select name="type" required class="form-control">
                        <option value="sales">Sales Report</option>
                        <option value="inventory">Inventory Report</option>
                        <option value="customer">Customer Report</option>
                        <option value="financial">Financial Report</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date Range:</label>
                    <select name="range">
                        <option value="today">Today</option>
                        <option value="7days">Last 7 Days</option>
                        <option value="30days">Last 30 Days</option>
                        <option value="3months">Last 3 Months</option>
                        <option value="1year">Last Year</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-chart-pie"></i> Generate Report
            </button>
        </form>

        <div style="margin-top: 2rem; background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3>Daily Sales Trends</h3>
            <canvas id="salesChart" style="max-width: 100%; height: 300px;"></canvas>
        </div>
    </div>
     <div id="logout-confirm">
    <div class="confirm-box">
        <div class="icon-container">
        <i class="fas fa-sign-out-alt"></i>
        </div>
        <p>Are you sure you want to logout?</p>
        <button id="confirm-yes">Yes, Logout!</button>
        <button id="confirm-no">No, Keep it!</button>
    </div>
    </div>

    <script>
        // Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels ?? []) !!},
                datasets: [{
                    label: 'Monthly Revenue ($)',
                    data: {!! json_encode($chartData ?? []) !!},
                    borderColor: 'rgb(82, 167, 232)',
                    backgroundColor: '#181c27',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Date' } },
                    y: { title: { display: true, text: 'Revenue ($)' }, beginAtZero: true }
                }
            }
        });
    </script>

@endsection
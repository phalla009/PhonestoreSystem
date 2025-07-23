@extends('layouts.master')

@section('pageTitle')
    KR System PhoneStore
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/report.js')}}"></script>
@endsection

@section('content')
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
    <form method="POST" action="{{ route('reports.generate') }}">
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
    </div>
    <div style="margin-top: 2rem; background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3>Daily Sales Trends</h3>
        <canvas id="salesChart" style="max-width: 100%; height: 300px;"></canvas>
    </div>
</div>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels ?? []) !!},
            datasets: [{
                label: 'Monthly Revenue ($)',
                data: {!! json_encode($chartData ?? []) !!},
                borderColor: 'rgb(82, 167, 232)',
                backgroundColor: 'rgba(13, 104, 232, 0.84)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date' 
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Revenue ($)'  
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection

@extends('layouts.master')

@section('pageTitle') Dashboard @endsection

@section('headerBlock')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/dashboard.css') }}">
@endsection

@section('content')
<div class="content-section active" id="dashboard">

    {{-- Success message --}}
    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>

    <div class="stats-grid">
        <div class="stat-card"><h3>{{ $totalProducts }}</h3><p>Total Products</p></div>
        <div class="stat-card"><h3>{{ $pendingOrders }}</h3><p>Pending Orders</p></div>
        <div class="stat-card"><h3>{{ $totalCustomers }}</h3><p>Total Customers</p></div>
        <div class="stat-card"><h3>${{ number_format($currentMonthRevenue,2) }}</h3><p>Monthly Revenue</p></div>
    </div>

    <h2><i class="fas fa-chart-pie"></i> Production Analysis</h2>
    <div class="charts-grid">
        <div class="card">
            <h4>Run Time vs Downtime</h4>
            <canvas id="barChart"></canvas>
        </div>
        <div class="card">
            <h4>Monthly Sales</h4>
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    <div class="table-container">
        <h2><i class="fas fa-shopping-cart"></i> Recent Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer->name ?? 'N/A' }}</td>
                    <td>{{ $order->product->name ?? 'N/A' }}</td>
                    <td>${{ number_format($order->total_amount,2) }}</td>
                    <td>
                        @if($order->status==='pending')
                            <i class="fas fa-hourglass-half" style="color: orange;"></i> Pending
                        @elseif($order->status==='completed')
                            <i class="fas fa-check-circle" style="color: green;"></i> Completed
                        @elseif($order->status==='cancelled')
                            <i class="fas fa-times-circle" style="color: red;"></i> Cancelled
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
  // ===== Run Time vs Downtime =====
  new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
      labels: @json($productionPhases),
      datasets: [
        { label: 'Downtime (h)', data: @json($downtime), backgroundColor:'#3498db' },
        { label: 'RunTime (h)', data: @json($runtime), backgroundColor:'#1abc9c' }
      ]
    },
    options: { responsive: true }
  });

  // ===== Monthly Sales =====
  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: @json($months),
      datasets: [{
        label: 'Monthly Sales ($)',
        data: @json($monthlyRevenue),
        borderColor:'#2980b9',
        backgroundColor:'rgba(41,128,185,0.2)',
        fill:true,
        tension:0.3
      }]
    },
    options: { responsive:true }
  });

  // ===== Auto-hide success message =====
  document.addEventListener('DOMContentLoaded', () => {
    const msg = document.querySelector('.success-message');
    if(msg){ setTimeout(()=>msg.remove(),3000); }
  });
</script>
@endsection
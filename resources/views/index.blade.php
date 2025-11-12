@extends('layouts.master')

@section('pageTitle')
    Dashboard
@endsection

@section('headerBlock')
  
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/gaugeJS/dist/gauge.min.js"></script>
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/dashboard.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>

@endsection
@section('content')
<div class="content-section active" id="dashboard">

    {{-- Show success message if present --}}
    @if(session('success'))
      <div class="success-message">
        {{ session('success') }}
      </div>
    @endif

    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>{{ $totalProducts }}</h3>
            <p>Total Products</p>
        </div>
        <div class="stat-card">
            <h3>{{ $pendingOrders }}</h3>
            <p>Pending Orders</p>
        </div>
        <div class="stat-card">
            <h3>{{ $totalCustomers }}</h3>
            <p>Total Customers</p>
        </div>
        <div class="stat-card">
            <h3>${{ number_format($monthlyRevenue, 2) }}</h3>
            <p>Monthly Revenue</p>
        </div>
    </div>
    <h2><i class="fas fa-chart-pie"></i> Production Analysis</h2>
    {{-- Charts --}}
    <div class="charts-grid">
        <div class="card">
            <h4>Run Time vs Downtime</h4>
            <canvas id="barChart"></canvas>
        </div>
        <div class="card">
            <h4>Production Cost - Last 12 Months</h4>
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    {{-- Gauges --}}
    <div class="gauges-grid">
        <div class="card"><h4>Availability</h4><canvas id="gauge1"></canvas></div>
        <div class="card"><h4>Performance</h4><canvas id="gauge2"></canvas></div>
        <div class="card"><h4>Quality</h4><canvas id="gauge3"></canvas></div>
        <div class="card"><h4>OEE</h4><canvas id="gauge4"></canvas></div>
    </div>
     <div class="table-container">
        <h2><i class="fas fa-shopping-cart" id="reorder"></i> Recent Orders</h2>
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
            <tbody id="dashboardOrdersTable">
                @forelse ($recentOrders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer->name ?? 'N/A' }}</td>
                        <td>{{ $order->product->name ?? 'N/A' }}</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            @if($order->status === 'pending')
                                <i class="fas fa-hourglass-half" style="color: orange; margin-right: 5px;"></i>
                                <span class="status-pending">{{ ucfirst($order->status) }}</span>
                            @elseif($order->status === 'completed')
                                <i class="fas fa-check-circle" style="color: green; margin-right: 5px;"></i>
                                <span class="status-completed">{{ ucfirst($order->status) }}</span>
                            @elseif($order->status === 'cancelled')
                                <i class="fas fa-times-circle" style="color: red; margin-right: 5px;"></i>
                                <span class="status-cancelled">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;" id="found">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script>
  // Bar Chart
  new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
      labels: ['Kiln phase','Pre-Heating','Packaging','Shipping'],
      datasets: [
        {
          label: 'Downtime (h)',
          data: [124, 125, 123, 124],
          backgroundColor: '#3498db'
        },
        {
          label: 'RunTime (h)',
          data: [3091, 3191, 3185, 3138],
          backgroundColor: '#1abc9c'
        }
      ]
    },
    options: { responsive: true }
  });

  // Line Chart
  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: ['Sep','Oct','Nov','Dec','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug'],
      datasets: [{
        label: 'Production Cost',
        data: [169.79,186.83,192.37,195.34,192.21,174.3,445.22,593.65,684.99,802.55,1018,1210],
        borderColor: '#2980b9',
        backgroundColor: 'rgba(41,128,185,0.2)',
        fill: true,
        tension: 0.3
      }]
    },
    options: { responsive: true }
  });

  // Gauge Function
   function createGauge(id, value, max, color) {
      const ctx = document.getElementById(id).getContext('2d');
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          datasets: [{
            data: [value, max - value],
            backgroundColor: [color, '#eaeaea'],
            borderWidth: 0,
            cutout: '70%'
          }]
        },
        options: {
          responsive: false,
          plugins: {
            legend: { display: false },
            tooltip: { enabled: false },
            // បង្ហាញ value នៅកណ្តាល
            annotation: {}
          }
        },
        plugins: [{
          id: 'textCenter',
          afterDraw(chart) {
            const {ctx, chartArea: {width, height}} = chart;
            ctx.save();
            ctx.font = 'bold 18px sans-serif';
            ctx.fillStyle = color;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(value + '%', width / 2, height / 2);
          }
        }]
      });
    }

    createGauge('gauge1', 73.59, 100, "#3498db");
    createGauge('gauge2', 94.56, 100, "#2ecc71");
    createGauge('gauge3', 90.85, 100, "#9b59b6");
    createGauge('gauge4', 63.21, 100, "#e74c3c");
</script>
{{-- Script to auto hide success message after 3 seconds --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const successMsg = document.querySelector('.success-message');
    if (successMsg) {
      setTimeout(() => {
        successMsg.style.transition = 'opacity 0.1s ease';
        successMsg.style.opacity = '0';
        setTimeout(() => {
          successMsg.remove();
        }, 100);
      }, 1000);
    }
  });
</script>
@endsection

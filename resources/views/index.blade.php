@extends('layouts.master')

@section('pageTitle')
    KR System PhoneStore
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .status-completed {
            color: green;
            font-weight: bold;
        }
        .status-cancelled {
            color: red;
            font-weight: bold;
        }
        #reorder{
            margin-bottom: 5px;
        }
        .success-message {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            width: 300px;
            height: 50px;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            margin-left: 500px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
            animation: fadeInUp 0.5s ease;
        }
        .success-message::before {
            content: '✓';
            margin-right: 8px;
            font-size: 24px;
            font-weight: bold;
        }
        @keyframes fadeInUp {
          0% { opacity: 0; transform: translateY(40px); }
          100% { opacity: 1; transform: translateY(0); }
        }
    </style>
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

    <div class="table-container">
        <h3><i class="fas fa-shopping-cart" id="reorder"></i> Recent Orders</h3>
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
                            <span class="status-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
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

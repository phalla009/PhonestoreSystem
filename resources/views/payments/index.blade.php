@extends('layouts.master')

@section('pageTitle')
   Payments Listing
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <script src="{{ URL::asset('js/delete_form.js') }}"></script>

    <style>
        .filter-search-wrap { display: flex; gap: 12px; margin-bottom: 20px; align-items: center; flex-wrap: wrap; }
        .filter-search-wrap input[type="text"] { padding: 10px 16px; border: 1px solid #ccc; border-radius: 24px; width: 300px; font-size: 15px; outline-color: #3498db; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .filter-search-wrap .btn { padding: 10px 18px; font-size: 14px; border-radius: 24px; }
        .btn.btn-light { background-color: #c82333; color: white; border-radius: 24px; }
        .btn.btn-light:hover { background-color: #a91c2a; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(56,161,105,0.3); }
    </style>
@endsection

@section('content')

{{-- ❌ លុប: #loading-overlay  — master layout មានហើយ --}}
{{-- ❌ លុប: #logout-confirm   — master layout មានហើយ --}}
{{-- ❌ លុប: logout JS         — master layout មានហើយ --}}

@if(session('success'))
<div id="successMessage" class="custom-success">
    <div class="success-content">
        <span class="success-icon">✔</span>
        <span class="success-text">{{ session('success') }}</span>
    </div>
    <div class="progress-bar"></div>
</div>
@endif

<div class="content-section" id="payments">
    <h2><i class="fas fa-credit-card"></i> All Payments</h2>

    {{-- Search Form --}}
    <form method="GET" action="{{ route('payments.index') }}"
          class="filter-search-wrap" id="filterForm"
          role="search" aria-label="Payment search form">
        <label for="customer_name" style="font-weight:600; color:#08051f; font-size:20px;">Search:</label>
        <input
            type="text"
            id="customer_name"
            name="customer_name"
            placeholder="Search by customer name..."
            value="{{ request('customer_name') }}"
        >
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>
        @if(request()->has('customer_name'))
            <a href="{{ route('payments.index') }}"
               class="btn btn-light page-link-loading"
               data-loading-text="Clearing filters...">
                <i class="fas fa-times"></i> Clear
            </a>
        @endif
    </form>

    <div class="table-container">
        <table cellpadding="8" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order Number</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Amount Paid</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td data-label="No">#{{ $loop->iteration }}</td>
                        <td data-label="Order Number">{{ $order->order_number ?? 'N/A' }}</td>
                        <td data-label="Customer">{{ $order->customer->name ?? 'N/A' }}</td>
                        <td data-label="Product">{{ $order->product->name ?? 'N/A' }}</td>
                        <td data-label="Amount Paid">${{ number_format($order->payments->sum('amount'), 2) }}</td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                @php $payment = $order->payments->first() @endphp

                                <a href="{{ route('payments.show', $payment->id) }}"
                                   class="action-btn show-btn page-link-loading"
                                   data-loading-text="Loading details..."
                                   title="View Details">
                                    <i class="fas fa-info-circle"></i>
                                </a>

                                <a href="{{ route('payments.edit', $payment->id) }}"
                                   class="action-btn edit-btn page-link-loading"
                                   data-loading-text="Loading edit..."
                                   title="Edit Payment">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <button type="button"
                                    class="action-btn delete-btn openDeleteModal"
                                    data-action="{{ route('payments.destroy', $payment->id) }}"
                                    title="Delete Payment">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;" id="found">No completed orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Completed orders missing a payment record --}}
    @if($unpaidOrders->isNotEmpty())
        <div style="margin-top: 30px;">
            <h4 style="color:#856404; margin-bottom:10px;">
                <i class="fas fa-exclamation-triangle"></i>
                Completed Orders — Missing Payment Record ({{ $unpaidOrders->count() }}) form POS
            </h4>
            <div class="table-container">
                <table cellpadding="8" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Order Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unpaidOrders as $i => $order)
                            <tr style="background:#fffbf0;">
                                <td>#{{ $i + 1 }}</td>
                                <td>{{ $order->order_number ?? 'N/A' }}</td>
                                <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                <td>{{ $order->product->name ?? 'N/A' }}</td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('orders.index') }}"
                                       class="action-btn show-btn page-link-loading"
                                       style="width:auto; padding:6px 12px; font-size:12px; gap:5px;"
                                       data-loading-text="Going to orders..."
                                       title="Go to Orders to re-process payment">
                                        <i class="fas fa-arrow-right"></i> Fix in Orders
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteConfirmModal" class="modal">
    <div class="delete-modal-box">
        <div class="delete-modal-icon"><i class="fas fa-trash-alt"></i></div>
        <button class="delete-modal-close" id="deleteModalClose" aria-label="Close">&times;</button>
        <h3>Delete Record?</h3>
        <p>This action <strong>cannot be undone.</strong> Are you sure you want to permanently delete this record?</p>
        <form id="deleteForm" method="POST" action="">
            @csrf @method('DELETE')
            <div class="delete-modal-actions">
                <button type="button" id="cancelDelete" class="delete-btn-cancel"><i class="fas fa-times"></i> Cancel</button>
                <button type="submit" id="confirmDeleteBtn" class="delete-btn-confirm"><i class="fas fa-trash-alt"></i> Yes, Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
    // ✅ reuse overlay ពី master layout
    function showLoading(msg) {
        const ov = document.getElementById('loading-overlay');
        const lt = document.getElementById('loading-text');
        if (!ov) return;
        if (lt) lt.textContent = msg || 'Loading...';
        ov.style.display = 'flex';
    }

    // Page nav links — class "page-link-loading"
    document.querySelectorAll('.page-link-loading').forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const msg  = this.getAttribute('data-loading-text') || 'Loading...';
            if (href && href !== '#' && href !== 'javascript:void(0)') {
                e.preventDefault();
                showLoading(msg);
                window.location.href = href;
            }
        });
    });

    // Filter form submit
    document.getElementById('filterForm').addEventListener('submit', function() {
        showLoading('Filtering...');
    });

    // Single delete
    document.querySelectorAll('.openDeleteModal').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('deleteForm').setAttribute('action', this.getAttribute('data-action'));
            document.getElementById('deleteConfirmModal').style.display = 'flex';
        });
    });
    document.getElementById('deleteModalClose').addEventListener('click', function() { document.getElementById('deleteConfirmModal').style.display = 'none'; });
    document.getElementById('cancelDelete').addEventListener('click', function() { document.getElementById('deleteConfirmModal').style.display = 'none'; });
    document.getElementById('confirmDeleteBtn').addEventListener('click', function(e) {
        e.preventDefault();
        showLoading('Deleting...');
        setTimeout(function() { document.getElementById('deleteForm').submit(); }, 300);
    });

    // Close modal on outside click / Escape key
    window.addEventListener('click', function(e) {
        if (e.target === document.getElementById('deleteConfirmModal')) {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }
    });
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.getElementById('deleteConfirmModal').style.display = 'none';
    });

    // Auto-hide toast
    const successMsg = document.getElementById('successMessage');
    if (successMsg) {
        setTimeout(function() {
            successMsg.style.transition = 'opacity 0.5s';
            successMsg.style.opacity = '0';
            setTimeout(function() { successMsg.style.display = 'none'; }, 500);
        }, 3000);
    }
</script>

@endsection
@extends('layouts.master')

@section('pageTitle')
    KR System PhoneStore
@endsection

@section('headerBlock')
<link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
<script src="{{ URL::asset('js/form.js') }}"></script>
<link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
<script src="{{ URL::asset('js/delete_form.js') }}"></script>

<style>
    .action-buttons {
        margin-left: -20px;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .status-pending { color: orange; }
    .status-completed { color: green; }
    .status-cancelled { color: red; }

    .payment-btn.disabled {
        pointer-events: none;
        opacity: 0.5;
        cursor: default;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0; top: 0;
        width: 100%; height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 60px auto;
        padding: 20px;
        border-radius: 10px;
        width: 600px;
        height: 480px;
        position: relative;
        transition: all 0.3s ease;
        transform: scale(0.95);
        animation: fadeIn 0.3s forwards;
        overflow-y: auto;
    }

    @keyframes fadeIn {
        to { transform: scale(1); }
    }

    .btn-payment {
        margin-top: 20px;
        width: 100%;
        background: #48bb78;
        color: white;
        transition: 0.3s ease, transform 0.2s ease;
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
    }

    .btn-payment:hover {
        background: #38a169;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3);
    }

    .close-modal {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        color: #333;
    }

    .close-modal:hover {
        color: #666;
    }

    .input-error {
        border: 1px solid red;
    }

    .error {
        color: red;
        margin-top: 4px;
        font-size: 0.9em;
    }

    .custom-success {
        padding: 12px 20px;
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 6px;
        margin-bottom: 15px;
        animation: fadeOut 4s ease forwards;
    }

    .filter-section {
        margin-top: -50px;
        margin-bottom: 20px;
    }

    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .filter-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .grid-3-columns {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .filter-form .form-group {
        display: flex;
        flex-direction: column;
    }

    .filter-form label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #333;
    }

    .filter-form select,
    .filter-form input[type="text"] {
        padding: 10px 14px;
        border: 1px solid #ccc;
        border-radius: 24px;
        font-size: 14px;
        outline-color: #3498db;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .filter-form .btn {
        padding: 10px 14px;
        font-size: 14px;
        border-radius: 24px;
        margin-right: 8px;
    }

    .filter-form .btn-light {
        background-color: #c82333;
        color: white; 
    }
    .filter-form .btn-light:hover {
        background-color: #a91c2a;
        border-color: #bd2130;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3);
    }

    .filter-clear-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-top: 15px;
    }

    @keyframes fadeOut {
        0% { opacity: 1; }
        80% { opacity: 1; }
        100% { opacity: 0; display: none; }
    }
</style>
@endsection

@section('content')

@if(session('success'))
    <div id="successMessage" class="custom-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="content-section" id="orders">
    <h2><i class="fas fa-shopping-cart"></i> Orders Management</h2>

    <div class="filter-section">
        <div class="filter-header">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Order
            </a>
        </div>

        <form method="GET" action="{{ route('orders.index') }}" class="filter-form">
            <div class="grid-3-columns">
                <div class="form-group">
                    <label for="customer_id">Customer</label>
                    <select name="customer_id" id="customer_id">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="customer name...">
                </div>
            </div>

            <div class="filter-clear-wrap">
                <div class="form-group" style="flex: 1;">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
                @if(request()->hasAny(['search', 'customer_id', 'status']))
                    <div class="form-group" style="flex: 1;">
                        <a href="{{ route('orders.index') }}" class="btn btn-light w-100">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Paid Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="ordersTable">
                @forelse ($orders as $order)
                <tr>
                    <td data-label="Order ID">{{ $order->order_number }}</td>
                    <td data-label="Customer">{{ $order->customer->name ?? 'N/A' }}</td>
                    <td data-label="Product">{{ $order->product->name ?? 'N/A' }}</td>
                    <td data-label="Quantity">{{ $order->quantity }}</td>
                    <td data-label="Paid Amount">${{ number_format($order->payments_sum_amount ?? 0, 2) }}</td>
                    <td data-label="Status">
                        <span class="status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td data-label="Actions">
                        <div class="action-buttons">
                            <a href="javascript:void(0);"
                               class="action-btn payment-btn open-payment-modal {{ in_array($order->status, ['completed', 'cancelled']) ? 'disabled' : '' }}"
                               data-order-id="{{ $order->id }}">
                               <i class="fas fa-credit-card"></i> Pay
                            </a>
                            <a href="{{ route('orders.show', $order->id) }}" class="action-btn show-btn">
                                <i class="fas fa-eye"></i> Show
                            </a>
                            <a href="{{ route('orders.edit', $order->id) }}" class="action-btn edit-btn">
                                <i class="fas fa-pen-to-square"></i> Edit
                            </a>
                            <button type="button" class="action-btn delete-btn openDeleteModal"
                                data-action="{{ route('orders.destroy', $order->id) }}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                         <td colspan="7" style="text-align:center;" id="found">No order found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content">
        <span class="close" id="deleteModalClose">&times;</span>
        <h3><i class="fas fa-trash-alt" style="color: #e74c3c; margin-right: 10px;"></i> Confirm Delete</h3>
        <p>Are you sure you want to delete this record?</p>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <button type="button" id="cancelDelete" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div id="paymentFormContainer">
            <!-- Payment form will be loaded here via AJAX -->
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('paymentModal');
        const container = document.getElementById('paymentFormContainer');

        // Open payment modal and load form via AJAX
        document.querySelectorAll('.open-payment-modal').forEach(btn => {
            btn.addEventListener('click', function () {
                if (this.classList.contains('disabled')) return;

                const orderId = this.getAttribute('data-order-id');
                fetch(`/payments/payment/${orderId}`)
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        modal.style.display = 'block';
                        attachPaymentFormScript();
                    });
            });
        });

        // Close modal on close button or outside modal
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('close-modal')) {
                modal.style.display = 'none';
                container.innerHTML = '';
            }
        });

        window.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                container.innerHTML = '';
            }
        });
    });

    function attachPaymentFormScript() {
        const paymentForm = document.getElementById('paymentForm');
        const paymentMethodSelect = document.getElementById('payment_method');
        const methodGroup = document.getElementById('payment-method-group');

        if (!paymentForm || !paymentMethodSelect || !methodGroup) return;

        paymentForm.addEventListener('submit', function (e) {
            // Remove existing error if present
            const existingError = document.querySelector('.payment-method-error');
            if (existingError) existingError.remove();
            paymentMethodSelect.classList.remove('input-error');
            paymentMethodSelect.removeAttribute('aria-describedby');

            if (paymentMethodSelect.value === '') {
                e.preventDefault();
                paymentMethodSelect.classList.add('input-error');
                paymentMethodSelect.setAttribute('aria-describedby', 'payment_method_error');

                const error = document.createElement('div');
                error.className = 'error payment-method-error';
                error.id = 'payment_method_error';
                error.innerText = 'Please select a payment method.';
                methodGroup.appendChild(error);
            }
        });

        paymentMethodSelect.addEventListener('change', function () {
            paymentMethodSelect.classList.remove('input-error');
            const error = document.querySelector('.payment-method-error');
            if (error) error.remove();
            paymentMethodSelect.removeAttribute('aria-describedby');
        });
    }
</script>

@endsection

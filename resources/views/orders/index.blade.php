@extends('layouts.master')

@section('pageTitle')
   Orders Listing
@endsection

@section('headerBlock')
<link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
<script src="{{ URL::asset('js/form.js') }}"></script>
<link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
<script src="{{ URL::asset('js/delete_form.js') }}"></script>

<style>

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

    @keyframes fadeIn { to { transform: scale(1); } }

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
        top: 10px; right: 15px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        color: #333;
    }
    .close-modal:hover { color: #666; }

    .input-error { border: 1px solid red; }
    .error { color: red; margin-top: 4px; font-size: 0.9em; }

    .custom-success {
        padding: 12px 20px;
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 6px;
        margin-bottom: 15px;
        animation: fadeOut 4s ease forwards;
    }

    .filter-section { margin-top: -50px; margin-bottom: 20px; }
    .filter-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .filter-form { display: flex; flex-direction: column; gap: 16px; }
    .grid-3-columns { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .filter-form .form-group { display: flex; flex-direction: column; }
    .filter-form label { font-weight: 600; margin-bottom: 6px; color: #333; }
    .filter-form select,
    .filter-form input[type="text"] {
        padding: 10px 14px;
        border: 1px solid #ccc;
        border-radius: 24px;
        font-size: 14px;
        outline-color: #3498db;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .filter-form .btn { padding: 10px 14px; font-size: 14px; border-radius: 24px; margin-right: 8px; }
    .filter-form .btn-light { background-color: #c82333; color: white; }
    .filter-form .btn-light:hover {
        background-color: #a91c2a;
        border-color: #bd2130;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3);
    }
    .filter-clear-wrap { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 15px; }

    @keyframes fadeOut {
        0% { opacity: 1; }
        80% { opacity: 1; }
        100% { opacity: 0; display: none; }
    }

    /* Loading Overlay */
    #loading-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.85);
        display: flex;
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
        <div id="loading-text">Loading...</div>
    </div>

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
                <a href="{{ route('orders.create') }}"
                   class="btn btn-primary nav-link-loading"
                   data-loading-text="Loading add...">
                    <i class="fas fa-circle-plus"></i> Add New Order
                </a>
            </div>

            <form method="GET" action="{{ route('orders.index') }}" class="filter-form" id="filterForm">
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
                            <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search"
                               value="{{ request('search') }}"
                               placeholder="customer name...">
                    </div>
                </div>

                <div class="filter-clear-wrap">
                    <div class="form-group" style="flex: 1;">
                        <button type="submit" class="btn btn-primary w-100" id="filterBtn">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                    @if(request()->hasAny(['search', 'customer_id', 'status']))
                        <div class="form-group" style="flex: 1;">
                            <a href="{{ route('orders.index') }}"
                               class="btn btn-light w-100 nav-link-loading"
                               data-loading-text="Clearing filters...">
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
                            @if($order->status === 'completed')
                                <i class="fas fa-check-circle" style="color: green; font-size: 20px;"></i>
                            @elseif($order->status === 'pending')
                                <i class="fas fa-hourglass-half" style="color: orange; font-size: 20px;"></i>
                            @elseif($order->status === 'cancelled')
                                <i class="fas fa-times-circle" style="color: red; font-size: 20px;"></i>
                            @endif
                        </td>
                        <td data-label="Actions">
                            <div class="action-buttons">

                                <a href="javascript:void(0);"
                                class="action-btn payment-btn open-payment-modal {{ in_array($order->status, ['completed', 'cancelled']) ? 'disabled' : '' }}"
                                data-order-id="{{ $order->id }}"
                                title="Make Payment">
                                    <i class="fas fa-credit-card"></i>
                                </a>

                                <a href="{{ route('orders.show', $order->id) }}"
                                class="action-btn show-btn nav-link-loading"
                                data-loading-text="Loading details..."
                                title="View Details">
                                    <i class="fas fa-info-circle"></i>
                                </a>

                                <a href="{{ route('orders.edit', $order->id) }}"
                                class="action-btn edit-btn nav-link-loading"
                                data-loading-text="Opening editor..."
                                title="Edit Order">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>

                                <button type="button"
                                    class="action-btn delete-btn openDeleteModal"
                                    data-action="{{ route('orders.destroy', $order->id) }}"
                                    title="Delete Order">
                                    <i class="fas fa-trash"></i>
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
        <div class="delete-modal-box">
            <div class="delete-modal-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <button class="delete-modal-close" id="deleteModalClose" aria-label="Close">&times;</button>
            <h3>Delete Record?</h3>
            <p>This action <strong>cannot be undone.</strong> Are you sure you want to permanently delete this record?</p>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="delete-modal-actions">
                    <button type="button" id="cancelDelete" class="delete-btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="delete-btn-confirm">
                        <i class="fas fa-trash-alt"></i> Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="paymentFormContainer"></div>
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
             // =============================================
        // Logout confirm
        // =============================================
        const logoutLink    = document.getElementById('logout-link');
        const logoutConfirm = document.getElementById('logout-confirm');
        const confirmYes    = document.getElementById('confirm-yes');
        const confirmNo     = document.getElementById('confirm-no');
        const logoutForm    = document.getElementById('logout-form');

        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            logoutConfirm.style.display = 'flex';
        });
        confirmYes.addEventListener('click', function() { logoutForm.submit(); });
        confirmNo.addEventListener('click',  function() { logoutConfirm.style.display = 'none'; });

        const overlay     = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        function showLoading(message) {
            loadingText.textContent = message || 'Loading...';
            overlay.style.display = 'flex';
        }

        // ✅ Hide overlay once page fully loads
        window.addEventListener('load', function () {
            overlay.style.display = 'none';
        });

        // Add / Show / Edit / Clear links
        document.querySelectorAll('.nav-link-loading').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                const msg  = this.getAttribute('data-loading-text') || 'Loading...';
                if (href && href !== '#') {
                    showLoading(msg);
                    window.location.href = href;
                }
            });
        });

        // Filter form submit
        document.getElementById('filterForm').addEventListener('submit', function() {
            showLoading('Filtering...');
        });

        // Delete → open modal
        document.querySelectorAll('.openDeleteModal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('deleteForm').setAttribute('action', this.getAttribute('data-action'));
                document.getElementById('deleteConfirmModal').style.display = 'flex';
            });
        });

        // Close delete modal
        document.getElementById('deleteModalClose').addEventListener('click', function() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });

        // ✅ Confirm delete → show loading on submit
        document.getElementById('deleteForm').addEventListener('submit', function() {
            showLoading('Deleting...');
        });

        // ── Payment Modal ──────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            const paymentModal     = document.getElementById('paymentModal');
            const paymentContainer = document.getElementById('paymentFormContainer');

            document.querySelectorAll('.open-payment-modal').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    if (this.classList.contains('disabled')) return;
                    const orderId = this.getAttribute('data-order-id');
                    fetch(`/payments/payment/${orderId}`)
                        .then(function(r) { return r.text(); })
                        .then(function(html) {
                            paymentContainer.innerHTML = html;
                            paymentModal.style.display = 'block';
                            attachPaymentFormScript();
                        });
                });
            });

            // Close on × click
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('close-modal')) {
                    paymentModal.style.display = 'none';
                    paymentContainer.innerHTML = '';
                }
            });

            // Close on backdrop click
            window.addEventListener('click', function(e) {
                if (e.target === paymentModal) {
                    paymentModal.style.display = 'none';
                    paymentContainer.innerHTML = '';
                }
            });
        });

        function attachPaymentFormScript() {
            const paymentForm         = document.getElementById('paymentForm');
            const paymentMethodSelect = document.getElementById('payment_method');
            const methodGroup         = document.getElementById('payment-method-group');

            if (!paymentForm || !paymentMethodSelect || !methodGroup) return;

            paymentForm.addEventListener('submit', function(e) {
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

            paymentMethodSelect.addEventListener('change', function() {
                paymentMethodSelect.classList.remove('input-error');
                const error = document.querySelector('.payment-method-error');
                if (error) error.remove();
                paymentMethodSelect.removeAttribute('aria-describedby');
            });
        }

        // Auto-hide success message
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
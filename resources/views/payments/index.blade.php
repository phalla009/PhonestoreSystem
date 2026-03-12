@extends('layouts.master')

@section('pageTitle')
   Payments Listing
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
    <script src="{{ URL::asset('js/delete_form.js') }}"></script>

    <style>
       
        .filter-search-wrap { display: flex; gap: 12px; margin-bottom: 20px; align-items: center; flex-wrap: wrap; }
        .filter-search-wrap input[type="text"] { padding: 10px 16px; border: 1px solid #ccc; border-radius: 24px; width: 300px; font-size: 15px; outline-color: #3498db; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .filter-search-wrap .btn { padding: 10px 18px; font-size: 14px; border-radius: 8px; }
        .filter-form .btn-light { background-color: #c82333; color: white; }
        .filter-form .btn-light:hover { background-color: #a91c2a; border-color: #bd2130; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3); }
        .custom-success { padding: 12px 20px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 6px; margin-bottom: 15px; animation: fadeOut 4s ease forwards; }
        .btn.btn-primary { border-radius: 24px; margin-top: 2px; }
        .btn.btn-light { border-radius: 24px; margin-top: 2px; background-color: #c82333; color: white; }
        .btn-light:hover { background-color: #a91c2a; border-color: #bd2130; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3); }
        @keyframes fadeOut { 0% { opacity: 1; } 80% { opacity: 1; } 100% { opacity: 0; display: none; } }

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
        <div id="loading-text">Loading...</div>
    </div>

    @if(session('success'))
        <div id="successMessage" class="custom-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="content-section" id="payments">
        <h2><i class="fas fa-credit-card"></i> All Payments</h2>

        <!-- Search Form -->
        <form method="GET" action="{{ route('payments.index') }}"
              class="filter-search-wrap" id="filterForm"
              role="search" aria-label="Payment search form">
            <label for="customer_name" style="font-weight: 600; color: #08051f; font-size: 20px;">Search:</label>
            <input
                type="text"
                id="customer_name"
                name="customer_name"
                placeholder="Search by customer name..."
                value="{{ request('customer_name') }}"
                aria-describedby="searchDescription"
            >
            <button type="submit" class="btn btn-primary" aria-label="Filter payments">
                <i class="fas fa-search"></i> Filter
            </button>
            @if(request()->has('customer_name'))
                <a href="{{ route('payments.index') }}"
                   class="btn btn-light nav-link-loading"
                   data-loading-text="Clearing filters..."
                   aria-label="Clear search filter">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>

        <div class="table-container" role="region" aria-live="polite" aria-relevant="all" aria-label="Payments table">
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
                    @forelse($payments as $payment)
                        <tr>
                            <td data-label="No">#{{ $loop->iteration }}</td>
                            <td data-label="Order Number">{{ $payment->order->order_number ?? 'N/A' }}</td>
                            <td data-label="Customer">{{ $payment->order->customer->name ?? 'N/A' }}</td>
                            <td data-label="Product">{{ $payment->order->product->name ?? 'N/A' }}</td>
                            <td data-label="Amount Paid">${{ number_format($payment->amount, 2) }}</td>
                            <td data-label="Actions">
                            <div class="action-buttons">

                                <a href="{{ route('payments.show', $payment->id) }}"
                                class="action-btn show-btn nav-link-loading"
                                data-loading-text="Loading details..."
                                title="View Details">
                                    <i class="fas fa-info-circle"></i>
                                </a>

                                <a href="{{ route('payments.edit', $payment->id) }}"
                                class="action-btn edit-btn nav-link-loading"
                                data-loading-text="Loading add..."
                                title="Edit Payment">
                                    <i class="fas fa-pen-to-square"></i>
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
                            <td colspan="6" style="text-align:center;" id="found">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
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

        // Show / Edit / Clear links
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

        document.addEventListener('DOMContentLoaded', function() {
            const modal        = document.getElementById('deleteConfirmModal');
            const deleteForm   = document.getElementById('deleteForm');
            const closeModalBtn  = document.getElementById('deleteModalClose');
            const cancelDeleteBtn = document.getElementById('cancelDelete');

            // Delete modal open
            document.querySelectorAll('.openDeleteModal').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    deleteForm.action = this.getAttribute('data-action');
                    modal.style.display = 'block';
                    modal.querySelector('.modal-content').focus();
                });
            });

            // Close modal
            closeModalBtn.addEventListener('click', function() { modal.style.display = 'none'; });
            cancelDeleteBtn.addEventListener('click', function() { modal.style.display = 'none'; });
            window.addEventListener('click', function(e) { if (e.target === modal) modal.style.display = 'none'; });
            window.addEventListener('keydown', function(e) { if (e.key === 'Escape' && modal.style.display === 'block') modal.style.display = 'none'; });

            // Confirm delete → show loading then submit
            document.getElementById('confirmDeleteBtn').addEventListener('click', function(e) {
                e.preventDefault();
                showLoading('Deleting...');
                setTimeout(function() {
                    deleteForm.submit();
                }, 300);
            });

            // Auto-hide success message
            const successMsg = document.getElementById('successMessage');
            if (successMsg) {
                setTimeout(function() {
                    successMsg.style.transition = 'opacity 0.5s';
                    successMsg.style.opacity = '0';
                    setTimeout(function() { successMsg.style.display = 'none'; }, 500);
                }, 3000);
            }
        });
    </script>

@endsection
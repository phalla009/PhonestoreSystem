@extends('layouts.master')

@section('pageTitle')
    All Payments
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
    <script src="{{ URL::asset('js/delete_form.js') }}"></script>

    <style>
        .action-buttons {
            margin-left: -30px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-search-wrap {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-search-wrap input[type="text"] {
            padding: 10px 16px;
            border: 1px solid #ccc;
            border-radius: 24px;
            width: 300px;
            font-size: 15px;
            outline-color: #3498db;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .filter-search-wrap .btn {
            padding: 10px 18px;
            font-size: 14px;
            border-radius: 8px;
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
        .custom-success {
            padding: 12px 20px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            margin-bottom: 15px;
            animation: fadeOut 4s ease forwards;
        }
        .btn.btn-primary {
            border-radius: 24px;
            margin-top: 2px;
        }
        .btn.btn-light {
            border-radius: 24px;
            margin-top: 2px;
            background-color: #c82333;
            color: white; 
        }
        .btn-light:hover {
            background-color: #a91c2a;
            border-color: #bd2130;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3);
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

<div class="content-section" id="payments">
    <h2><i class="fas fa-credit-card"></i> All Payments</h2>

    <!-- Search Form -->
    <form method="GET" action="{{ route('payments.index') }}" class="filter-search-wrap" role="search" aria-label="Payment search form">
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
            <a href="{{ route('payments.index') }}" class="btn btn-light" aria-label="Clear search filter">
                <i class="fas fa-times"></i> Clear
            </a>
        @endif
    </form>

    <div class="table-container" role="region" aria-live="polite" aria-relevant="all" aria-label="Payments table">
        <table cellpadding="8" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Payment ID</th>
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
                        <td data-label="Payment ID">{{ $payment->id }}</td>
                        <td data-label="Order Number">{{ $payment->order->order_number ?? 'N/A' }}</td>
                        <td data-label="Customer">{{ $payment->order->customer->name ?? 'N/A' }}</td>
                        <td data-label="Product">{{ $payment->order->product->name ?? 'N/A' }}</td>
                        <td data-label="Amount Paid">${{ number_format($payment->amount, 2) }}</td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="{{ route('payments.show', $payment->id) }}" class="action-btn show-btn">
                                    <i class="fas fa-eye"></i> Show
                                </a>
                                <a href="{{ route('payments.edit', $payment->id) }}" class="action-btn edit-btn">
                                    <i class="fas fa-pen-to-square"></i> Edit
                                </a>
                                <button type="button" class="action-btn delete-btn openDeleteModal"
                                    data-action="{{ route('payments.destroy', $payment->id) }}">
                                    <i class="fas fa-trash"></i> Delete
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
<div id="deleteConfirmModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle" aria-describedby="deleteModalDesc">
    <div class="modal-content">
        <span class="close" id="deleteModalClose" role="button" tabindex="0" aria-label="Close delete confirmation modal">&times;</span>
        <h3 id="deleteModalTitle">
            <i class="fas fa-trash-alt" style="color: #e74c3c; margin-right: 10px;"></i>
            Confirm Delete
        </h3>
        <p id="deleteModalDesc">Are you sure you want to delete this payment?</p>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <button type="button" id="cancelDelete" class="btn btn-secondary" aria-label="Cancel deletion">Cancel</button>
            <button type="submit" class="btn btn-danger" aria-label="Confirm deletion">Yes, Delete</button>
        </form>
    </div>
</div>

<!-- Delete Modal JS -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('deleteConfirmModal');
        const deleteForm = document.getElementById('deleteForm');
        const closeModalBtn = document.getElementById('deleteModalClose');
        const cancelDeleteBtn = document.getElementById('cancelDelete');
        const deleteButtons = document.querySelectorAll('.openDeleteModal');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const action = btn.getAttribute('data-action');
                deleteForm.action = action;
                modal.style.display = 'block';
                // Focus modal for accessibility
                modal.querySelector('.modal-content').focus();
            });
        });

        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        cancelDeleteBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });

        // Optional: close modal with ESC key
        window.addEventListener('keydown', function(event) {
            if (event.key === "Escape" && modal.style.display === 'block') {
                modal.style.display = 'none';
            }
        });
    });
</script>

@endsection

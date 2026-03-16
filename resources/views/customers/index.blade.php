@extends('layouts.master')

@section('pageTitle')
    Customers Listing
@endsection

@section('headerBlock')
<link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
<link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
<script src="{{ URL::asset('js/form.js') }}"></script>
<script src="{{ URL::asset('js/delete_form.js') }}"></script>

<style>
    .status-active   { color: green; background-color: transparent !important; }
    .status-inactive { color: red;   background-color: transparent !important; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
    thead tr { background-color: #f7f7f7; }
    th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; vertical-align: middle; word-wrap: break-word; }
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

<div class="content-section" id="customers">
    <h2><i class="fas fa-users"></i> Customer Management</h2>

    <div class="filter-section">
        <div class="filter-controls">
            <a href="{{ route('customers.create') }}"
               class="btn btn-primary page-link-loading"
               data-loading-text="Loading add...">
                <i class="fas fa-circle-plus"></i> Add New Customer
            </a>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="customersTable">
                @forelse ($customers as $customer)
                    <tr>
                        <td data-label="No">#{{ $loop->iteration }}</td>
                        <td data-label="Name"><i class="fas fa-user user-icon"></i>{{ $customer->name }}</td>
                        <td data-label="Gender">{{ ucfirst($customer->gender) }}</td>
                        <td data-label="Phone">{{ $customer->phone }}</td>
                        <td data-label="Status">
                            @if(strtolower($customer->status) === 'active')
                                <i class="fas fa-check-circle" style="color:green;font-size:20px;"></i>
                            @elseif(strtolower($customer->status) === 'inactive')
                                <i class="fas fa-times-circle" style="color:red;font-size:20px;"></i>
                            @endif
                        </td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="{{ route('customers.show', $customer->id) }}"
                                   class="action-btn show-btn page-link-loading"
                                   data-loading-text="Loading details..." title="View Details">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer->id) }}"
                                   class="action-btn edit-btn page-link-loading"
                                   data-loading-text="Opening editor..." title="Edit Customer">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button type="button"
                                    class="action-btn delete-btn openDeleteModal"
                                    data-action="{{ route('customers.destroy', $customer->id) }}"
                                    title="Delete Customer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;" id="found">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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

    // Close modal on outside click
    window.addEventListener('click', function(e) {
        if (e.target === document.getElementById('deleteConfirmModal')) {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }
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
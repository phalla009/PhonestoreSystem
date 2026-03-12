@extends('layouts.master')

@section('pageTitle')
    Customers Listing
@endsection

@section('headerBlock')
<link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
<script src="{{ URL::asset('js/form.js') }}"></script>
<link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
<script src="{{ URL::asset('js/delete_form.js') }}"></script>

<style>
    .status-active  { color: green; background-color: transparent !important; }
    .status-inactive { color: red;  background-color: transparent !important; }
    .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
    thead tr { background-color: #f7f7f7; }
    th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; vertical-align: middle; word-wrap: break-word; }

    .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
    .modal-content { background-color: #fff; margin: 60px auto; padding: 20px; border-radius: 10px; width: 500px; max-width: 95vw; position: relative; box-sizing: border-box; }
    .close { position: absolute; top: 10px; right: 15px; font-size: 24px; font-weight: bold; cursor: pointer; color: #333; }
    .close:hover { color: #666; }

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

    <div class="content-section" id="customers">
        <h2><i class="fas fa-users"></i> Customer Management</h2>

        <div class="filter-section">
            <div class="filter-controls">
                <a href="{{ route('customers.create') }}"
                   class="btn btn-primary nav-link-loading"
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
                            <td data-label="No">#{{ $customer->id }}</td>
                            <td data-label="Name"><i class="fas fa-user user-icon"></i>{{ $customer->name }}</td>
                            <td data-label="Gender">{{ ucfirst($customer->gender) }}</td>
                            <td data-label="Phone">{{ $customer->phone }}</td>
                            <td data-label="Status">
                                @if(strtolower($customer->status) === 'active')
                                    <i class="fas fa-check-circle" style="color: green; margin-right: 5px;"></i>
                                    <span class="status-active">{{ ucfirst($customer->status) }}</span>
                                @elseif(strtolower($customer->status) === 'inactive')
                                    <i class="fas fa-times-circle" style="color: red; margin-right: 5px;"></i>
                                    <span class="status-inactive">{{ ucfirst($customer->status) }}</span>
                                @endif
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <a href="{{ route('customers.show', $customer->id) }}"
                                       class="action-btn show-btn nav-link-loading"
                                       data-loading-text="Loading details...">
                                        <i class="fas fa-eye"></i> Show
                                    </a>
                                    <a href="{{ route('customers.edit', $customer->id) }}"
                                       class="action-btn edit-btn nav-link-loading"
                                       data-loading-text="Opening editor...">
                                        <i class="fas fa-pen-to-square"></i> Edit
                                    </a>
                                    <button type="button" class="action-btn delete-btn openDeleteModal"
                                        data-action="{{ route('customers.destroy', $customer->id) }}">
                                        <i class="fas fa-trash"></i> Delete
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

        // Add / Show / Edit links
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

        document.addEventListener('DOMContentLoaded', function() {
            // Delete modal open
            document.querySelectorAll('.openDeleteModal').forEach(function(button) {
                button.addEventListener('click', function() {
                    document.getElementById('deleteForm').action = this.getAttribute('data-action');
                    document.getElementById('deleteConfirmModal').style.display = 'block';
                });
            });

            // Close modal
            document.getElementById('deleteModalClose').addEventListener('click', function() {
                document.getElementById('deleteConfirmModal').style.display = 'none';
                document.getElementById('deleteForm').action = '';
            });
            document.getElementById('cancelDelete').addEventListener('click', function() {
                document.getElementById('deleteConfirmModal').style.display = 'none';
                document.getElementById('deleteForm').action = '';
            });
            window.addEventListener('click', function(e) {
                if (e.target === document.getElementById('deleteConfirmModal')) {
                    document.getElementById('deleteConfirmModal').style.display = 'none';
                    document.getElementById('deleteForm').action = '';
                }
            });

            // Confirm delete → show loading then submit
            document.getElementById('confirmDeleteBtn').addEventListener('click', function(e) {
                e.preventDefault();
                showLoading('Deleting...');
                setTimeout(function() {
                    document.getElementById('deleteForm').submit();
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
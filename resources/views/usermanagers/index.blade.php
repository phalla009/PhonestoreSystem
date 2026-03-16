@extends('layouts.master')

@section('pageTitle')
    User Managers Listing
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <script src="{{ URL::asset('js/delete_form.js') }}"></script>
    <style>
        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            width: fit-content;
        }
        .role-admin   { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .role-manager { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .role-staff   { background: #dbeafe; color: #2563eb; border: 1px solid #bfdbfe; }
        .role-default { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
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

<div class="content-section" id="users">
    <h2><i class="fas fa-user-shield"></i> User Managers Management</h2>

    <div class="filter-section">
        <div class="filter-controls">
            <a href="{{ route('usermanagers.create') }}"
               class="btn btn-primary page-link-loading"
               data-loading-text="Loading add...">
                <i class="fas fa-circle-plus"></i> Add New User Manager
            </a>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="usersTable">
                @forelse ($users as $user)
                    <tr>
                        <td data-label="No">#{{ $loop->iteration }}</td>
                        <td data-label="Full Name">
                            <i class="fas fa-user user-icon"></i> {{ $user->name }}
                        </td>
                        <td data-label="Email">{{ $user->email }}</td>
                        <td data-label="Role">
                            @php
                                $roleName  = $user->role->role_name ?? 'N/A';
                                $roleClass = match(strtolower($roleName)) {
                                    'admin'   => 'role-admin',
                                    'manager' => 'role-manager',
                                    'staff'   => 'role-staff',
                                    default   => 'role-default',
                                };
                            @endphp
                            <span class="role-badge {{ $roleClass }}">{{ $roleName }}</span>
                        </td>
                        <td data-label="Created At">{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="{{ route('usermanagers.show', $user->id) }}"
                                   class="action-btn show-btn page-link-loading"
                                   data-loading-text="Loading details..."
                                   title="View Details">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="{{ route('usermanagers.edit', $user->id) }}"
                                   class="action-btn edit-btn page-link-loading"
                                   data-loading-text="Opening editor..."
                                   title="Edit User">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button type="button"
                                    class="action-btn delete-btn openDeleteModal"
                                    data-action="{{ route('usermanagers.destroy', $user->id) }}"
                                    title="Delete User">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;" id="found">No users found.</td>
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
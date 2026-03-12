@extends('layouts.master')

@section('pageTitle')
    User Managers Listing
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
    <script src="{{ URL::asset('js/form.js') }}" defer></script>
    <script src="{{ URL::asset('js/delete_form.js') }}" defer></script>
    <style>
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

    <div class="content-section" id="users">
        <h2><i class="fas fa-user-shield"></i> User Managers Management</h2>

        <div class="filter-section">
            <div class="filter-controls" style="display:flex; align-items:center; gap: 10px;">
                <a href="{{ route('usermanagers.create') }}" class="btn btn-primary" id="openCreateModal">
                    <i class="fas fa-circle-plus"></i> Add New User Manager
                </a>
            </div>
            <div id="sidebar">
                @yield('sidebar')
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
                                    $roleName = $user->role->role_name ?? 'N/A';
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
                                        class="action-btn show-btn nav-link"
                                        title="View Details">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    <a href="{{ route('usermanagers.edit', $user->id) }}"
                                        class="action-btn edit-btn nav-link"
                                        data-id="{{ $user->id }}"
                                        title="Edit User">
                                        <i class="fas fa-pen-to-square"></i>
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

    {{-- Logout Confirm --}}
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

        window.addEventListener('load', function () {
            overlay.style.display = 'none';
        });

        document.getElementById('openCreateModal').addEventListener('click', function (e) {
            e.preventDefault();
            showLoading('Loading add...');
            window.location.href = this.getAttribute('href');
        });

        document.querySelectorAll('a.nav-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const isEdit = this.classList.contains('edit-btn');
                showLoading(isEdit ? 'Loading editor...' : 'Loading details...');
                window.location.href = this.getAttribute('href');
            });
        });

        document.querySelectorAll('.openDeleteModal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('deleteForm').action = this.dataset.action;
                document.getElementById('deleteConfirmModal').style.display = 'flex';
            });
        });

        document.getElementById('deleteForm').addEventListener('submit', function () {
            showLoading('Deleting...');
        });

        document.getElementById('deleteModalClose').addEventListener('click', function () {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });
        document.getElementById('cancelDelete').addEventListener('click', function () {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });
    </script>

@endsection
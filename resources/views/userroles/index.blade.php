@extends('layouts.master')

@section('pageTitle')
    Roles Listing
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

    <div class="content-section" id="roles">
        <h2><i class="fas fa-user-shield"></i> Roles Management</h2>
        <div class="filter-section">
            <div class="filter-controls" style="display:flex; align-items:center; gap: 10px;">
                <a href="{{ route('userroles.create') }}" class="btn btn-primary" id="openCreateModal">
                    <i class="fas fa-circle-plus"></i> Add New Role
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
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="rolesTable">
                    @forelse ($roles as $role)
                        <tr>
                            <td data-label="No">#{{ $role->id }}</td>

                            <td data-label="Role Name">
                                @php
                                    $icon    = 'fas fa-user';
                                    $bgColor = '#7f8c8d';
                                    switch(strtolower($role->role_name ?? '')) {
                                        case 'admin':   $icon = 'fas fa-user-shield'; $bgColor = '#e74c3c'; break;
                                        case 'manager': $icon = 'fas fa-user-tie';    $bgColor = '#27ae60'; break;
                                        case 'staff':   $icon = 'fas fa-user';        $bgColor = '#2980b9'; break;
                                    }
                                @endphp
                                <i class="{{ $icon }}"
                                   style="color:#fff; background:{{ $bgColor }}; border-radius:50%;
                                          padding:5px; margin-right:5px; font-size:14px; width:25px; height:25px;
                                          display:inline-flex; align-items:center; justify-content:center;">
                                </i>
                                {{ $role->role_name }}
                            </td>

                            <td data-label="Description">{{ $role->description ?? 'N/A' }}</td>

                            <td data-label="Created At">
                                {{ $role->created_at ? $role->created_at->format('Y-m-d H:i') : 'N/A' }}
                            </td>

                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <a href="{{ route('userroles.show', $role->id) }}"
                                       class="action-btn show-btn nav-link">
                                        <i class="fas fa-eye"></i> Show
                                    </a>

                                    <a href="{{ route('userroles.edit', $role->id) }}"
                                       class="action-btn edit-btn nav-link" data-id="{{ $role->id }}">
                                        <i class="fas fa-pen-to-square"></i> Edit
                                    </a>

                                    <button type="button" class="action-btn delete-btn openDeleteModal"
                                            data-action="{{ route('userroles.destroy', $role->id) }}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;" id="found">No roles found.</td>
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

        // ✅ Hide overlay once page fully loads
        window.addEventListener('load', function () {
            overlay.style.display = 'none';
        });

        // Add New Role button
        document.getElementById('openCreateModal').addEventListener('click', function (e) {
            e.preventDefault();
            showLoading('Loading form...');
            window.location.href = this.getAttribute('href');
        });

        // Show & Edit links
        document.querySelectorAll('a.nav-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const isEdit = this.classList.contains('edit-btn');
                showLoading(isEdit ? 'Loading editor...' : 'Loading details...');
                window.location.href = this.getAttribute('href');
            });
        });

        // Delete button → populate form action & open modal
        document.querySelectorAll('.openDeleteModal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('deleteForm').action = this.dataset.action;
                document.getElementById('deleteConfirmModal').style.display = 'flex';
            });
        });

        // Delete confirm submit → show loading
        document.getElementById('deleteForm').addEventListener('submit', function () {
            showLoading('Deleting...');
        });

        // Close delete modal
        document.getElementById('deleteModalClose').addEventListener('click', function () {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });
        document.getElementById('cancelDelete').addEventListener('click', function () {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });
    </script>

@endsection
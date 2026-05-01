@extends('layouts.master')

@section('pageTitle')
    Roles Listing
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <script src="{{ URL::asset('js/delete_form.js') }}"></script>
    <style>
        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .search-wrapper .search-icon {
            position: absolute;
            left: 10px;
            color: #9ca3af;
            pointer-events: none;
            font-size: 14px;
        }
        .search-wrapper input[type="text"] {
            padding: 10px 12px 10px 32px;
            border: 1px solid #d1d5db;
            border-radius: 24px;
            font-size: 14px;
            width: 400px;
            margin-top: -5px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .search-wrapper input[type="text"]:focus {
            border-color: #132f4f;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }
        #noResultsRow { display: none; }
    </style>
@endsection

@section('content')

@if(session('success'))
<div id="successMessage" class="custom-success">
    <div class="success-content">
        <span class="success-icon">✔</span>
        <span class="success-text">{{ session('success') }}</span>
    </div>
    <div class="progress-bar"></div>
</div>
@endif

<div class="content-section" id="roles">
    <h2><i class="fas fa-user-shield"></i> Roles Management</h2>

    <div class="filter-section">
        <div class="filter-controls">
            <a href="{{ route('userroles.create') }}"
               class="btn btn-primary page-link-loading"
               data-loading-text="Loading form...">
                <i class="fas fa-circle-plus"></i> Add New Role
            </a>

            {{-- 🔍 Search Field --}}
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text"
                       id="roleSearch"
                       placeholder="Search name or description..."
                       autocomplete="off">
            </div>
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
                    <tr class="role-row">
                        <td data-label="No">#{{ $loop->iteration }}</td>
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
                        <td data-label="Created At">{{ $role->created_at ? $role->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="{{ route('userroles.show', $role->id) }}"
                                   class="action-btn show-btn page-link-loading"
                                   data-loading-text="Loading details..."
                                   title="View Details">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="{{ route('userroles.edit', $role->id) }}"
                                   class="action-btn edit-btn page-link-loading"
                                   data-loading-text="Opening editor..."
                                   title="Edit Role">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button type="button"
                                    class="action-btn delete-btn openDeleteModal"
                                    data-action="{{ route('userroles.destroy', $role->id) }}"
                                    title="Delete Role">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;" id="found">No roles found.</td>
                    </tr>
                @endforelse

                {{-- Shown when search yields no matches --}}
                <tr id="noResultsRow">
                    <td colspan="5" style="text-align:center; color:#6b7280;">
                        <i class="fas fa-search" style="margin-right:6px;"></i> No results match your search.
                    </td>
                </tr>
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

    // 🔍 Live search — filters by role name and description
    document.getElementById('roleSearch').addEventListener('input', function () {
        const term = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#rolesTable .role-row');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const name = row.querySelector('[data-label="Role Name"]')?.textContent.toLowerCase() ?? '';
            const desc = row.querySelector('[data-label="Description"]')?.textContent.toLowerCase() ?? '';

            const matches = name.includes(term) || desc.includes(term);
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        document.getElementById('noResultsRow').style.display = (visibleCount === 0) ? '' : 'none';
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
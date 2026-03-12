@extends('layouts.master')

@section('pageTitle')
    Categories Listing
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
    <script src="{{ URL::asset('js/form.js') }}" defer></script>
    <script src="{{ URL::asset('js/delete_form.js') }}" defer></script>

    <style>
        /* --- Loading Overlay --- */
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
            width: 60px;
            height: 60px;
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

    <div class="content-section" id="categories">
        <h2><i class="fas fa-tags"></i> Categories Management</h2>
        <div class="filter-section">
            <div class="filter-controls" style="display:flex; align-items:center; gap: 10px;">
                {{-- Add Button → show loading then navigate --}}
                <a href="{{ route('categories.create') }}"
                   class="btn btn-primary nav-link-loading"
                   data-loading-text="Loading add...">
                    <i class="fas fa-circle-plus"></i> Add New Brand
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
                        <th>Brand Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoriesTable">
                    @forelse ($categories as $category)
                        <tr>
                            <td data-label="No">#{{ $loop->iteration }}</td>
                            <td data-label="Category Name">{{ $category->name }}</td>
                            <td data-label="Created At">{{ $category->created_at ? $category->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td data-label="Actions">
                               <div class="action-buttons">
                                {{-- Show Button --}}
                                <a href="{{ route('categories.show', $category->id) }}"
                                class="action-btn show-btn nav-link-loading"
                                data-loading-text="Loading details..."
                                title="View Details">
                                    <i class="fas fa-info-circle"></i>
                                </a>

                                {{-- Edit Button --}}
                                <a href="{{ route('categories.edit', $category->id) }}"
                                class="action-btn edit-btn nav-link-loading"
                                data-loading-text="Opening editor..."
                                title="Edit Category">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>

                                {{-- Delete Button --}}
                                <button type="button"
                                    class="action-btn delete-btn openDeleteModal"
                                    data-action="{{ route('categories.destroy', $category->id) }}"
                                    title="Delete Category">
                                    <i class="fas fa-trash"></i>
                                </button>

                            </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;" id="found">No categories found.</td>
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

        const overlay   = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');
        function showLoading(message) {
            loadingText.textContent = message || 'Loading...';
            overlay.style.display = 'flex';
        }
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
        document.querySelectorAll('.openDeleteModal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                document.getElementById('deleteForm').setAttribute('action', action);
                document.getElementById('deleteConfirmModal').style.display = 'flex';
            });
        });

        // Close modal
        document.getElementById('deleteModalClose').addEventListener('click', function() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });
        document.getElementById('confirmDeleteBtn').addEventListener('click', function(e) {
            e.preventDefault();
            showLoading('Deleting...');
            setTimeout(function() {
                document.getElementById('deleteForm').submit();
            }, 300);
        });
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
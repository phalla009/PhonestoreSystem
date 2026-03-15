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

        /* --- Search Bar --- */
        .search-wrapper {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
        }
        .search-input-group {
            position: relative;
            display: flex;
            align-items: center;
             
        }
        .search-input-group .search-icon {
            position: absolute;
            left: 11px;
            color: #aaa;
            font-size: 13px;
            pointer-events: none;
            z-index: 1;
        }
        .search-input-group input[type="text"] {
            padding: 10px 36px 10px 32px;
            border: 1.5px solid #dde3ec;
            border-radius: 24px;
            font-size: 14px;
            color: #333;
            background: #f9fafc;
            width: 250px;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }
        .search-input-group input[type="text"]:focus {
            border-color: #3498db;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.13);
        }
        .search-input-group input[type="text"]::placeholder {
            color: #bbb;
        }
        .search-clear-btn {
            position: absolute;
            right: 9px;
            background: none;
            border: none;
            cursor: pointer;
            color: #bbb;
            font-size: 13px;
            padding: 0;
            display: flex;
            align-items: center;
            transition: color 0.15s;
        }
        .search-clear-btn:hover { color: #e74c3c; }
        .search-submit-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 10px 20px;
            background: #282828;
            color: #fff;
            border: none;
            border-radius: 24px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            text-decoration: none;
            white-space: nowrap;
        }
        .search-submit-btn:hover {
            background: #333;
            transform: translateY(-1px);
        }
        .search-reset-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 10px 20px;
            background: #f0f2f5;
            color: #666;
            border: 1.5px solid #dde3ec;
            border-radius: 24px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
            white-space: nowrap;
        }
        .search-reset-btn:hover {
            background: #e8eaf0;
            color: #e74c3c;
            border-color: #e74c3c;
        }
        .search-results-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #eaf4fd;
            color: #2980b9;
            border: 1px solid #b6d9f5;
            border-radius: 20px;
            padding: 3px 11px;
            font-size: 12.5px;
            font-weight: 500;
            white-space: nowrap;
        }
        .search-results-badge i { font-size: 11px; }
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
            <div class="success-content">
                <span class="success-icon">✔</span>
                <span class="success-text">{{ session('success') }}</span>
            </div>
            <div class="progress-bar"></div>
        </div>
    @endif

    <div class="content-section" id="categories">
        <h2><i class="fas fa-tags"></i> Categories Management</h2>
        <div class="filter-section">
            <div class="filter-controls" style="display:flex; align-items:center; gap: 12px; flex-wrap: wrap;">

                {{-- Add Button --}}
                <a href="{{ route('categories.create') }}"
                   class="btn btn-primary nav-link-loading"
                   data-loading-text="Loading add...">
                    <i class="fas fa-circle-plus"></i> Add New Brand
                </a>

                {{-- Search Form --}}
                <form method="GET" action="{{ route('categories.index') }}" class="search-wrapper" id="searchForm">
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text"
                               name="search"
                               id="searchInput"
                               value="{{ $search ?? '' }}"
                               placeholder="Search categories...">
                        @if(!empty($search))
                            <button type="button" class="search-clear-btn" id="clearSearchBtn" title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                    <button type="submit" class="search-submit-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('categories.index') }}" class="search-reset-btn" title="Show all">
                            <i class="fas fa-rotate-left"></i> Reset
                        </a>
                    @endif
                </form>

                {{-- Results Badge --}}
                @if(!empty($search))
                    <span class="search-results-badge">
                        <i class="fas fa-filter"></i>
                        {{ $categories->count() }} result{{ $categories->count() !== 1 ? 's' : '' }}
                        for &ldquo;{{ $search }}&rdquo;
                    </span>
                @endif

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
                            <td colspan="4" style="text-align:center; padding: 30px;" id="found">
                                @if(!empty($search))
                                    <i class="fas fa-search" style="font-size:24px; color:#ccc; display:block; margin-bottom:8px;"></i>
                                    No categories found matching &ldquo;<strong>{{ $search }}</strong>&rdquo;.
                                @else
                                    <i class="fas fa-tags" style="font-size:24px; color:#ccc; display:block; margin-bottom:8px;"></i>
                                    No categories found.
                                @endif
                            </td>
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

        // =============================================
        // Loading overlay
        // =============================================
        const overlay     = document.getElementById('loading-overlay');
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

        // =============================================
        // Inline clear button — clears input & submits
        // =============================================
        const clearBtn = document.getElementById('clearSearchBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                window.location.href = "{{ route('categories.index') }}";
            });
        }

        // =============================================
        // Delete Modal
        // =============================================
        document.querySelectorAll('.openDeleteModal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                document.getElementById('deleteForm').setAttribute('action', action);
                document.getElementById('deleteConfirmModal').style.display = 'flex';
            });
        });
        document.getElementById('deleteModalClose').addEventListener('click', function() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        });

        // =============================================
        // Success message auto-hide
        // =============================================
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
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
    /* Search Bar */
    .search-wrapper { display: flex; align-items: center; gap: 6px; margin-bottom: 10px; }
    .search-input-group { position: relative; display: flex; align-items: center; }
    .search-input-group .search-icon { position: absolute; left: 11px; color: #aaa; font-size: 13px; pointer-events: none; z-index: 1; }
    .search-input-group input[type="text"] {
        padding: 10px 36px 10px 32px; border: 1.5px solid #dde3ec; border-radius: 24px;
        font-size: 14px; color: #333; background: #f9fafc; width: 250px;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; outline: none;
    }
    .search-input-group input[type="text"]:focus { border-color: #3498db; background: #fff; box-shadow: 0 0 0 3px rgba(52,152,219,0.13); }
    .search-input-group input[type="text"]::placeholder { color: #bbb; }
    .search-clear-btn { position: absolute; right: 9px; background: none; border: none; cursor: pointer; color: #bbb; font-size: 13px; padding: 0; display: flex; align-items: center; transition: color 0.15s; }
    .search-clear-btn:hover { color: #e74c3c; }
    .search-submit-btn { display: flex; align-items: center; gap: 5px; padding: 10px 20px; background: #282828; color: #fff; border: none; border-radius: 24px; font-size: 14px; cursor: pointer; transition: background 0.2s, transform 0.1s; text-decoration: none; white-space: nowrap; }
    .search-submit-btn:hover { background: #333; transform: translateY(-1px); }
    .search-reset-btn { display: flex; align-items: center; gap: 5px; padding: 10px 20px; background: #f0f2f5; color: #666; border: 1.5px solid #dde3ec; border-radius: 24px; font-size: 13px; cursor: pointer; text-decoration: none; transition: background 0.2s, color 0.2s; white-space: nowrap; }
    .search-reset-btn:hover { background: #e8eaf0; color: #e74c3c; border-color: #e74c3c; }
    .search-results-badge { display: inline-flex; align-items: center; gap: 5px; background: #eaf4fd; color: #2980b9; border: 1px solid #b6d9f5; border-radius: 20px; padding: 3px 11px; font-size: 12.5px; font-weight: 500; white-space: nowrap; }
    .search-results-badge i { font-size: 11px; }

    /* Bulk delete */
    .bulk-actions-bar {
        display: none; align-items: center; gap: 10px; margin-bottom: 10px;
        background: #fff5f5; border: 1.5px solid #f5c2c2; border-radius: 10px; padding: 10px 14px;
    }
    .bulk-actions-bar.active { display: flex; }
    .bulk-actions-bar .selected-count { font-size: 13.5px; color: #c0392b; font-weight: 600; }
    .bulk-delete-btn {
        display: flex; align-items: center; gap: 6px; padding: 8px 16px; background: #e74c3c; color: #fff;
        border: none; border-radius: 20px; font-size: 13.5px; cursor: pointer; transition: background 0.2s;
    }
    .bulk-delete-btn:hover { background: #c0392b; }
    .bulk-delete-btn:disabled { background: #f1a9a0; cursor: not-allowed; }
    .bulk-clear-btn {
        display: flex; align-items: center; gap: 5px; padding: 8px 14px; background: #f0f2f5; color: #666;
        border: 1.5px solid #dde3ec; border-radius: 20px; font-size: 13px; cursor: pointer; text-decoration: none;
    }
    .bulk-clear-btn:hover { background: #e8eaf0; }
    .row-checkbox, #selectAllCheckbox { width: 16px; height: 16px; cursor: pointer; accent-color: #e74c3c; }
    tr.row-selected { background: #fff7f7; }

    /* ── Delete Confirm Modal ── */
    /* ── Backdrop ── */
    .delete-confirm-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1100;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .delete-confirm-modal.open {
        display: flex;
    }

    /* ── Modal Box ── */
    .delete-modal-box {
        background: #ffffff;
        border-radius: 20px;
        max-width: 440px;
        width: 100%;
        padding: 40px 36px 32px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18);
        position: relative;
        text-align: center;
        animation: deleteModalIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        margin: auto;
    }

    /* ── Icon ── */
    .delete-modal-icon {
        width: 70px;
        height: 70px;
        background: #fff1f0;
        border: 2px solid #ffd6d4;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .delete-modal-icon i {
        font-size: 28px;
        color: #e74c3c;
    }

    /* ── Close Button ── */
    .delete-modal-close {
        position: absolute;
        top: 16px;
        right: 18px;
        background: #f1f5f9;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        font-size: 18px;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s, color 0.2s, transform 0.2s;
        line-height: 1;
    }

    .delete-modal-close:hover {
        background: #fee2e2;
        color: #e74c3c;
        transform: rotate(90deg);
    }

    /* ── Heading ── */
    .delete-modal-box h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 10px;
    }

    /* ── Body Text ── */
    .delete-modal-box p {
        font-size: 0.95rem;
        color: #64748b;
        margin-bottom: 28px;
        line-height: 1.6;
    }

    .delete-modal-box p strong {
        color: #e74c3c;
    }

    /* ── Actions ── */
    .delete-modal-actions {
        display: flex;
        justify-content: center;
        gap: 12px;
    }

    /* ── Cancel Button ── */
    .delete-btn-cancel {
        padding: 10px 24px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s, transform 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .delete-btn-cancel:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }

    /* ── Confirm Button ── */
    .delete-btn-confirm {
        padding: 10px 24px;
        border-radius: 10px;
        border: none;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s, transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.35);
    }

    .delete-btn-confirm:hover {
        opacity: 0.9;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(231, 76, 60, 0.45);
    }

    /* ── Animation ── */
    @keyframes deleteModalIn {
        from { transform: scale(0.88) translateY(16px); opacity: 0; }
        to   { transform: scale(1)    translateY(0);    opacity: 1; }
    }

    /* ── Responsive ── */
    @media (max-width: 480px) {
        .delete-modal-box {
            padding: 32px 20px 24px;
        }

        .delete-modal-actions {
            flex-direction: column;
        }

        .delete-btn-cancel,
        .delete-btn-confirm {
            width: 100%;
            justify-content: center;
        }
    }

    /* Pagination */
    .categories-pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    
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

<div class="content-section" id="categories">
    <h2><i class="fas fa-tags"></i> Categories Management</h2>

    <div class="filter-section">
        <div class="filter-controls" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">

            {{-- Add --}}
            <a href="{{ route('categories.create') }}"
               class="btn btn-primary page-link-loading"
               data-loading-text="Loading form...">
                <i class="fas fa-circle-plus"></i> Add New Category
            </a>

            {{-- Search --}}
            <form method="GET" action="{{ route('categories.index') }}" class="search-wrapper" id="searchForm">
                <div class="search-input-group">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" id="searchInput"
                           value="{{ $search ?? '' }}"
                           placeholder="Search categories...">
                    @if(!empty($search))
                        <button type="button" class="search-clear-btn" id="clearSearchBtn" title="Clear">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
                <button type="submit" class="search-submit-btn">
                    <i class="fas fa-search"></i> Search
                </button>
                @if(!empty($search))
                    <a href="{{ route('categories.index') }}" class="search-reset-btn">
                        <i class="fas fa-rotate-left"></i> Reset
                    </a>
                @endif
            </form>

            {{-- Results badge --}}
            @if(!empty($search))
                <span class="search-results-badge">
                    <i class="fas fa-filter"></i>
                    {{ $categories->total() }} result{{ $categories->total() !== 1 ? 's' : '' }}
                    for &ldquo;{{ $search }}&rdquo;
                </span>
            @endif
        </div>
    </div>

    <div class="bulk-actions-bar" id="bulkActionsBar">
        <span class="selected-count" id="selectedCount">0 selected</span>
        <button type="button" class="bulk-delete-btn" id="bulkDeleteBtn" disabled>
            <i class="fas fa-trash"></i> Delete Selected
        </button>
        <button type="button" class="bulk-clear-btn" id="bulkClearBtn">
            <i class="fas fa-times"></i> Clear Selection
        </button>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:36px;"><input type="checkbox" id="selectAllCheckbox" title="Select all"></th>
                    <th>#</th>
                    <th>Category Name</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="categoriesTable">
                @forelse ($categories as $category)
                    <tr data-id="{{ $category->id }}">
                        <td data-label="Select">
                            <input type="checkbox" class="row-checkbox" value="{{ $category->id }}">
                        </td>
                        <td data-label="No">#{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                        <td data-label="Category Name">{{ $category->name }}</td>
                        <td data-label="Created At">{{ $category->created_at ? $category->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="{{ route('categories.show', $category->id) }}"
                                   class="action-btn show-btn page-link-loading"
                                   data-loading-text="Loading details..."
                                   title="View Details">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="{{ route('categories.edit', $category->id) }}"
                                   class="action-btn edit-btn page-link-loading"
                                   data-loading-text="Opening editor..."
                                   title="Edit Category">
                                    <i class="fas fa-pen"></i>
                                </a>
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
                        <td colspan="5" style="text-align:center; padding:30px;" id="found">
                            @if(!empty($search))
                                <i class="fas fa-search" style="font-size:24px;color:#ccc;display:block;margin-bottom:8px;"></i>
                                No categories found matching &ldquo;<strong>{{ $search }}</strong>&rdquo;.
                            @else
                                <i class="fas fa-tags" style="font-size:24px;color:#ccc;display:block;margin-bottom:8px;"></i>
                                No categories found.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($categories->hasPages())
    <nav aria-label="Page navigation" class="categories-pagination">
        <ul class="pagination-list">

            {{-- Previous --}}
            @if ($categories->onFirstPage())
                <li class="page-btn disabled"><span><i class="fa fa-angle-left"></i></span></li>
            @else
                <li class="page-btn">
                    <a href="{{ $categories->previousPageUrl() }}" class="page-link-loading" data-loading-text="Loading...">
                         <i class="fa fa-angle-left"></i>
                    </a>
                </li>
            @endif

            {{-- Page numbers --}}
            @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                @if ($page == $categories->currentPage())
                    <li class="page-btn active"><span>{{ $page }}</span></li>
                @else
                    <li class="page-btn">
                        <a href="{{ $url }}" class="page-link-loading" data-loading-text="Loading...">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($categories->hasMorePages())
                <li class="page-btn">
                    <a href="{{ $categories->nextPageUrl() }}" class="page-link-loading" data-loading-text="Loading...">
                         <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            @else
                <li class="page-btn disabled"><span><i class="fa fa-angle-right"></i></span></li>
            @endif

        </ul>
    </nav>
    @endif
</div>

{{-- Delete Modal --}}
<x-delete-modal />

{{-- Bulk Delete Confirm Modal --}}
<x-delete-multiple-modal
    id="bulkDeleteConfirmModal"
    title="Delete Selected Categories?"
>
    You are about to delete <strong><span id="bulkDeleteCountText">0</span> categor<span id="bulkDeleteCountWord">y</span></strong>. This action cannot be undone.
</x-delete-multiple-modal>

{{-- Hidden form used to submit bulk delete --}}
<form id="bulkDeleteForm" action="{{ route('categories.bulkDestroy') }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
    <div id="bulkDeleteIdsContainer"></div>
</form>

<script>
    // Clear search
    const clearBtn = document.getElementById('clearSearchBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            window.location.href = "{{ route('categories.index') }}";
        });
    }

    // ---- Bulk select / delete ----
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = () => Array.from(document.querySelectorAll('.row-checkbox'));
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCountEl = document.getElementById('selectedCount');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkClearBtn = document.getElementById('bulkClearBtn');
    const bulkDeleteModal = document.getElementById('bulkDeleteConfirmModal');
    const bulkDeleteCancelBtn = document.getElementById('bulkDeleteConfirmModalCancelBtn');
    const bulkDeleteCloseBtn = document.getElementById('bulkDeleteConfirmModalCloseBtn');
    const bulkDeleteConfirmBtn = document.getElementById('bulkDeleteConfirmModalConfirmBtn');
    const bulkDeleteCountText = document.getElementById('bulkDeleteCountText');
    const bulkDeleteCountWord = document.getElementById('bulkDeleteCountWord');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const bulkDeleteIdsContainer = document.getElementById('bulkDeleteIdsContainer');

    function getSelectedIds() {
        return rowCheckboxes().filter(cb => cb.checked).map(cb => cb.value);
    }

    function updateBulkUI() {
        const checked = rowCheckboxes().filter(cb => cb.checked);
        const count = checked.length;

        // toggle row highlight
        rowCheckboxes().forEach(cb => {
            const row = cb.closest('tr');
            if (row) row.classList.toggle('row-selected', cb.checked);
        });

        // bar visibility + count text
        bulkActionsBar.classList.toggle('active', count > 0);
        selectedCountEl.textContent = count + ' selected';
        bulkDeleteBtn.disabled = count === 0;

        // select-all checkbox state
        if (selectAllCheckbox) {
            const total = rowCheckboxes().length;
            selectAllCheckbox.checked = total > 0 && count === total;
            selectAllCheckbox.indeterminate = count > 0 && count < total;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes().forEach(cb => cb.checked = selectAllCheckbox.checked);
            updateBulkUI();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('row-checkbox')) {
            updateBulkUI();
        }
    });

    if (bulkClearBtn) {
        bulkClearBtn.addEventListener('click', function() {
            rowCheckboxes().forEach(cb => cb.checked = false);
            updateBulkUI();
        });
    }

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;
            bulkDeleteCountText.textContent = ids.length;
            bulkDeleteCountWord.textContent = ids.length === 1 ? 'y' : 'ies';
            bulkDeleteModal.classList.add('open');
        });
    }

    function closeBulkDeleteModal() {
        bulkDeleteModal.classList.remove('open');
    }

    if (bulkDeleteCancelBtn) {
        bulkDeleteCancelBtn.addEventListener('click', closeBulkDeleteModal);
    }

    if (bulkDeleteCloseBtn) {
        bulkDeleteCloseBtn.addEventListener('click', closeBulkDeleteModal);
    }

    // close modal on backdrop click
    if (bulkDeleteModal) {
        bulkDeleteModal.addEventListener('click', function(e) {
            if (e.target === bulkDeleteModal) closeBulkDeleteModal();
        });
    }

    // close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && bulkDeleteModal.classList.contains('open')) {
            closeBulkDeleteModal();
        }
    });

    if (bulkDeleteConfirmBtn) {
        bulkDeleteConfirmBtn.addEventListener('click', function() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            bulkDeleteIdsContainer.innerHTML = '';
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                bulkDeleteIdsContainer.appendChild(input);
            });

            bulkDeleteForm.submit();
        });
    }

    updateBulkUI();
</script>

@endsection
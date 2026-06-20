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

    /* Search field */
    .search-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .search-wrapper .search-icon {
        position: absolute;
        left: 10px;
        color: #aaa;
        pointer-events: none;
    }
    #customerSearch {
        margin-top: -5px;   
        padding: 10px 12px 10px 34px;
        border: 1px solid #ddd;
        border-radius: 24px;
        font-size: 14px;
        width: 400px;
        outline: none;
        transition: border-color 0.2s;
    }
    #customerSearch:focus {
        border-color: #132f4f;
        box-shadow: 0 0 0 3px rgba(74,144,226,0.15);
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

<div class="content-section" id="customers">
    <h2><i class="fas fa-users"></i> Customer Management</h2>

    <div class="filter-section">
        <div class="filter-controls">
            <a href="{{ route('customers.create') }}"
               class="btn btn-primary page-link-loading"
               data-loading-text="Loading add...">
                <i class="fas fa-circle-plus"></i> Add New Customer
            </a>

            {{-- 🔍 Search field --}}
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text"
                       id="customerSearch"
                       placeholder="Search by name, phone..."
                       autocomplete="off">
            </div>
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

                {{-- Shown by JS when search yields no matches --}}
                <tr id="noResultsRow">
                    <td colspan="6" style="text-align:center;">No customers match your search.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<x-delete-modal />
<script>

    // 🔍 Live search — filters by Name and Phone
    document.getElementById('customerSearch').addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        const rows  = document.querySelectorAll('#customersTable tr:not(#noResultsRow)');
        let visibleCount = 0;

        rows.forEach(function(row) {
            // Column indices: 1 = Name, 3 = Phone
            const name  = (row.cells[1]?.textContent || '').toLowerCase();
            const phone = (row.cells[3]?.textContent || '').toLowerCase();
            const match = name.includes(query) || phone.includes(query);
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        document.getElementById('noResultsRow').style.display =
            (visibleCount === 0 && query !== '') ? '' : 'none';
    });
</script>

@endsection
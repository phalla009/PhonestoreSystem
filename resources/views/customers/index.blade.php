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
    /* Status styles */
    .status-active {
        color: green;
        background-color: transparent !important;
    }
    .status-inactive {
        color: red;
        background-color: transparent !important;
    }

    /* Action buttons container */
    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* Table base styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }

    thead tr {
        background-color: #f7f7f7;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        vertical-align: middle;
        word-wrap: break-word;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0; top: 0;
        width: 100%; height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
        background-color: #fff;
        margin: 60px auto;
        padding: 20px;
        border-radius: 10px;
        width: 500px;
        max-width: 95vw;
        position: relative;
        box-sizing: border-box;
    }
    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        color: #333;
    }
    .close:hover {
        color: #666;
    }

</style>
@endsection

@section('content')
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
            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                <i class="fas fa-circle-plus"></i> Add New Customer
            </a>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
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
                        <td data-label="No">{{ $customer->id }}</td>
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
                                <a href="{{ route('customers.show', $customer->id) }}" class="action-btn show-btn">
                                    <i class="fas fa-eye"></i> Show
                                </a>
                                <a href="{{ route('customers.edit', $customer->id) }}" class="action-btn edit-btn">
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
    <div class="modal-content">
        <span class="close" id="deleteModalClose">&times;</span>
        <h3>
            <i class="fas fa-trash-alt" style="color: #e74c3c; margin-right: 10px;"></i>
            Confirm Delete
        </h3>
        <p>Are you sure you want to delete this customer?</p>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <button type="button" id="cancelDelete" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Delete modal open
        document.querySelectorAll('.openDeleteModal').forEach(button => {
            button.addEventListener('click', function () {
                const action = this.getAttribute('data-action');
                const deleteModal = document.getElementById('deleteConfirmModal');
                const deleteForm = document.getElementById('deleteForm');
                deleteForm.action = action;
                deleteModal.style.display = 'block';
            });
        });

        // Close modal handlers
        document.getElementById('deleteModalClose').addEventListener('click', () => {
            document.getElementById('deleteConfirmModal').style.display = 'none';
            document.getElementById('deleteForm').action = '';
        });
        document.getElementById('cancelDelete').addEventListener('click', () => {
            document.getElementById('deleteConfirmModal').style.display = 'none';
            document.getElementById('deleteForm').action = '';
        });

        window.addEventListener('click', function (e) {
            if (e.target === document.getElementById('deleteConfirmModal')) {
                document.getElementById('deleteConfirmModal').style.display = 'none';
                document.getElementById('deleteForm').action = '';
            }
        });
    });
</script>
@endsection

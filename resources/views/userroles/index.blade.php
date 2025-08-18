@extends('layouts.master')

@section('pageTitle')
    Roles Listing
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/delete_form.css') }}">
    <script src="{{ URL::asset('js/form.js') }}" defer></script>
    <script src="{{ URL::asset('js/delete_form.js') }}" defer></script>
@endsection

@section('content')
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
                    <i class="fas fa-plus"></i> Add New Role
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
                        <th>No</th>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="rolesTable">
                    @forelse ($roles as $role)
                        <tr>
                            <td data-label="No">{{ $role->id }}</td>
                            <td data-label="Role Name">{{ $role->role_name }}</td>
                             <td data-label="Role Name">{{ $role->description  ?? 'N/A' }}</td>
                            <td data-label="Created At">{{ $role->created_at ? $role->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <a href="{{ route('userroles.show', $role->id) }}" class="action-btn show-btn openShowModal">
                                        <i class="fas fa-eye"></i> Show
                                    </a>
                                    <a href="{{ route('userroles.edit', $role->id) }}" class="action-btn edit-btn openEditModal" data-id="{{ $role->id }}">
                                        <i class="fas fa-pen-to-square"></i> Edit
                                    </a>

                                    <button type="button" class="action-btn delete-btn openDeleteModal" data-action="{{ route('userroles.destroy', $role->id) }}">
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
        <div class="modal-content">
            <span class="close" id="deleteModalClose">&times;</span>
            <h3>
                <i class="fas fa-trash-alt" style="color: #e74c3c; margin-right: 10px;"></i>
                Confirm Delete
            </h3>
            <p>Are you sure you want to delete this record?</p>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="button" id="cancelDelete" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
            </form>
        </div>
    </div>
@endsection

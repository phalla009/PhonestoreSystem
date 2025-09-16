@extends('layouts.master')

@section('pageTitle')
    User Managers Listing
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

    <div class="content-section" id="users">
        <h2><i class="fas fa-user-shield"></i> User Managers Management</h2>

        <div class="filter-section">
            <div class="filter-controls" style="display:flex; align-items:center; gap: 10px;">
                <a href="{{ route('usermanagers.create') }}" class="btn btn-primary" id="openCreateModal">
                    <i class="fas fa-plus"></i> Add New User Manager
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
                            <td data-label="No">{{ $user->id }}</td>
                            <td data-label="Full Name"><i class="fas fa-user user-icon"></i>{{ $user->name }}</td>
                            <td data-label="Email">{{ $user->email }}</td>
                           <td data-label="Role">
                                @php
                                    $roleName = $user->role->role_name ?? 'N/A';
                                    $roleColor = 'gray'; // default color
                                    switch(strtolower($roleName)) {
                                        case 'admin':
                                            $roleColor = 'red';
                                            break;
                                        case 'manager':
                                            $roleColor = 'green';
                                            break;
                                        case 'staff':
                                            $roleColor = 'blue';
                                            break;
                                    }
                                @endphp
                                <span style="color: {{ $roleColor }}; font-weight: 600;">{{ $roleName }}</span>
                            </td>
                            <td data-label="Created At">{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <a href="{{ route('usermanagers.show', $user->id) }}" class="action-btn show-btn openShowModal">
                                        <i class="fas fa-eye"></i> Show
                                    </a>

                                    <a href="{{ route('usermanagers.edit', $user->id) }}" class="action-btn edit-btn openEditModal" data-id="{{ $user->id }}">
                                        <i class="fas fa-pen-to-square"></i> Edit
                                    </a>

                                    <button type="button" class="action-btn delete-btn openDeleteModal" data-action="{{ route('usermanagers.destroy', $user->id) }}">
                                        <i class="fas fa-trash"></i> Delete
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

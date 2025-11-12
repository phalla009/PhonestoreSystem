@extends('layouts.master')

@section('pageTitle')
    Categories Listing
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
            <i class="fas fa-check-circle" ></i>
            {{ session('success') }}
        </div>
    @endif
        <div class="content-section" id="categories">
            <h2><i class="fas fa-tags"></i> Categories Management</h2>
            <div class="filter-section">
                <div class="filter-controls" style="display:flex; align-items:center; gap: 10px;">
                    <a href="{{ route('categories.create') }}" class="btn btn-primary" id="openCreateModal">
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
                            <th>No</th>
                            <th>Brand Name</th>
                            {{-- <th>Description</th> --}}
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTable">
                        @forelse ($categories as $category)
                            <tr>
                                <td data-label="No">{{ $category->id }}</td>
                                <td data-label="Category Name">{{ $category->name }}</td>
                                {{-- <td data-label="Description">{{ $category->description ?? 'No description' }}</td> --}}
                                <td data-label="Created At">{{ $category->created_at ? $category->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td data-label="Actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('categories.show', $category->id) }}"
                                            class="action-btn show-btn openShowModal">
                                            <i class="fas fa-eye"></i> Show
                                        </a>

                                        <a href="{{ route('categories.edit', $category->id) }}"
                                            class="action-btn edit-btn openEditModal"
                                            data-id="{{ $category->id }}">
                                            <i class="fas fa-pen-to-square"></i> Edit
                                        </a>

                                        <button type="button" class="action-btn delete-btn openDeleteModal"
                                            data-action="{{ route('categories.destroy', $category->id) }}">
                                            <i class="fas fa-trash"></i> Delete
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

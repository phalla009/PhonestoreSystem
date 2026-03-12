@extends('layouts.master')

@section('pageTitle')
    Edit User Role
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
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
            <i class="fas fa-check-circle" style="color: green; margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="modal-content" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
        <a href="{{ route('userroles.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-user-shield"></i> Edit User Role</h2>

        <form id="editRoleForm" action="{{ route('userroles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="role_name">Role Name:</label>
                    <input id="role_name" type="text" name="role_name" value="{{ old('role_name', $role->role_name) }}" placeholder="Enter role name">
                    @error('role_name')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" style="width:100%;" placeholder="Enter role description">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="field">
                <fieldset style="border: 1px solid #7d7d7d; padding: 15px;">
                    <legend style="font-weight: bold; padding: 0 15px;">Select the permissions of your role</legend>
                    <div style="display: flex; flex-wrap: wrap; gap: 15px; flex-direction: row;">
                        @foreach ($allPermissions as $permissionGroupName => $permissionsOfAGroup)
                            <div style="background-color: white; color: black; padding: 10px; border: 1px solid #e7e7e7; flex: 1;">
                                <h4 style="font-size: 12px;">{{ $permissionGroupName }}</h4>
                                @foreach ($permissionsOfAGroup as $perm)
                                    <div style="display: flex; gap: 5px; min-width: 200px;">
                                        <input
                                            type="checkbox"
                                            value="{{ $perm->id }}"
                                            name="permissions[]"
                                            id="permissions{{ $perm->id }}"
                                            @if(in_array($perm->id, $rolePermissions)) checked @endif
                                        >
                                        <label style="color: #535353; font-size: 12px;" for="permissions{{ $perm->id }}">
                                            {{ $perm->permission_name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            </div>

            <div style="text-align: right; margin-top: 1rem;">
                <button type="submit" class="btn btn-update">
                    <i class="fas fa-save"></i> Update Role
                </button>
                
            </div>
        </form>
    </div>

    <script>
        const overlay     = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        // ✅ Hide overlay once page fully loads
        window.addEventListener('load', function () {
            overlay.style.display = 'none';
        });

        // Back button → show loading then navigate
        document.getElementById('backBtn').addEventListener('click', function (e) {
            e.preventDefault();
            loadingText.textContent = 'Going back...';
            overlay.style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });

        // Submit form → show loading
        document.getElementById('editRoleForm').addEventListener('submit', function () {
            loadingText.textContent = 'Saving...';
            overlay.style.display = 'flex';
        });

        // Cancel → reset form
        document.getElementById('cancel').addEventListener('click', function () {
            document.getElementById('editRoleForm').reset();
        });
    </script>

@endsection
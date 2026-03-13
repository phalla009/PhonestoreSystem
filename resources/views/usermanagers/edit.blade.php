@extends('layouts.master')

@section('pageTitle', 'Edit User Manager')

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
        .text-danger { color: red; margin-top: 4px; }

        /* Permission Display */
        .perm-display-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            min-height: 80px;
        }

        .perm-display-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 12.5px;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        .perm-display-item input[type="checkbox"] {
            width: 13px;
            height: 13px;
            accent-color: #6366f1;
            flex-shrink: 0;
        }
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
        <a href="{{ route('usermanagers.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-user-shield"></i> Edit User Manager</h2>

        <form id="editUserManagerForm" action="{{ route('usermanagers.update', $usermanager->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Row 1: Full Name & Email --}}
            <div class="form-row" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $usermanager->name) }}" placeholder="Enter full name">
                    @error('name') <p class="text-danger">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $usermanager->email) }}" placeholder="Enter email">
                    @error('email') <p class="text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Row 2: Password & Confirm Password --}}
            <div class="form-row" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 12px;">
                <div class="form-group">
                    <label for="password">Password: <small>(leave blank to keep current)</small></label>
                    <input id="password" type="password" name="password" placeholder="Enter new password">
                    @error('password') <p class="text-danger">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password:</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm password">
                    @error('password_confirmation') <p class="text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Row 3: Role & Description --}}
            <div class="form-row" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 12px;">
                <div class="form-group">
                    <label for="role_id">Assign Role:</label>
                    <select id="role_id" name="role_id">
                        <option value="">Select role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $usermanager->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->role_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="text-danger">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"
                        placeholder="Enter a brief description about this user manager..."
                        rows="3"
                        style="width: 100%; resize: none;">{{ old('description', $usermanager->description) }}</textarea>
                    @error('description') <p class="text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
             {{-- <div class="form-group">
                    <label>Permissions for Selected Role:</label>
                    <div id="permissionsContainer" class="perm-display-grid">
                        <span style="color: #9ca3af; font-size: 12.5px; grid-column: span 2;">Select a role to view permissions...</span>
                    </div>
                </div> --}}

            {{-- Buttons --}}
            <div style="text-align: right; margin-top: 1rem; display: flex; justify-content: flex-end; gap: 8px;">
                <button type="submit" class="btn btn-update">
                    <i class="fas fa-save"></i> Update User Manager
                </button>
               
            </div>

        </form>
    </div>

    <script>
        const overlay     = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        window.addEventListener('load', function () {
            overlay.style.display = 'none';
        });

        document.getElementById('backBtn').addEventListener('click', function (e) {
            e.preventDefault();
            loadingText.textContent = 'Going back...';
            overlay.style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });

        document.getElementById('editUserManagerForm').addEventListener('submit', function () {
            loadingText.textContent = 'Saving...';
            overlay.style.display = 'flex';
        });

        document.getElementById('cancel').addEventListener('click', function () {
            document.getElementById('editUserManagerForm').reset();
            if (roleSelect.value) {
                roleSelect.dispatchEvent(new Event('change'));
            } else {
                permissionsContainer.innerHTML =
                    '<span style="color:#9ca3af;font-size:12.5px;grid-column:span 2;">Select a role to view permissions...</span>';
            }
        });

        const roles = @json($roles);
        const roleSelect = document.getElementById('role_id');
        const permissionsContainer = document.getElementById('permissionsContainer');

        roleSelect.addEventListener('change', function () {
            const roleId = this.value;
            permissionsContainer.innerHTML = '';
            const role = roles.find(r => r.id == roleId);

            if (role && role.permissions && role.permissions.length > 0) {
                role.permissions.forEach(p => {
                    const div = document.createElement('div');
                    div.className = 'perm-display-item';
                    div.innerHTML = `
                        <input type="checkbox" checked disabled id="perm_${p.id}">
                        <label for="perm_${p.id}" style="font-size:12.5px; color:#4b5563; cursor:default;">${p.permission_name}</label>
                    `;
                    permissionsContainer.appendChild(div);
                });
            } else {
                permissionsContainer.innerHTML =
                    '<span style="color:#9ca3af;font-size:12.5px;grid-column:span 2;">No permissions found for this role.</span>';
            }
        });

        if (roleSelect.value) {
            roleSelect.dispatchEvent(new Event('change'));
        }
    </script>
@endsection
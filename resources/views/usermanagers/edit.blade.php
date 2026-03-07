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

            <!-- Full Name -->
            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="name">Full Name:</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $usermanager->name) }}" placeholder="Enter full name">
                    @error('name') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Email -->
            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="email">Email:</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $usermanager->email) }}" placeholder="Enter email">
                    @error('email') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Password -->
            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="password">Password: <small>(leave blank to keep current)</small></label>
                    <input id="password" type="password" name="password" placeholder="Enter new password">
                    @error('password') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Password Confirmation -->
            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="password_confirmation">Confirm Password:</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm password">
                    @error('password_confirmation') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Role Selection -->
            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label for="role_id">Assign Role:</label>
                    <select id="role_id" name="role_id">
                        <option value="">Select role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $usermanager->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->role_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Permissions Display -->
            <div class="form-row">
                <div class="form-group" style="width: 100%;">
                    <label>Permissions for Selected Role:</label>
                    <div id="permissionsContainer" style="border:1px solid #ccc; padding:10px; height:150px; overflow-y:auto;">
                        <!-- dynamically filled -->
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div style="text-align:right; margin-top:1rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update User Manager
                </button>
            </div>

            <!-- Cancel -->
            <div style="text-align:right; margin-top:1rem;">
                <button type="button" id="cancel" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Cancel
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
        document.getElementById('editUserManagerForm').addEventListener('submit', function () {
            loadingText.textContent = 'Saving...';
            overlay.style.display = 'flex';
        });

        // Cancel → reset form and clear permissions
        document.getElementById('cancel').addEventListener('click', function () {
            document.getElementById('editUserManagerForm').reset();
            document.getElementById('permissionsContainer').innerHTML = '';
            // Re-populate permissions for the current role after reset
            if (roleSelect.value) {
                roleSelect.dispatchEvent(new Event('change'));
            }
        });

        // Roles with Permissions
        const roles = @json($roles);
        const roleSelect = document.getElementById('role_id');
        const permissionsContainer = document.getElementById('permissionsContainer');

        roleSelect.addEventListener('change', function () {
            const roleId = this.value;
            permissionsContainer.innerHTML = '';
            const role = roles.find(r => r.id == roleId);

            if (role && role.permissions) {
                role.permissions.forEach(p => {
                    const div = document.createElement('div');
                    div.innerHTML = `
                        <input type="checkbox" checked disabled id="perm_${p.id}">
                        <label for="perm_${p.id}">${p.permission_name}</label>
                    `;
                    permissionsContainer.appendChild(div);
                });
            }
        });

        // Initialize display for current role
        if (roleSelect.value) {
            roleSelect.dispatchEvent(new Event('change'));
        }
    </script>
@endsection
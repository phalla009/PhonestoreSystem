@extends('layouts.master')

@section('pageTitle', 'Edit User Manager')

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
@endsection

@section('content')
@if(session('success'))
    <div id="successMessage" class="custom-success">
        <i class="fas fa-check-circle" style="color: green; margin-right: 8px;"></i>
        {{ session('success') }}
    </div>
@endif

<div class="modal-content" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
    <a href="{{ route('usermanagers.index') }}" class="btn btn-back">
        <i class="fas fa-chevron-left"></i> Back
    </a>

    <h2><i class="fas fa-user-shield"></i> Edit User Manager</h2>

    <form action="{{ route('usermanagers.update', $usermanager->id) }}" method="POST">
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
// Cancel button resets form
document.getElementById('cancel').addEventListener('click', function() {
    this.closest('form').reset();
    document.getElementById('permissionsContainer').innerHTML = '';
});

// Roles with Permissions
const roles = @json($roles);
const roleSelect = document.getElementById('role_id');
const permissionsContainer = document.getElementById('permissionsContainer');

roleSelect.addEventListener('change', function() {
    const roleId = this.value;
    permissionsContainer.innerHTML = '';
    const role = roles.find(r => r.id == roleId);

    if(role && role.permissions){
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

// Initialize display if old role selected
if(roleSelect.value){
    roleSelect.dispatchEvent(new Event('change'));
}
</script>
@endsection

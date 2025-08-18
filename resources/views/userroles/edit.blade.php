@extends('layouts.master')

@section('pageTitle')
    Edit User Role
@endsection

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
    <a href="{{ route('userroles.index') }}" class="btn btn-back">
        <i class="fas fa-chevron-left"></i> Back
    </a>

    <h2><i class="fas fa-user-shield"></i> Edit User Role</h2>

    <form action="{{ route('userroles.update', $role->id) }}" method="POST">
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
                <legend style="font-weight: bold; padding: 0 15px;"> Select the permissions of your role</legend>
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

            <div>
                    <button class="btn btn-update" type="submit">
                        <i class="fas fa-save"></i> Update Brand
                    </button>
                </div>
    </form>
</div>
@endsection

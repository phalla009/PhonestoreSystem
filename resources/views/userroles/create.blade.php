@extends('layouts.master')

@section('pageTitle')
    Add New User Role
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .permissions-grid {
            display: grid;
            grid-template-rows: repeat(2, auto);
            grid-auto-flow: column;
            gap: 12px;
            margin-top: 8px;
            margin-bottom: 20px;
        }
        .permission-item {
            display: flex;
            align-items: center;
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .permission-item:hover {
            background-color: #f0f0f0;
        }
        .permission-item input[type="checkbox"] {
            margin-right: 8px;
            accent-color: #28a745;
        }
        .text-danger { color: red; margin-top: 4px; }
    </style>
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

    <h2><i class="fas fa-user-shield"></i> Add New User Role</h2>

    <form action="{{ route('userroles.store') }}" method="POST">
        @csrf

        <div class="form-row">
            <div class="form-group" style="width: 100%;">
                <label for="role_name">Role Name:</label>
                <input id="role_name" type="text" name="role_name" value="{{ old('role_name') }}" placeholder="Enter role name">
                @error('role_name')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="width: 100%;">
                <label for="description">Description:</label>
                <textarea id="description" name="description" style="width:100%;" placeholder="Enter role description">{{ old('description') }}</textarea>
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
            <button id="submitRole" type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Add Role
            </button>
            <button id="cancel" type="button" class="btn btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('cancel').addEventListener('click', function () {
        this.closest('form').reset();
    });
</script>
@endsection

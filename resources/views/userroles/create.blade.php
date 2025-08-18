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

      <div class="field" style="margin-top: 20px;">
            <fieldset style="border: none; padding: 0;">
                <legend style="font-weight: 700; font-size: 16px; color: #1f2937; margin-bottom: 15px;">
                    Select the permissions of your role
                </legend>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                    @foreach ($allPermissions as $permissionGroupName => $permissionsOfAGroup)
                        <div style="background-color: #f3f4f6; padding: 20px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); transition: transform 0.2s; cursor: pointer;">
                            <h4 style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 12px;">{{ $permissionGroupName }}</h4>
                            @foreach ($permissionsOfAGroup as $perm)
                                <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 13px; color: #374151; cursor: pointer; transition: color 0.2s;">
                                    <input type="checkbox" value="{{ $perm->id }}" name="permissions[]" id="permissions{{ $perm->id }}" style="width: 16px; height: 16px; accent-color: #3b82f6;">
                                    {{ $perm->permission_name }}
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </fieldset>
        </div>

        <style>
        .field div:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        </style>


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

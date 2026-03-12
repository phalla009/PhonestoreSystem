@extends('layouts.master')

@section('pageTitle')
    Add New User Role
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
        .text-danger { color: red; margin-top: 4px; }

        /* Permission Section */
        .perm-outer-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 16px;
        }

        .perm-group {
            padding: 18px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .perm-group:hover {
            border-color: #a5b4fc;
            box-shadow: 0 4px 12px rgba(99,102,241,0.08);
        }

        .perm-group-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e5e7eb;
        }

        .perm-group-dot {
            width: 7px;
            height: 7px;
            background: #6366f1;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .perm-group-title {
            font-size: 12px;
            font-weight: 700;
            color: #4338ca;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .perm-inner-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }

        .perm-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 7px;
            font-size: 12.5px;
            color: #4b5563;
            cursor: pointer;
            border: 1px solid #e5e7eb;
            transition: all 0.18s;
            user-select: none;
        }

        .perm-label:hover {
            border-color: #a5b4fc;
            color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(99,102,241,0.1);
        }

        .perm-label input[type="checkbox"] {
            width: 14px;
            height: 14px;
            accent-color: #6366f1;
            flex-shrink: 0;
            cursor: pointer;
        }

        .perm-label:has(input:checked) {
            border-color: #818cf8;
            color: #4338ca;
            background: #eef2ff;
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
        <a href="{{ route('userroles.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-user-shield"></i> Add New User Role</h2>

        <form id="createRoleForm" action="{{ route('userroles.store') }}" method="POST">
            @csrf

           <div class="form-row" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                <div class="form-group">
                    <label for="role_name">Role Name:</label>
                    <input id="role_name" type="text" name="role_name" value="{{ old('role_name') }}" placeholder="Enter role name">
                    @error('role_name')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
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

                    <div class="perm-outer-grid">
                        @foreach ($allPermissions as $permissionGroupName => $permissionsOfAGroup)
                            <div class="perm-group">

                                <div class="perm-group-header">
                                    <div class="perm-group-dot"></div>
                                    <h4 class="perm-group-title">{{ $permissionGroupName }}</h4>
                                </div>

                                <div class="perm-inner-grid">
                                    @foreach ($permissionsOfAGroup as $perm)
                                        <label class="perm-label">
                                            <input
                                                type="checkbox"
                                                value="{{ $perm->id }}"
                                                name="permissions[]"
                                                id="permissions{{ $perm->id }}">
                                            <span>{{ $perm->permission_name }}</span>
                                        </label>
                                    @endforeach
                                </div>

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

        document.getElementById('createRoleForm').addEventListener('submit', function () {
            loadingText.textContent = 'Saving...';
            overlay.style.display = 'flex';
        });

        document.getElementById('cancel').addEventListener('click', function () {
            document.getElementById('createRoleForm').reset();
        });
    </script>

@endsection
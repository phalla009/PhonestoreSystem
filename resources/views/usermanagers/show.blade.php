@extends('layouts.master')

@section('pageTitle')
    Show User Manager
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <style>
        .user-details {
            max-width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 30px 40px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
        }

        .user-details h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #34495e;
            font-weight: 700;
            font-size: 2rem;
        }

        .row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .row p {
            flex: 1 1 48%;
            margin: 10px 0;
            font-size: 1.1rem;
        }

        .row p strong {
            color: #2980b9;
            margin-right: 5px;
        }

        #permissionsContainer div {
            margin-bottom: 5px;
        }

        @media (max-width: 768px) {
            .row p {
                flex: 1 1 100%;
            }
        }
    </style>
@endsection

@section('content')
<div class="user-details">
    <a href="{{ route('usermanagers.index') }}" class="btn btn-back" style="margin-bottom: 20px;">
        <i class="fas fa-chevron-left"></i> Back
    </a>

    <h2><i class="fas fa-user-shield"></i> User Manager Details</h2>

    <div class="row">
        <p><strong>Full Name:</strong> {{ $usermanager->name }}</p>
        <p><strong>Email:</strong> {{ $usermanager->email }}</p>
    </div>

    <div class="row">
        <p><strong>Role:</strong> {{ $usermanager->role->role_name ?? 'N/A' }}</p>
        <p><strong>Created At:</strong> {{ $usermanager->created_at ? $usermanager->created_at->format('Y-m-d H:i') : 'N/A' }}</p>
    </div>

    <div class="row">
        <p><strong>Last Updated:</strong> {{ $usermanager->updated_at ? $usermanager->updated_at->format('Y-m-d H:i') : 'N/A' }}</p>
        <p><strong>Permissions:</strong></p>
    </div>

    @php
        $permissions = $usermanager->role->permissions ?? collect();
        $rows = 5; // fixed number of rows
        $totalPermissions = $permissions->count();
        $columns = ceil($totalPermissions / $rows); // auto-calculate columns
        $perColumn = ceil($totalPermissions / $columns); // items per column
    @endphp

    <div id="permissionsContainer" style="border:1px solid #ccc; padding:10px; display:grid; grid-template-columns: repeat({{ $columns }}, 1fr); gap:10px; max-height:200px; overflow-y:auto;">
        @for($i = 0; $i < $columns; $i++)
            <div>
                @foreach($permissions->slice($i * $perColumn, $perColumn) as $permission)
                    <div style="display:flex; align-items:center; margin-bottom:5px;">
                        <i class="fas fa-check-circle" style="color:green; margin-right:5px;"></i>
                        {{ $permission->permission_name }}
                    </div>
                @endforeach
            </div>
        @endfor
    </div>


</div>
@endsection

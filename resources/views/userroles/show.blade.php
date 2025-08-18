@extends('layouts.master')

@section('pageTitle')
   Show User Role
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .role-details {
            max-width: 100%;
            min-height: 500px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 30px 40px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
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

        .btn-back i {
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .row p {
                flex: 1 1 100%;
            }
        }

    </style>
@endsection

@section('content')
<div class="role-details">

    <a href="{{ route('userroles.index') }}" class="btn btn-back">
        <i class="fas fa-chevron-left"></i> Back
    </a>
    <h2><i class="fas fa-user-shield"></i> Role Details</h2>

    <div class="row">
        <p><strong>Role Name:</strong> {{ $role->role_name }}</p>
         <p><strong>Permissions:</strong> 
                @foreach($role->permissions as $permission)
                    {{ $permission->permission_name }}{{ !$loop->last ? ',' : '' }}
                @endforeach
        </p>
    </div>
    <div class="row">
        <p><strong>Created At:</strong> {{ $role->created_at ? $role->created_at->format('Y-m-d H:i') : 'N/A' }}</p>
        <p><strong>Last Updated:</strong> {{ $role->updated_at ? $role->updated_at->format('Y-m-d H:i') : 'N/A' }}</p>
    </div>
</div>
    
@endsection

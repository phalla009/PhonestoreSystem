@extends('layouts.master')

@section('pageTitle')
    Show User Role
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

        .btn-back i { margin-right: 5px; }

        @media (max-width: 768px) {
            .row p { flex: 1 1 100%; }
        }
    </style>
@endsection

@section('content')

    {{-- Loading Overlay --}}
    <div id="loading-overlay">
        <div class="spinner"></div>
        <div id="loading-text">Loading...</div>
    </div>

    <div class="role-details">
        <a href="{{ route('userroles.index') }}" id="backBtn" class="btn btn-back">
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
    </script>

@endsection
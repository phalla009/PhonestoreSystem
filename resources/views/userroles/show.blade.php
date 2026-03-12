@extends('layouts.master')

@section('pageTitle')
     UserRole Details
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

        .info-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .info-box {
            padding: 14px 18px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .info-box:hover {
            border-color: #a5b4fc;
            box-shadow: 0 4px 12px rgba(99,102,241,0.08);
        }

        .info-label {
            font-size: 11px;
            font-weight: 700;
            color: #4338ca;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-label i {
            font-size: 11px;
            color: #6366f1;
        }

        .info-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }

        .perm-tag-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            margin-top: 4px;
        }

        .perm-tag {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 6px;
            border: 1px solid #e0e7ff;
            font-size: 12.5px;
            color: #4338ca;
            background: #eef2ff;
        }

        .perm-tag i {
            font-size: 10px;
            color: #6366f1;
        }
    </style>
@endsection

@section('content')

    {{-- Loading Overlay --}}
    <div id="loading-overlay">
        <div class="spinner"></div>
        <div id="loading-text">Loading...</div>
    </div>

    <div class="modal-content" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
        <a href="{{ route('userroles.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-user-shield"></i> Role Details</h2>

        {{-- Row 1: Role Name & Dates --}}
        <div class="info-row">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-tag"></i> Role Name</div>
                <div class="info-value">{{ $role->role_name }}</div>
            </div>

            <div class="info-box">
                <div class="info-label"><i class="fas fa-align-left"></i> Description</div>
                <div class="info-value">{{ $role->description ?? 'N/A' }}</div>
            </div>
        </div>

        {{-- Row 2: Created & Updated --}}
        <div class="info-row">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-calendar-plus"></i> Created At</div>
                <div class="info-value">{{ $role->created_at ? $role->created_at->format('Y-m-d H:i') : 'N/A' }}</div>
            </div>

            <div class="info-box">
                <div class="info-label"><i class="fas fa-clock"></i> Last Updated</div>
                <div class="info-value">{{ $role->updated_at ? $role->updated_at->format('Y-m-d H:i') : 'N/A' }}</div>
            </div>
        </div>

        {{-- Permissions --}}
        <div class="info-box" style="margin-bottom: 16px;">
            <div class="info-label"><i class="fas fa-shield-alt"></i> Permissions</div>
            <div class="perm-tag-grid">
                @forelse($role->permissions as $permission)
                    <div class="perm-tag">
                        <i class="fas fa-check-circle"></i>
                        {{ $permission->permission_name }}
                    </div>
                @empty
                    <span style="font-size: 13px; color: #9ca3af;">No permissions assigned.</span>
                @endforelse
            </div>
        </div>

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
    </script>

@endsection
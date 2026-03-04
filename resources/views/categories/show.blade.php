@extends('layouts.master')

@section('pageTitle')
   Show Categories
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .category-details {
            max-width: 100%;
            height: 750px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 30px 40px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
        }
        .category-details h2 {
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
        @media (max-width: 768px) {
            .row p { flex: 1 1 100%; }
        }

        /* Loading Overlay */
        #loading-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.85);
            display: none;
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
        <div id="loading-text">Going back...</div>
    </div>

    <div class="category-details">
        <a href="{{ route('categories.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>

        <h2><i class="fas fa-mobile-alt"></i> Brand Details</h2>

        <div class="row">
            <p><strong>Brand Name:</strong> {{ $category->name }}</p>
            <p><strong>Description:</strong> {{ $category->description ?? 'No description available.' }}</p>
        </div>

        <div class="row">
            <p><strong>Created At:</strong> {{ $category->created_at ? $category->created_at->format('Y-m-d H:i') : 'N/A' }}</p>
            <p><strong>Last Updated:</strong> {{ $category->updated_at ? $category->updated_at->format('Y-m-d H:i') : 'N/A' }}</p>
        </div>
    </div>

    <script>
        // Back button → show loading then navigate
        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loading-overlay').style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });
    </script>

@endsection
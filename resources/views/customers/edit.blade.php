@extends('layouts.master')

@section('pageTitle')
   Edited Customers
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js')}}"></script>
    <style>
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
        <div id="loading-text">Loading...</div>
    </div>

    <div class="modal-content">
        <a href="{{ route('customers.index') }}" id="backBtn" class="btn btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h2><i class="fas fa-users"></i> Edit Customer</h2>

        <form id="customerForm" action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Customer Name:</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}">
                @error('name')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Gender:</label>
                <select name="gender">
                    <option value="">Select Gender</option>
                    <option value="male"   {{ (old('gender', $customer->gender) == 'male')   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ (old('gender', $customer->gender) == 'female') ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}">
                @error('phone')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Status:</label>
                <input type="text" value="{{ $customer->status }}" readonly class="readonly">
            </div>

            <div style="text-align: right; margin-top: 1rem;">
                <button class="btn btn-update" type="submit">
                    <i class="fas fa-save"></i> Update Customer
                </button>
            </div>
        </form>
    </div>

    <script>
        const overlay     = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        // Back button → show loading then navigate
        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            loadingText.textContent = 'Going back...';
            overlay.style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });

        // Submit form → show loading
        document.getElementById('customerForm').addEventListener('submit', function() {
            loadingText.textContent = 'Updating...';
            overlay.style.display = 'flex';
        });
    </script>

@endsection
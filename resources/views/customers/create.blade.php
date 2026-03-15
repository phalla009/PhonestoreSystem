@extends('layouts.master')

@section('pageTitle')
    Add New Customers
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 1.5rem;
        }
        .form-grid .form-group-full {
            grid-column: 1 / -1;
        }
        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
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
            <div class="success-content">
                <span class="success-icon">✔</span>
                <span class="success-text">{{ session('success') }}</span>
            </div>
            <div class="progress-bar"></div>
        </div>
    @endif

    <div class="modal-content">
        <a href="{{ route('customers.index') }}" id="backBtn" class="btn btn-back" aria-label="Back to customers list">
            <i class="fas fa-chevron-left"></i> Back
        </a>
        <h2><i class="fas fa-users"></i> Add New Customer</h2>

        <form id="customerForm" action="{{ route('customers.store') }}" method="POST">
            @csrf

            <div class="form-grid">

                {{-- Customer Name --}}
                <div class="form-group">
                    <label for="name">Customer Name:</label>
                    <input id="name" type="text" name="name" placeholder="Enter customer name" value="{{ old('name') }}">
                    @error('name')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gender --}}
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input id="phone" type="text" name="phone" placeholder="Enter phone number" value="{{ old('phone') }}">
                    @error('phone')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email (full width) --}}
                <div class="form-group form-group-full">
                    <label for="email">Email:</label>
                    <input id="email" type="email" name="email" placeholder="Enter email address" value="{{ old('email') }}">
                    @error('email')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="password">Password: <small style="color:#888;"></small></label>
                    <input id="password" type="password" name="password" placeholder="Enter password" autocomplete="new-password">
                    @error('password')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password:</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm password" autocomplete="new-password">
                </div>

            </div>

            <div style="text-align: right; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Add Customer
                </button>
                <button id="cancelBtn" type="button" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>

    <script>
        const overlay     = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            loadingText.textContent = 'Going back...';
            overlay.style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });

        document.getElementById('customerForm').addEventListener('submit', function() {
            loadingText.textContent = 'Saving...';
            overlay.style.display = 'flex';
        });

        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('customerForm').reset();
        });
    </script>

@endsection
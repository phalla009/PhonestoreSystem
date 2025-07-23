@extends('layouts.master')

@section('pageTitle', 'Add Customer')

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
@endsection

@section('content')
    @if(session('success'))
        <div id="successMessage" class="custom-success">
            <i class="fas fa-check-circle" style="color: green; margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="modal-content">
        <a href="{{ route('customers.index') }}" class="btn btn-back" aria-label="Back to product list">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2><i class="fas fa-users"></i> Add New Customer</h2>

        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
                <div class="form-group">
                    <label for="name">Customer Name:</label>
                    <input id="name" type="text" name="name" placeholder="Enter customer name" value="{{ old('name') }}">
                    @error('name')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input id="phone" type="text" name="phone" placeholder="Enter phone number" value="{{ old('phone') }}">
                    @error('phone')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" >
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            <div style="text-align: right; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Add Customer
                </button>
                <button id="cancelCategory" type="button" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('cancelCategory').addEventListener('click', function () {
            this.closest('form').reset();
        });
    </script>
@endsection

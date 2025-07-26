@extends('layouts.master')

@section('pageTitle')
   Edited Customers
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js')}}"></script>
@endsection
@section('content')
<div class="modal-content">
    <a href="{{ route('customers.index') }}" class="btn btn-back">
        <i class="fas fa-chevron-left"></i> Back
    </a>
    <h2><i class="fas fa-users"></i> Edit Customer</h2>
    <form action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
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
                    <option value="male" {{ (old('gender', $customer->gender) == 'male') ? 'selected' : '' }}>Male</option>
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
@endsection

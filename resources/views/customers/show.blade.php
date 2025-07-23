@extends('layouts.master')

@section('pageTitle')
    Show Customer
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        .details-card {
            max-width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 30px 40px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
        }

        .details-card h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #34495e;
            font-weight: 700;
            font-size: 2rem;
        }

        .details-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .details-group {
            flex: 1 1 45%;
            margin-bottom: 20px;
        }

        .details-group label {
            font-weight: bold;
            color: #2980b9;
            display: block;
            margin-bottom: 5px;
        }

        .details-group div {
            font-size: 1.1rem;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: capitalize;
            background-color: #f1f1f1;
            color: #2c3e50;
        }

        .status-active {
            background-color: #27ae60;
            color: #fff;
        }

        .status-inactive {
            background-color: #c0392b;
            color: #fff;
        }
        @media (max-width: 600px) {
            .details-group {
                flex: 1 1 100%;
            }
        }
    </style>
@endsection

@section('content')
<div class="details-card" role="main" aria-label="Customer Details">
    <a href="{{ route('customers.index') }}" class="btn btn-back">
        <i class="fas fa-chevron-left"></i> Back
    </a>
    <h2><i class="fas fa-user"></i> Customer Details</h2>

    <div class="details-row">
        <div class="details-group">
            <label>Customer Name:</label>
            <div>{{ $customer->name }}</div>
        </div>
        <div class="details-group">
            <label>Gender:</label>
            <div>{{ ucfirst($customer->gender) }}</div>
        </div>
    </div>

    <div class="details-row">
        <div class="details-group">
            <label>Phone:</label>
            <div>{{ $customer->phone }}</div>
        </div>
        <div class="details-group">
            <label>Status:</label>
            <div>
                <span class="status-badge status-{{ strtolower($customer->status) }}">
                    {{ ucfirst($customer->status) }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.master')

@section('pageTitle')
   Generated Report
@endsection

@section('content')
<div class="content-section">
    <h2>{{ ucfirst($type) }} Report</h2>
    <p>From {{ $start->format('M d, Y') }} to {{ $end->format('M d, Y') }}</p>

    @if($type === 'sales')
        <table class="table">
            <thead><tr><th>Date</th><th>Total ($)</th></tr></thead>
            <tbody>
                @foreach($results as $row)
                    <tr>
                        <td>{{ $row->date }}</td>
                        <td>{{ number_format($row->total ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($type === 'financial')
        <h4>Financial Report ({{ $start->format('M d, Y') }} - {{ $end->format('M d, Y') }})</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Sold Quantity</th>
                    <th>Revenue ($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                        <td>{{ $row->sold_qty ?? 0 }}</td>
                        <td>${{ number_format($row->revenue ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($type === 'inventory')
        <table class="table">
            <thead><tr><th>Product</th><th>Quantity</th><th>Price ($)</th></tr></thead>
            <tbody>
                @foreach($results as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->quantity ?? $row->stock ?? 'N/A' }}</td>
                        <td>{{ number_format($row->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($type === 'customer')
        <table class="table">
            <thead><tr><th>Name</th><th>Gender</th><th>Joined</th></tr></thead>
            <tbody>
                @foreach($results as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->gender }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->created_at)->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

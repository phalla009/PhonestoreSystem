@extends('layouts.master')

@section('pageTitle')
    Report
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
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price ($)</th>
                    <th>Total ($)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $grandTotalUSD = 0;
                    $totalQty = 0;
                    $rate = 4100; // 1 USD = 4100 KHR
                @endphp

                @foreach($results as $row)
                    @php
                        $qty = $row->quantity ?? $row->stock ?? 0;
                        $total = $qty * $row->price;

                        $grandTotalUSD += $total;
                        $totalQty += $qty;
                    @endphp
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{{ $qty }}</td>
                        <td>${{ number_format($row->price, 2) }}</td>
                        <td>${{ number_format($total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th>Total Qty:</th>
                    <th>{{ $totalQty }}</th>
                    <th style="text-align:right;">Grand Total (USD):</th>
                    <th>${{ number_format($grandTotalUSD, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="3" style="text-align:right;">Grand Total (KHR):</th>
                    <th>៛{{ number_format($grandTotalUSD * $rate) }}</th>
                </tr>
            </tfoot>
        </table>
        @elseif($type === 'customer')
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>
                            <th class="text-center">Total Qty</th>
                            <th class="text-right">Total Price</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td>{{ ucfirst($row->gender) }}</td>
                                <td class="text-center">
                                    {{ number_format($row->total_qty ?? 0) }}
                                </td>
                                <td class="text-right">
                                    ${{ number_format($row->total_price ?? 0, 2) }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($row->created_at)->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #e3f2fd; color: #0d47a1; font-weight: bold;">
                            <td colspan="2" class="text-right">Grand Total:</td>
                            <td class="text-center">{{ number_format($results->sum('total_qty')) }}</td>
                            <td class="text-right">${{ number_format($results->sum('total_price'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
@endsection

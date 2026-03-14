{{-- resources/views/orders/invoice_combined.blade.php --}}
{{-- Receives: $orders (Eloquent Collection with customer & product loaded) --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            width: 80mm;
            margin: 0 auto;
            padding: 6mm 4mm;
        }

        .center { text-align: center; }
        .right   { text-align: right; }
        .left    { text-align: left; }
        .bold    { font-weight: bold; }

        .shop-name {
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .sub-title {
            font-size: 10px;
            text-align: center;
            color: #444;
            margin-bottom: 4px;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }

        thead th {
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px dashed #000;
            padding: 3px 2px;
            text-align: left;
        }

        tbody td {
            font-size: 10px;
            padding: 3px 2px;
            vertical-align: top;
        }

        tbody tr + tr td {
            border-top: 1px dotted #ccc;
        }

        .product-name {
            max-width: 28mm;
            word-break: break-word;
        }

        .total-section {
            margin-top: 4px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
        }

        .grand-total {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin-top: 3px;
        }

        .badge {
            font-size: 9px;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 3px;
        }
        .badge-completed { background: #d4edda; color: #155724; }
        .badge-pending   { background: #fff3cd; color: #856404; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }

        .notes-section {
            margin-top: 6px;
            font-size: 10px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #555;
            margin-top: 8px;
            line-height: 1.6;
        }

        @media print {
            body { padding: 4mm; }
        }
    </style>
</head>
<body>

    {{-- SHOP HEADER --}}
    <div class="shop-name">ORDER RECEIPT</div>
    <div class="sub-title">Combined Invoice</div>

    <hr class="divider">

    {{-- META INFO --}}
   <div class="info-row">
        <span>Date:</span>
        <span>{{ now()->timezone('Asia/Phnom_Penh')->format('d/m/Y H:i') }}</span>
    </div>
    <div class="info-row">
        <span>Total Orders:</span>
        <span>{{ $orders->count() }}</span>
    </div>

    <hr class="divider">

    {{-- ORDERS TABLE --}}
    <table>
        <thead>
            <tr>
                <th class="left">Item</th>
                <th class="center">Qty</th>
                <th class="right">Price</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td class="product-name">
                    <span class="bold">{{ $order->order_number }}</span><br>
                    {{ $order->product->name ?? 'N/A' }}<br>
                    <span style="font-size:9px; color:#555;">{{ $order->customer->name ?? 'N/A' }}</span><br>
                    @if($order->status === 'completed')
                        <span class="badge badge-completed">Completed</span>
                    @elseif($order->status === 'pending')
                        <span class="badge badge-pending">Pending</span>
                    @else
                        <span class="badge badge-cancelled">Cancelled</span>
                    @endif
                </td>
                <td class="center">{{ $order->quantity }}</td>
                <td class="right">${{ number_format($order->product->price ?? 0, 2) }}</td>
                <td class="right">${{ number_format($order->total_amount ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="divider">

    {{-- TOTALS --}}
    <div class="total-section">
        <div class="total-row">
            <span>Subtotal ({{ $orders->count() }} orders)</span>
            <span>${{ number_format($orders->sum('total_amount'), 2) }}</span>
        </div>
        <div class="grand-total">
            <span>GRAND TOTAL</span>
            <span>${{ number_format($orders->sum('total_amount'), 2) }}</span>
        </div>
    </div>

    {{-- NOTES --}}
    @if($orders->whereNotNull('note')->where('note', '!=', '')->count())
        <hr class="divider">
        <div class="notes-section">
            <span class="bold">Notes:</span>
            @foreach($orders->whereNotNull('note')->where('note', '!=', '') as $order)
                <div style="margin-top:3px;">
                    <span class="bold">{{ $order->order_number }}:</span> {{ $order->note }}
                </div>
            @endforeach
        </div>
    @endif

    <hr class="divider">

    {{-- FOOTER --}}
    <div class="footer">
        Thank you for your business!<br>
        {{ now()->format('d M Y, H:i') }}
    </div>

</body>
</html>
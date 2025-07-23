
<h2><i class="fas fa-credit-card"></i> Payments</h2>

<form id="paymentForm" action="{{ route('payments.store') }}" method="POST">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">

    <div class="form-group">
        <label>Order Number:</label>
        <input type="text" value="{{ $order->order_number }}" disabled class="form-control" />
    </div>

    <div class="form-group">
        <label>Amount to Pay:</label>
        <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount', $order->total_amount) }}" class="form-control" placeholder="Enter amount" />
        @error('amount')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group" id="payment-method-group">
        <label>Payment Method:</label>
        <select name="payment_method" id="payment_method" class="form-control">
            <option value="">Select method</option>
            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
            <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank</option>
        </select>
        @error('payment_method')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-payment">Submit Payment</button>
</form>









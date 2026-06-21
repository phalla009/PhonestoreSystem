<style>
    #loading-overlay-payment {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.85);
        display: none;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        z-index: 99999;
    }
    .spinner-payment {
        border: 6px solid #f3f3f3;
        border-top: 6px solid #48bb78;
        border-radius: 50%;
        width: 60px; height: 60px;
        animation: spinP 1s linear infinite;
    }
    @keyframes spinP { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    #loading-text-payment { margin-top: 15px; font-size: 16px; color: #333; }
</style>

{{-- Loading Overlay --}}
<div id="loading-overlay-payment">
    <div class="spinner-payment"></div>
    <div id="loading-text-payment">Processing payment...</div>
</div>

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
        <input type="number" name="amount" step="0.01" min="0.01"
               value="{{ old('amount', $order->total_amount) }}"
               class="form-control" placeholder="Enter amount" />
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

    <button type="submit" class="btn-payment">
        <i class="fas fa-check-circle"></i> Submit Payment
    </button>
</form>

<script>
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const paymentMethod = document.getElementById('payment_method').value;

        // Validate payment method before showing loading
        if (!paymentMethod) return; // let existing validation handle it

        document.getElementById('loading-overlay-payment').style.display = 'flex';
    });
</script>
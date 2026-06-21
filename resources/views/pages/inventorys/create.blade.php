@extends('layouts.master')

@section('pageTitle')
    Add Stock
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>
    <style>
        /* ── Page wrapper ── */
        .content-section#add-stock {
            max-width: 720px;
            margin: 0 auto;
        }

        /* ── Card ── */
        .form-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 32px;
            margin-top: 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        /* ── Section label ── */
        .form-section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6b7280;
            margin: 0 0 18px;
        }

        /* ── Field rows ── */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .form-group label span.required {
            color: #ef4444;
            margin-left: 2px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            background: #f9fafb;
            transition: border-color .18s, box-shadow .18s;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
            background: #fff;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 90px;
        }

        /* ── Qty stepper ── */
        .qty-wrapper {
            display: flex;
            align-items: center;
            gap: 0;
            width: fit-content;
        }

        .qty-btn {
            width: 38px;
            height: 40px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            font-size: 18px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
            line-height: 1;
            user-select: none;
        }

        .qty-btn:first-child { border-radius: 8px 0 0 8px; }
        .qty-btn:last-child  { border-radius: 0 8px 8px 0; }
        .qty-btn:hover       { background: #e5e7eb; }
        .qty-btn:active      { background: #d1d5db; }

        .qty-input {
            width: 80px !important;
            text-align: center;
            border-left: none !important;
            border-right: none !important;
            border-radius: 0 !important;
            background: #fff !important;
            font-size: 15px !important;
            font-weight: 600;
        }

        /* ── Stock-type pills ── */
        .type-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .type-pill input[type="radio"] { display: none; }

        .type-pill label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            border: 1.5px solid #d1d5db;
            color: #6b7280;
            cursor: pointer;
            transition: all .18s;
        }

        .type-pill input[type="radio"]:checked + label.pill-in {
            background: #dcfce7; color: #16a34a; border-color: #86efac;
        }
        .type-pill input[type="radio"]:checked + label.pill-out {
            background: #fee2e2; color: #dc2626; border-color: #fca5a5;
        }
        .type-pill input[type="radio"]:checked + label.pill-adj {
            background: #eff6ff; color: #2563eb; border-color: #93c5fd;
        }

        /* ── Divider ── */
        .form-divider {
            border: none;
            border-top: 1px solid #f3f4f6;
            margin: 24px 0;
        }

        /* ── Action buttons ── */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 8px;
        }

        .btn {
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: opacity .15s, transform .12s;
        }

        .btn:hover  { opacity: .88; }
        .btn:active { transform: scale(.97); }

        .btn-primary  { background: #2563eb; color: #fff; }
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        /* ── Validation hint ── */
        .hint {
            font-size: 11.5px;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* ── Alert flash ── */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .alert-error   { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        /* ── Responsive ── */
        @media (max-width: 520px) {
            .form-card { padding: 20px 16px; }
            .form-actions { flex-direction: column-reverse; }
            .btn { width: 100%; text-align: center; }
        }
    </style>
@endsection

@section('content')

    <div class="content-section" id="add-stock">

        {{-- Page heading --}}
        <h2><i class="fas fa-plus-circle"></i> Add Stock</h2>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Form card --}}
        <div class="form-card">
            <form action="{{ route('inventory.store') }}" method="POST" id="stockForm">
                @csrf

                {{-- ── Product selection ── --}}
                <p class="form-section-title"><i class="fas fa-box"></i>&nbsp; Product</p>

                <div class="form-group">
                    <label for="product_id">Select Product <span class="required">*</span></label>
                    <select name="product_id" id="product_id" required>
                        <option value="" disabled selected>— Choose a product —</option>
                        @foreach($products as $product)
                            <option
                                value="{{ $product->id }}"
                                data-stock="{{ $product->stock }}"
                                data-sku="{{ $product->sku ?? '-' }}"
                                {{ old('product_id') == $product->id ? 'selected' : '' }}
                            >
                                {{ $product->name }}
                                (Stock: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <span class="hint" style="color:#dc2626">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Current stock display --}}
                <div class="form-group" id="current-stock-row" style="display:none;">
                    <label>Current Stock</label>
                    <input type="text" id="current-stock-display" disabled
                           style="background:#f3f4f6; color:#6b7280; font-weight:600;">
                </div>

                <hr class="form-divider">

                {{-- ── Stock adjustment ── --}}
                <p class="form-section-title"><i class="fas fa-cubes"></i>&nbsp; Stock Adjustment</p>

                {{-- Type --}}
                <div class="form-group">
                    <label>Type <span class="required">*</span></label>
                    <div class="type-group">
                        <div class="type-pill">
                            <input type="radio" name="type" id="type_in" value="in"
                                {{ old('type', 'in') === 'in' ? 'checked' : '' }}>
                            <label for="type_in" class="pill-in">
                                <i class="fas fa-arrow-down"></i> Stock In
                            </label>
                        </div>
                        <div class="type-pill">
                            <input type="radio" name="type" id="type_out" value="out"
                                {{ old('type') === 'out' ? 'checked' : '' }}>
                            <label for="type_out" class="pill-out">
                                <i class="fas fa-arrow-up"></i> Stock Out
                            </label>
                        </div>
                        <div class="type-pill">
                            <input type="radio" name="type" id="type_adj" value="adjustment"
                                {{ old('type') === 'adjustment' ? 'checked' : '' }}>
                            <label for="type_adj" class="pill-adj">
                                <i class="fas fa-sliders-h"></i> Adjustment
                            </label>
                        </div>
                    </div>
                    @error('type')
                        <span class="hint" style="color:#dc2626">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Quantity --}}
                <div class="form-group">
                    <label for="quantity">Quantity <span class="required">*</span></label>
                    <div class="qty-wrapper">
                        <button type="button" class="qty-btn" id="qty-minus">−</button>
                        <input
                            class="qty-input"
                            type="number"
                            name="quantity"
                            id="quantity"
                            min="1"
                            value="{{ old('quantity', 1) }}"
                            required>
                        <button type="button" class="qty-btn" id="qty-plus">+</button>
                    </div>
                    <span class="hint">Minimum value: 1</span>
                    @error('quantity')
                        <span class="hint" style="color:#dc2626">{{ $message }}</span>
                    @enderror
                </div>

                <hr class="form-divider">

                {{-- ── Extra info ── --}}
                <p class="form-section-title"><i class="fas fa-info-circle"></i>&nbsp; Additional Info</p>

                {{-- Reference / PO number --}}
                <div class="form-group">
                    <label for="reference">Reference / PO No.</label>
                    <input type="text" name="reference" id="reference"
                           placeholder="e.g. PO-2024-001"
                           value="{{ old('reference') }}">
                </div>

                {{-- Note --}}
                <div class="form-group">
                    <label for="note">Note</label>
                    <textarea name="note" id="note"
                              placeholder="Optional remarks...">{{ old('note') }}</textarea>
                </div>

                <hr class="form-divider">

                {{-- Actions --}}
                <div class="form-actions">
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Stock
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Logout Confirm --}}
    <div id="logout-confirm">
        <div class="confirm-box">
            <div class="icon-container">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <p>Are you sure you want to logout?</p>
            <button id="confirm-yes">Yes, Logout!</button>
            <button id="confirm-no">No, Keep it!</button>
        </div>
    </div>

    <script>
        /* ── Logout confirm ── */
        const logoutLink    = document.getElementById('logout-link');
        const logoutConfirm = document.getElementById('logout-confirm');
        const confirmYes    = document.getElementById('confirm-yes');
        const confirmNo     = document.getElementById('confirm-no');
        const logoutForm    = document.getElementById('logout-form');

        logoutLink.addEventListener('click', e => {
            e.preventDefault();
            logoutConfirm.style.display = 'flex';
        });
        confirmYes.addEventListener('click', () => logoutForm.submit());
        confirmNo.addEventListener('click',  () => logoutConfirm.style.display = 'none');

        /* ── Qty stepper ── */
        const qtyInput = document.getElementById('quantity');

        document.getElementById('qty-minus').addEventListener('click', () => {
            const val = parseInt(qtyInput.value) || 1;
            if (val > 1) qtyInput.value = val - 1;
        });

        document.getElementById('qty-plus').addEventListener('click', () => {
            qtyInput.value = (parseInt(qtyInput.value) || 0) + 1;
        });

        qtyInput.addEventListener('input', () => {
            if (parseInt(qtyInput.value) < 1 || qtyInput.value === '') qtyInput.value = 1;
        });

        /* ── Show current stock when product selected ── */
        const productSelect     = document.getElementById('product_id');
        const currentStockRow   = document.getElementById('current-stock-row');
        const currentStockInput = document.getElementById('current-stock-display');

        productSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const stock = opt.getAttribute('data-stock');
            const sku   = opt.getAttribute('data-sku');
            if (stock !== null) {
                currentStockInput.value = stock + ' units' + (sku !== '-' ? '  |  SKU: ' + sku : '');
                currentStockRow.style.display = 'flex';
            } else {
                currentStockRow.style.display = 'none';
            }
        });

        // Restore on page reload with old value
        if (productSelect.value) productSelect.dispatchEvent(new Event('change'));
    </script>

@endsection
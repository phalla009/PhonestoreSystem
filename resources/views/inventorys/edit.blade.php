@extends('layouts.master')

@section('pageTitle')
    Edit Stock
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <script src="{{ URL::asset('js/form.js') }}"></script>

    <style>
        /* ── Loading Overlay ── */
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

        /* ── Base ── */
        #edit-stock *:not(i) { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        #edit-stock { width: 100%; padding: 0; }

        /* ── Page header ── */
        .page-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding: 32px 0 24px;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 36px;
            margin-left: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-eyebrow {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #b0b0b0;
            margin: 0 0 5px;
        }

        .page-header h2 {
            margin: 0;
            font-size: 26px;
            font-weight: 600;
            color: #0f0f0f;
            letter-spacing: -.4px;
        }

        /* ── Flash alerts ── */
        .alert {
            padding: 13px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 3px solid transparent;
        }
        .alert-success { background: #f6fdf8; color: #1a7a45; border-left-color: #34c96a; }
        .alert-error   { background: #fff6f6; color: #c0392b; border-left-color: #e74c3c; }

        /* ── 3-column grid ── */
        .edit-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1px;
            background: #ebebeb;
            border: 1px solid #ebebeb;
            border-radius: 14px;
            overflow: hidden;
        }

        .edit-panel {
            background: #fff;
            padding: 32px 28px;
            animation: fadeUp .28s ease both;
        }
        .edit-panel:nth-child(2) { animation-delay: .06s; }
        .edit-panel:nth-child(3) { animation-delay: .12s; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Panel label ── */
        .panel-label {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #c0c0c0;
            margin: 0 0 22px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .panel-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f4f4f4;
        }

        /* ── Product info ── */
        .product-name {
            font-size: 19px;
            font-weight: 600;
            color: #0f0f0f;
            letter-spacing: -.3px;
            line-height: 1.3;
            margin: 0 0 16px;
        }

        .stock-block {
            padding: 16px 18px;
            background: #fafafa;
            border: 1px solid #f0f0f0;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .stock-num {
            font-size: 28px;
            font-weight: 700;
            font-family: 'DM Mono', monospace;
            color: #0f0f0f;
            letter-spacing: -.5px;
            line-height: 1;
        }

        .stock-sub {
            font-size: 11px;
            color: #bbb;
            margin-top: 4px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 0;
            border-bottom: 1px solid #f7f7f7;
            font-size: 13px;
        }
        .meta-row:last-child { border-bottom: none; }
        .meta-key { color: #aaa; font-weight: 400; }
        .meta-val {
            font-weight: 500;
            color: #222;
            font-family: 'DM Mono', monospace;
            font-size: 12px;
        }

        /* ── Form fields ── */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 20px;
        }
        .form-group:last-of-type { margin-bottom: 0; }

        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #555;
            letter-spacing: .02em;
        }
        .form-group label .req { color: #e74c3c; margin-left: 2px; }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ebebeb;
            border-radius: 8px;
            font-size: 13.5px;
            color: #111;
            background: #fafafa;
            font-family: 'DM Sans', sans-serif;
            transition: border-color .16s, background .16s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #111;
            background: #fff;
        }
        .form-group textarea { resize: vertical; min-height: 90px; }

        /* ── Qty plain input ── */
        .qty-input {
            width: 100% !important;
            padding: 10px 14px !important;
            border: 1px solid #ebebeb !important;
            border-radius: 8px !important;
            font-size: 13.5px !important;
            font-weight: 600;
            color: #111;
            background: #fafafa;
            font-family: 'DM Mono', monospace !important;
            transition: border-color .16s, background .16s;
            box-sizing: border-box !important;
        }
        .qty-input:focus {
            outline: none;
            border-color: #111 !important;
            background: #fff;
            box-shadow: none !important;
        }

        .hint { font-size: 11px; color: #c0c0c0; }
        .err  { font-size: 11.5px; color: #e74c3c; }

        /* ── Type pills ── */
        .type-group { display: flex; flex-direction: column; gap: 7px; }

        .type-pill input[type="radio"] { display: none; }

        .type-pill label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #ebebeb;
            color: #777;
            cursor: pointer;
            background: #fafafa;
            transition: all .15s;
        }
        .type-pill label:hover { border-color: #ccc; background: #fff; color: #333; }

        .pill-icon {
            width: 28px; height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            background: #efefef;
            color: #999;
            flex-shrink: 0;
            transition: all .15s;
        }

        .type-pill input[type="radio"]:checked + label.pill-in  { border-color: #34c96a; background: #f4fdf7; color: #1a7a45; }
        .type-pill input[type="radio"]:checked + label.pill-in  .pill-icon { background: #d0f5e1; color: #1a7a45; }
        .type-pill input[type="radio"]:checked + label.pill-out { border-color: #e74c3c; background: #fff6f5; color: #c0392b; }
        .type-pill input[type="radio"]:checked + label.pill-out .pill-icon { background: #fde8e6; color: #c0392b; }
        .type-pill input[type="radio"]:checked + label.pill-adj { border-color: #3b82f6; background: #f0f6ff; color: #1d4ed8; }
        .type-pill input[type="radio"]:checked + label.pill-adj .pill-icon { background: #dbeafe; color: #1d4ed8; }

        /* ── Action panel ── */
        .action-panel { display: flex; flex-direction: column; justify-content: space-between; }
        .action-top   { flex: 1; }

        .action-bottom {
            display: flex;
            flex-direction: column;
            gap: 9px;
            padding-top: 24px;
            border-top: 1px solid #f4f4f4;
            margin-top: 24px;
        }

        .btn-submit {
            width: 100%;
            padding: 13px 20px;
            background: #0f0f0f;
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: opacity .15s, transform .12s;
            letter-spacing: -.1px;
        }
        .btn-submit:hover  { opacity: .82; }
        .btn-submit:active { transform: scale(.98); }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .edit-grid { grid-template-columns: 1fr 1fr; }
            .edit-panel:nth-child(3) { grid-column: span 2; }
        }
        @media (max-width: 560px) {
            .edit-grid { grid-template-columns: 1fr; }
            .edit-panel:nth-child(3) { grid-column: span 1; }
            .page-header h2 { font-size: 21px; }
        }
    </style>
@endsection

@section('content')

    {{-- Loading Overlay --}}
    <div id="loading-overlay">
        <div class="spinner"></div>
        <div id="loading-text">Loading...</div>
    </div>

    <div class="content-section" id="edit-stock">

        {{-- Page header --}}
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Inventory</p>
                <h2><i class="fas fa-boxes"></i> Edit Stock</h2>
            </div>
            <a href="{{ route('inventory.index') }}" id="backBtn" class="btn btn-back">
                <i class="fas fa-chevron-left"></i> Back
            </a>
        </div>

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

        <form action="{{ route('inventory.update', $product->id) }}" method="POST" id="editStockForm">
            @csrf
            @method('PUT')

            <div class="edit-grid">

                {{-- ── Panel 1 · Product ── --}}
                <div class="edit-panel">
                    <p class="panel-label">Product</p>

                    <div class="product-name">{{ $product->name }}</div>

                    <div class="stock-block">
                        <div class="stock-num">{{ $product->stock }}</div>
                        <div class="stock-sub">units currently in stock</div>
                    </div>

                    <div class="meta-row">
                        <span class="meta-key">SKU</span>
                        <span class="meta-val">{{ $product->sku ?? '—' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Min Stock</span>
                        <span class="meta-val">{{ $product->min_stock ?? '—' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Last Updated</span>
                        <span class="meta-val">{{ $product->updated_at->timezone('Asia/Phnom_Penh')->format('d M Y') }}</span>
                    </div>
                </div>

                {{-- ── Panel 2 · Adjustment ── --}}
                <div class="edit-panel">
                    <p class="panel-label">Adjustment</p>

                    <div class="form-group">
                        <label>Type <span class="req">*</span></label>
                        <div class="type-group">
                            <div class="type-pill">
                                <input type="radio" name="type" id="type_in" value="in"
                                    {{ old('type', 'in') === 'in' ? 'checked' : '' }}>
                                <label for="type_in" class="pill-in">
                                    <span class="pill-icon"><i class="fas fa-arrow-down"></i></span>
                                    Stock In
                                </label>
                            </div>
                            <div class="type-pill">
                                <input type="radio" name="type" id="type_out" value="out"
                                    {{ old('type') === 'out' ? 'checked' : '' }}>
                                <label for="type_out" class="pill-out">
                                    <span class="pill-icon"><i class="fas fa-arrow-up"></i></span>
                                    Stock Out
                                </label>
                            </div>
                            <div class="type-pill">
                                <input type="radio" name="type" id="type_adj" value="adjustment"
                                    {{ old('type') === 'adjustment' ? 'checked' : '' }}>
                                <label for="type_adj" class="pill-adj">
                                    <span class="pill-icon"><i class="fas fa-sliders-h"></i></span>
                                    Adjustment
                                </label>
                            </div>
                        </div>
                        @error('type')
                            <span class="err">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="quantity">Quantity <span class="req">*</span></label>
                        <input class="qty-input" type="number" name="quantity" id="quantity"
                               min="1" value="{{ old('quantity', 1) }}" required
                               placeholder="Enter quantity">
                        <span class="hint">Minimum value: 1</span>
                        @error('quantity')
                            <span class="err">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- ── Panel 3 · Notes + Actions ── --}}
                <div class="edit-panel action-panel">
                    <div class="action-top">
                        <p class="panel-label">Notes</p>

                        <div class="form-group">
                            <label for="reference">Reference / PO No.</label>
                            <input type="text" name="reference" id="reference"
                                   placeholder="e.g. PO-2024-001"
                                   value="{{ old('reference') }}">
                        </div>

                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea name="note" id="note"
                                      placeholder="Optional remarks...">{{ old('note') }}</textarea>
                        </div>
                    </div>

                    <div class="action-bottom">
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fas fa-save"></i> Update Stock
                        </button>
                    </div>
                </div>

            </div>
        </form>
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
        const overlay     = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        /* ── Back button ── */
        document.getElementById('backBtn').addEventListener('click', function(e) {
            e.preventDefault();
            loadingText.textContent = 'Going back...';
            overlay.style.display = 'flex';
            window.location.href = this.getAttribute('href');
        });

        /* ── Submit form ── */
        document.getElementById('editStockForm').addEventListener('submit', function() {
            loadingText.textContent = 'Updating...';
            overlay.style.display = 'flex';
        });

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

        /* ── Qty validation ── */
        const qtyInput = document.getElementById('quantity');
        qtyInput.addEventListener('input', () => {
            if (parseInt(qtyInput.value) < 1 || qtyInput.value === '') qtyInput.value = 1;
        });
    </script>

@endsection
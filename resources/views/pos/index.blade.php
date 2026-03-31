@extends('layouts.pos')

@section('pageTitle')
    Point of Sale
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <style>
        * { box-sizing: border-box; }

        body, .pos-wrapper, .pos-products-panel, .pos-cart-panel {
            font-family: 'DM Sans', sans-serif;
        }

        /* ══════════════════════════════════════
           LAYOUT — DESKTOP (default)
        ══════════════════════════════════════ */
        .pos-wrapper {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start;
            min-height: calc(100vh - 80px);
        }

        /* ── PRODUCTS PANEL ── */
        .pos-products-panel {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 14px 14px 10px;
            max-height: 82vh;
            overflow-y: auto;
        }
        .pos-products-panel::-webkit-scrollbar { width: 5px; }
        .pos-products-panel::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }

        /* ── SEARCH ── */
        .pos-search-wrap { position: relative; margin-bottom: 16px; }
        .pos-search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 14px; pointer-events: none; }
        .pos-search-wrap input { width: 100%; padding: 10px 14px 10px 38px; border: 1px solid #e5e7eb; border-radius: 24px; font-size: 14px; outline: none; transition: border-color 0.2s; font-family: 'DM Sans', sans-serif; }
        .pos-search-wrap input:focus { border-color: #111; }

        /* ── CATEGORY BUTTONS ── */
        .cat-btn { padding: 8px 16px; border: 1px solid #e5e7eb; border-radius: 24px; font-size: 13px; font-family: 'DM Sans', sans-serif; background: #fff; color: #374151; cursor: pointer; transition: all 0.15s; white-space: nowrap; font-weight: 500; }
        .cat-btn:hover { border-color: #a5b4fc; color: #4f46e5; }
        .cat-btn.active { background: #111; color: #fff; border-color: #111; }

        /* ── CATEGORY GROUP ── */
        .category-group { margin-bottom: 22px; }
        .category-group.hidden { display: none; }
        .category-heading { font-size: 18px; font-weight: 700; color: #1f2937; margin: 0 0 10px 2px; letter-spacing: -0.03em; text-transform: uppercase; line-height: 1.2; display: flex; align-items: center; gap: 10px; }
        .category-heading::after { content: ''; flex: 1; height: 1px; border-top: 2px dotted #d1d5db; }

        /* ── PRODUCT GRID ── */
        .product-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }

        /* ── PRODUCT CARD ── */
        .product-card { position: relative; border: 1px solid #ebebeb; border-radius: 12px; cursor: pointer; background: #fff; overflow: hidden; transition: border-color 0.2s, box-shadow 0.25s, transform 0.2s; display: flex; flex-direction: column; animation: cardFadeUp 0.3s ease both; }
        .product-card::after { content: ''; position: absolute; inset: 0; border-radius: 12px; background: rgba(0,0,0,0); transition: background 0.2s; pointer-events: none; }
        .product-card:hover { border-color: #a5b4fc; box-shadow: 0 8px 24px rgba(0,0,0,0.08); transform: translateY(-3px); }
        .product-card:active { transform: translateY(0px) scale(0.98); }
        .product-card.out-of-stock { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

        .product-card-img-wrap { position: relative; width: 100%; height: 150px; background: #f7f7f7; overflow: hidden; padding: 10px;}
        .product-card-img { width: 100%; height: 100%; object-fit: contain; display: block; transition: transform 0.35s ease; }
        .product-card:hover .product-card-img { transform: scale(1.04); }
        .product-card-img-placeholder { width: 100%; height: 100%; background: #f7f7f7; display: flex; align-items: center; justify-content: center; color: #d4d4d4; font-size: 22px; }
        .product-card-add-badge { position: absolute; bottom: 8px; right: 8px; width: 26px; height: 26px; border-radius: 50%; background: #111; color: #fff; font-size: 14px; display: flex; align-items: center; justify-content: center; opacity: 0; transform: scale(0.7); transition: opacity 0.2s, transform 0.2s; font-weight: 300; line-height: 1; }
        .product-card:hover .product-card-add-badge { opacity: 1; transform: scale(1); }

        .product-card-body { padding: 10px 11px 12px; flex: 1; display: flex; flex-direction: column; gap: 2px; }
        .product-card-name { font-size: 12.5px; font-weight: 600; color: #111; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: -0.01em; line-height: 1.3; }
        .product-card-brand { font-size: 10.5px; color: #aaa; font-weight: 400; letter-spacing: 0.02em; text-transform: uppercase; margin-bottom: 6px; }
        .product-card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: auto; }
        .product-card-price { font-family: 'DM Mono', monospace; font-size: 13px; font-weight: 500; color: #ff0000; letter-spacing: -0.02em; }
        .product-card-stock { font-size: 10px; font-weight: 500; color: #bbb; background: #f5f5f5; padding: 2px 7px; border-radius: 20px; letter-spacing: 0.01em; }
        .product-card-stock.low { color: #ef4444; background: #fef2f2; }

        /* ── CART PANEL ── */
        .pos-cart-panel {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 20px;
            position: sticky;
            top: 20px;
            height: fit-content;
            max-height: calc(100vh - 40px);
            display: flex;
            flex-direction: column;
        }
        .pos-cart-panel h3 { font-size: 15px; font-weight: 700; color: #1f2937; margin: 0 0 14px 0; display: flex; align-items: center; gap: 8px; }

        .cart-items {
            min-height: 180px;
            max-height: calc(100vh - 520px);
            overflow-y: auto;
            margin-bottom: 16px;
            padding-right: 6px;
            flex: 1;
        }
        .cart-items::-webkit-scrollbar { width: 6px; }
        .cart-items::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .cart-items::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        .cart-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 120px; color: #d1d5db; font-size: 13px; gap: 8px; }
        .cart-empty i { font-size: 28px; }

        .cart-item { display: flex; align-items: center; gap: 10px; padding: 10px 8px; border-radius: 10px; margin-bottom: 3px; border: 1px solid transparent; transition: background 0.15s, border-color 0.15s; }
        .cart-item:hover { background: #fafafa; border-color: #ebebeb; }
        .cart-item-thumb { width: 42px; height: 42px; border-radius: 8px; object-fit: contain; background: #f3f4f6; flex-shrink: 0; border: 1px solid #ebebeb; padding: 4px; }
        .cart-item-thumb-placeholder { width: 42px; height: 42px; border-radius: 8px; background: #f7f7f7; border: 1px solid #ebebeb; display: flex; align-items: center; justify-content: center; color: #d4d4d4; font-size: 14px; flex-shrink: 0; }
        .cart-item-info { flex: 1; min-width: 0; }
        .cart-item-name { font-size: 12.5px; font-weight: 600; color: #111; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: -0.01em; line-height: 1.3; }
        .cart-item-price { font-size: 11px; color: #ff4545; font-weight: 400; margin-top: 1px; }
        .cart-item-category { display: inline-block; font-size: 10px; font-weight: 500; color: #6366f1; background: #eef2ff; padding: 1px 7px; border-radius: 20px; letter-spacing: 0.02em; text-transform: uppercase; margin-top: 2px; }
        .cart-item-qty { display: flex; align-items: center; gap: 4px; }
        .qty-btn { width: 20px; height: 20px; border-radius: 50%; border: 1px solid #e5e7eb; background: #f9fafb; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1; transition: background 0.15s; }
        .qty-btn:hover { background: #111; border-color: #111; color: #fff; }
        .qty-value { font-size: 12px; font-weight: 600; min-width: 16px; text-align: center; color: #1f2937; }
        .cart-item-total { font-size: 12px; font-weight: 700; color: #ff0000; min-width: 52px; text-align: right; font-family: 'DM Mono', monospace; }
        .cart-item-remove { background: none; border: none; color: #d1d5db; cursor: pointer; font-size: 12px; padding: 2px; transition: color 0.15s; }
        .cart-item-remove:hover { color: #ef4444; }

        .cart-summary { border-top: 2px solid #f3f4f6; padding-top: 12px; }
        .summary-row { display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; margin-bottom: 5px; }
        .summary-row.total { font-size: 14px; font-weight: 700; color: #1f2937; margin-top: 6px; padding-top: 6px; border-top: 1px solid #e5e7eb; }

        /* ── PAYMENT METHOD TOGGLE ── */
        .pay-method-row { display: flex; gap: 8px; margin-bottom: 12px; }
        .pay-method-btn { flex: 1; padding: 9px 10px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; background: #fff; color: #374151; font-family: 'DM Sans', sans-serif; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .pay-method-btn:hover { border-color: #a5b4fc; color: #4f46e5; }
        .pay-method-btn.active-khqr { background: #111; color: #fff; border-color: #111; }
        .pay-method-btn.active-cash { background: #16a34a; color: #fff; border-color: #16a34a; }

        /* ── CASH INPUT ── */
        .cash-input-wrap { display: none; margin-bottom: 10px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 14px; position: sticky;}
        .cash-input-label { font-size: 11px; font-weight: 600; color: #15803d; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; display: block; }
        .cash-input-prefix { position: relative; }
        .cash-input-prefix::before { content: '$'; position: absolute; left: 9px; top: 50%; transform: translateY(-50%); font-size: 13px; color: #6b7280; font-family: 'DM Mono', monospace; pointer-events: none; z-index: 1; }
        .cash-input-field { width: 100%; padding: 9px 12px 9px 24px; border: 1.5px solid #86efac; border-radius: 8px; font-size: 15px; font-weight: 600; font-family: 'DM Mono', monospace; color: #111; outline: none; background: #fff; transition: border-color 0.2s; }
        .cash-input-field:focus { border-color: #16a34a; }
        .cash-change-row { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; padding-top: 10px; border-top: 1px solid #bbf7d0; }
        .cash-change-label { font-size: 12px; color: #15803d; font-weight: 600; }
        .cash-change-val { font-size: 15px; font-weight: 700; color: #15803d; font-family: 'DM Mono', monospace; }
        .cash-change-val.insufficient { color: #ef4444; }

        .btn-checkout { width: 100%; padding: 11px; background: #111; color: #fff; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 12px; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity 0.2s, transform 0.15s, background 0.2s; font-family: 'DM Sans', sans-serif; letter-spacing: -0.01em;}
        .btn-checkout:hover { opacity: 0.85; transform: translateY(-1px); }
        .btn-checkout:disabled { background: #d1d5db !important; cursor: not-allowed; transform: none; opacity: 1; }

        .btn-clear-cart { width: 100%; padding: 8px; background: #fff; color: #ef4444; border: 1px solid #fca5a5; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: pointer; margin-top: 7px; transition: background 0.15s; font-family: 'DM Sans', sans-serif; }
        .btn-clear-cart:hover { background: #fef2f2; }

        /* ── CUSTOMER DISPLAY BUTTON ── */
        .btn-customer-display { width: 100%; padding: 8px; background: #fff; color: #6366f1; border: 1px solid #c7d2fe; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: pointer; margin-top: 7px; transition: background 0.15s; font-family: 'DM Sans', sans-serif; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .btn-customer-display:hover { background: #eef2ff; }

        .no-results { text-align: center; color: #9ca3af; font-size: 13px; padding: 40px 0; }

        /* ── SUCCESS MODAL ── */
        .checkout-success-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 99998; justify-content: center; align-items: center; }
        .checkout-success-box { background: #fff; border-radius: 16px; padding: 40px 32px; text-align: center; max-width: 360px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .checkout-success-box i { font-size: 48px; color: #10b981; margin-bottom: 16px; }
        .checkout-success-box h3 { font-size: 20px; font-weight: 700; color: #1f2937; margin-bottom: 8px; }
        .checkout-success-box p { font-size: 14px; color: #6b7280; margin-bottom: 20px; }
        .checkout-success-box button { padding: 10px 28px; background: #111; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif; }

        /* ── REMOVE / CLEAR MODALS ── */
        .remove-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 99999; justify-content: center; align-items: center; }
        .remove-modal-box { background: #fff; border-radius: 16px; padding: 32px 28px; text-align: center; max-width: 320px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .remove-modal-icon { width: 52px; height: 52px; border-radius: 50%; background: #fef2f2; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .remove-modal-icon i { font-size: 20px; color: #ef4444; }
        .remove-modal-box h3 { font-size: 17px; font-weight: 700; color: #1f2937; margin: 0 0 8px; }
        .remove-modal-box p { font-size: 13px; color: #6b7280; margin: 0 0 22px; line-height: 1.5; }
        .remove-modal-actions { display: flex; gap: 8px; }
        .btn-cancel-remove { flex: 1; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background: #fff; color: #374151; font-family: 'DM Sans', sans-serif; transition: background 0.15s; }
        .btn-cancel-remove:hover { background: #f9fafb; }
        .btn-confirm-remove { flex: 1; padding: 10px; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background: #ef4444; color: #fff; font-family: 'DM Sans', sans-serif; transition: background 0.15s; }
        .btn-confirm-remove:hover { background: #dc2626; }

        /* ── TOAST ── */
        .toast { position: fixed; bottom: 28px; right: 28px; background: #111; color: #fff; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 500; font-family: 'DM Sans', sans-serif; display: flex; align-items: center; gap: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); z-index: 999999; opacity: 0; transform: translateY(12px); transition: opacity 0.25s, transform 0.25s; pointer-events: none; }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success i { color: #10b981; }
        .toast.error   i { color: #ef4444; }

        /* ── ANIMATION ── */
        @keyframes cardFadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .product-card:nth-child(1)  { animation-delay: 0.03s; }
        .product-card:nth-child(2)  { animation-delay: 0.06s; }
        .product-card:nth-child(3)  { animation-delay: 0.09s; }
        .product-card:nth-child(4)  { animation-delay: 0.12s; }
        .product-card:nth-child(5)  { animation-delay: 0.15s; }
        .product-card:nth-child(6)  { animation-delay: 0.18s; }
        .product-card:nth-child(7)  { animation-delay: 0.21s; }
        .product-card:nth-child(8)  { animation-delay: 0.24s; }
        .product-card:nth-child(n+9){ animation-delay: 0.27s; }

        /* ── ADD TO CART MODAL ── */
        .atc-modal-box { background: #fff; border-radius: 20px; padding: 24px 24px 20px; max-width: 400px; width: 94%; box-shadow: 0 24px 60px rgba(0,0,0,0.18); }
        .atc-section-label { font-size: 10.5px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.08em; display: block; margin-bottom: 9px; }
        .atc-chip-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 18px; }
        .atc-chip { padding: 6px 14px; border-radius: 24px; border: 1.5px solid #e5e7eb; background: #fff; color: #374151; font-size: 12.5px; font-weight: 500; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.15s; }
        .atc-chip:hover { border-color: #a5b4fc; color: #4f46e5; }
        .atc-chip.active-disc  { background: #111;    color: #fff; border-color: #111; }
        .atc-chip.active-sugar { background: #6366f1; color: #fff; border-color: #6366f1; }

        /* ════════════════════════════════════════
           KHQR CHECKOUT MODAL
        ════════════════════════════════════════ */
        .khqr-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 100000; justify-content: center; align-items: center; padding: 16px; }
        .khqr-backdrop.open { display: flex; }

        .khqr-modal { background: #fff; border-radius: 20px; width: 100%; max-width: 820px; max-height: 92vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 24px 80px rgba(0,0,0,0.2); animation: khqrIn 0.28s cubic-bezier(0.34,1.4,0.64,1) both; }
        @keyframes khqrIn { from { opacity: 0; transform: translateY(28px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }

        .khqr-header { display: flex; align-items: center; justify-content: space-between; padding: 15px 22px; border-bottom: 1px solid #f0f0f0; flex-shrink: 0; }
        .khqr-header-left { display: flex; align-items: center; gap: 11px; }
        .khqr-logo { width: 36px; height: 36px; border-radius: 8px; background: #E8005A; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .khqr-title    { font-size: 15px; font-weight: 700; color: #1f2937; line-height: 1.2; }
        .khqr-subtitle { font-size: 11px; color: #9ca3af; }
        .khqr-close { background: #f3f4f6; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; font-size: 13px; color: #6b7280; display: flex; align-items: center; justify-content: center; transition: background 0.15s; }
        .khqr-close:hover { background: #e5e7eb; color: #111; }

        .khqr-body { display: grid; grid-template-columns: 1fr 1fr; flex: 1; overflow: hidden; }

        .khqr-left { padding: 20px 22px; border-right: 1px solid #f0f0f0; display: flex; flex-direction: column; overflow: hidden; }
        .khqr-section-label { font-size: 10.5px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px; }
        .khqr-item-list { flex: 1; overflow-y: auto; margin-bottom: 14px; }
        .khqr-item-list::-webkit-scrollbar { width: 4px; }
        .khqr-item-list::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }

        .khqr-item-row { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid #f5f5f5; }
        .khqr-item-row:last-child { border-bottom: none; }
        .khqr-thumb    { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; flex-shrink: 0; border: 1px solid #ebebeb; background: #f9fafb; }
        .khqr-thumb-ph { width: 40px; height: 40px; border-radius: 8px; background: #f9fafb; border: 1px solid #ebebeb; display: flex; align-items: center; justify-content: center; color: #d1d5db; font-size: 14px; flex-shrink: 0; }
        .khqr-item-info { flex: 1; min-width: 0; }
        .khqr-item-name { font-size: 12.5px; font-weight: 600; color: #111; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .khqr-item-meta { font-size: 11px; color: #ff6060; margin-top: 2px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .khqr-disc        { background: #fef2f2; color: #ef4444; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 20px; }
        .khqr-sugar-badge { background: #eef2ff; color: #6366f1; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 20px; }
        .khqr-item-amt { font-size: 13px; font-weight: 700; color: #ff0000; font-family: 'DM Mono', monospace; flex-shrink: 0; }

        .khqr-totals { border-top: 1px solid #f0f0f0; padding-top: 12px; }
        .khqr-total-row { display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; margin-bottom: 5px; }
        .khqr-total-row.grand { font-size: 15px; font-weight: 700; color: #111; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb; }
        .khqr-total-row.disc  { color: #10b981; }

        .khqr-pay-chip { margin-top: 14px; background: #fff5f8; border: 1px solid #fce7f3; border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; gap: 10px; }
        .khqr-pay-icon { width: 28px; height: 28px; border-radius: 6px; background: #E8005A; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .khqr-pay-name { font-size: 12.5px; font-weight: 600; color: #111; }
        .khqr-pay-acc  { font-size: 10.5px; color: #9ca3af; }
        .khqr-sel      { background: #dcfce7; color: #15803d; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px; margin-left: auto; }

        .khqr-right { padding: 20px 22px; display: flex; flex-direction: column; align-items: center; }

        .khqr-amount-card { width: 100%; background: #f9fafb; border-radius: 12px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border: 1px solid #f0f0f0; }
        .khqr-amount-label { font-size: 12px; color: #6b7280; }
        .khqr-amount-val   { font-size: 24px; font-weight: 700; color: #111; font-family: 'DM Mono', monospace; letter-spacing: -0.03em; }

        .khqr-error { width: 100%; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 10px 14px; font-size: 12px; color: #dc2626; display: none; align-items: center; gap: 8px; margin-bottom: 14px; }
        .khqr-error button { margin-left: auto; background: #ef4444; color: #fff; border: none; border-radius: 6px; padding: 4px 10px; font-size: 11px; cursor: pointer; font-weight: 600; }

        .khqr-expiry-warn { width: 100%; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 10px; padding: 8px 12px; font-size: 11px; color: #92400e; display: none; align-items: center; gap: 6px; margin-bottom: 10px; }

        .khqr-skeleton { width: 228px; height: 228px; border-radius: 14px; background: #f3f4f6; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; margin-bottom: 14px; border: 1px solid #e5e7eb; }
        .khqr-skeleton i { font-size: 28px; color: #d1d5db; animation: khqrSpin 1s linear infinite; }
        .khqr-skeleton span { font-size: 12px; color: #9ca3af; }
        @keyframes khqrSpin { to { transform: rotate(360deg); } }

        .khqr-qr-frame { position: relative; border: 1px solid #e5e7eb; border-radius: 14px; padding: 14px; background: #fff; margin-bottom: 14px; display: none; }
        .khqr-qr-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 32px; height: 32px; background: #fff; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
        .khqr-qr-inner  { width: 24px; height: 24px; background: #E8005A; border-radius: 4px; }

        .khqr-merchant-name { font-size: 12px; font-weight: 600; color: #374151; text-align: center; margin-bottom: 2px; }
        .khqr-merchant-acc  { font-size: 11px; color: #9ca3af; text-align: center; margin-bottom: 14px; }

        .khqr-status-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
        .khqr-dot { width: 8px; height: 8px; border-radius: 50%; background: #E8005A; flex-shrink: 0; animation: khqrPulse 1.4s ease-in-out infinite; }
        .khqr-dot.green  { background: #10b981; animation: none; }
        .khqr-dot.grey   { background: #d1d5db; animation: none; }
        .khqr-dot.amber  { background: #f59e0b; animation: khqrPulse 0.8s ease-in-out infinite; }
        @keyframes khqrPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.3;transform:scale(0.7)} }
        .khqr-status-text { font-size: 12.5px; color: #6b7280; }
        .khqr-poll-info   { font-size: 11px; color: #d1d5db; text-align: center; margin-bottom: 14px; min-height: 16px; }

        .khqr-actions { display: flex; gap: 8px; width: 100%; }
        .khqr-btn-cancel { flex: 1; padding: 10px; border: 1px solid #e5e7eb; border-radius: 10px; background: #fff; font-size: 13px; font-weight: 600; cursor: pointer; color: #374151; font-family: 'DM Sans', sans-serif; transition: background 0.15s; }
        .khqr-btn-cancel:hover { background: #f9fafb; }
        .khqr-btn-paid { flex: 2; padding: 10px; border: none; border-radius: 10px; background: #E8005A; color: #fff; font-size: 13px; font-weight: 700; cursor: pointer; font-family: 'DM Sans', sans-serif; display: flex; align-items: center; justify-content: center; gap: 7px; transition: opacity 0.15s; }
        .khqr-btn-paid:hover { opacity: 0.88; }
        .khqr-btn-paid:disabled { background: #d1d5db; cursor: not-allowed; opacity: 1; }

        .khqr-success { display: none; flex-direction: column; align-items: center; justify-content: center; gap: 10px; width: 100%; text-align: center; padding: 20px 0; }
        .khqr-success-circle { width: 64px; height: 64px; border-radius: 50%; background: #dcfce7; display: flex; align-items: center; justify-content: center; margin-bottom: 4px; }
        .khqr-success-circle i { font-size: 28px; color: #16a34a; }
        .khqr-success-title { font-size: 17px; font-weight: 700; color: #111; }
        .khqr-success-sub   { font-size: 13px; color: #6b7280; line-height: 1.6; }

        /* ════════════════════════════════════════
           MOBILE CART DRAWER — FAB + DRAWER
           (visible only on mobile/tablet)
        ════════════════════════════════════════ */
        .cart-fab { display: none; }
        .cart-drawer-overlay { display: none; }
        .cart-drawer { display: none; }

        /* ════════════════════════════════════════
           RESPONSIVE — TABLET (≤1024px)
        ════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .pos-wrapper {
                grid-template-columns: 1fr 290px;
                gap: 14px;
            }

            .product-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
            }

            .product-card-img-wrap { height: 120px; }

            .pos-cart-panel {
                max-height: calc(100vh - 30px);
                padding: 16px;
            }

            .cart-items {
                max-height: calc(100vh - 500px);
                min-height: 140px;
            }

            .khqr-modal { max-width: 720px; }
            .khqr-skeleton { width: 190px; height: 190px; }
        }

        /* ════════════════════════════════════════
           RESPONSIVE — SMALL TABLET (≤860px)
           Stack layout: products on top, cart drawer
        ════════════════════════════════════════ */
        @media (max-width: 860px) {
            .pos-wrapper {
                grid-template-columns: 1fr;
                gap: 0;
            }

            /* Hide the right-column sticky cart on tablet/mobile */
            .pos-cart-panel {
                display: none !important;
            }

            /* Products panel takes full width */
            .pos-products-panel {
                max-height: none;
                border-radius: 12px;
                padding: 12px 12px 80px; /* bottom padding for FAB */
            }

            .product-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .product-card-img-wrap { height: 130px; }
            .category-heading { font-size: 15px; }

            /* ── FLOATING CART BUTTON ── */
            .cart-fab {
                display: flex;
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 9990;
                background: #111;
                color: #fff;
                border: none;
                border-radius: 50px;
                padding: 14px 22px;
                font-size: 14px;
                font-weight: 700;
                font-family: 'DM Sans', sans-serif;
                cursor: pointer;
                gap: 10px;
                align-items: center;
                box-shadow: 0 8px 28px rgba(0,0,0,0.25);
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .cart-fab:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.3); }
            .cart-fab:active { transform: scale(0.97); }
            .cart-fab-count {
                background: #ef4444;
                color: #fff;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 700;
                padding: 2px 7px;
                min-width: 20px;
                text-align: center;
            }
            .cart-fab-total {
                font-family: 'DM Mono', monospace;
                font-size: 13px;
                color: #fff;
                opacity: 0.85;
            }
            .cart-fab.hidden { display: none; }

            /* ── CART DRAWER OVERLAY ── */
            .cart-drawer-overlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0);
                z-index: 9991;
                pointer-events: none;
                transition: background 0.3s;
            }
            .cart-drawer-overlay.open {
                background: rgba(0,0,0,0.45);
                pointer-events: all;
            }

            /* ── CART DRAWER ── */
            .cart-drawer {
                display: flex;
                flex-direction: column;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 9992;
                background: #fff;
                border-radius: 20px 20px 0 0;
                padding: 0 0 env(safe-area-inset-bottom, 0);
                max-height: 88vh;
                transform: translateY(100%);
                transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1);
                box-shadow: 0 -8px 40px rgba(0,0,0,0.18);
            }
            .cart-drawer.open {
                transform: translateY(0);
            }

            .cart-drawer-handle {
                width: 40px;
                height: 4px;
                background: #e5e7eb;
                border-radius: 999px;
                margin: 12px auto 0;
                flex-shrink: 0;
            }

            .cart-drawer-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 18px 10px;
                border-bottom: 1px solid #f3f4f6;
                flex-shrink: 0;
            }
            .cart-drawer-header h3 {
                font-size: 15px;
                font-weight: 700;
                color: #1f2937;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .cart-drawer-close {
                background: #f3f4f6;
                border: none;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 13px;
                color: #6b7280;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .cart-drawer-body {
                flex: 1;
                overflow-y: auto;
                padding: 12px 18px;
                -webkit-overflow-scrolling: touch;
            }
            .cart-drawer-body .cart-items {
                max-height: none;
                min-height: unset;
                margin-bottom: 0;
                padding-right: 0;
            }

            .cart-drawer-footer {
                padding: 12px 18px;
                border-top: 1px solid #f3f4f6;
                flex-shrink: 0;
                background: #fff;
            }

            /* KHQR modal goes full screen on tablet */
            .khqr-modal {
                max-width: 100%;
                border-radius: 16px;
                max-height: 96vh;
            }
            .khqr-body {
                grid-template-columns: 1fr 1fr;
                overflow-y: auto;
            }
        }

        /* ════════════════════════════════════════
           RESPONSIVE — MOBILE PHONE (≤600px)
        ════════════════════════════════════════ */
        @media (max-width: 600px) {
            .content-section { padding: 8px !important; }

            .pos-products-panel {
                border-radius: 10px;
                padding: 10px 10px 90px;
            }

            /* 2-column grid on phone */
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .product-card-img-wrap { height: 110px; padding: 8px; }
            .product-card-body { padding: 8px 9px 10px; }
            .product-card-name { font-size: 11.5px; }
            .product-card-price { font-size: 12px; }
            .product-card-stock { font-size: 9px; padding: 1px 5px; }
            .product-card-add-badge { width: 22px; height: 22px; font-size: 13px; }
            /* Always show add badge on mobile (no hover) */
            .product-card .product-card-add-badge { opacity: 1; transform: scale(1); }

            .category-heading { font-size: 13px; margin-bottom: 8px; }
            #categoryButtons { gap: 5px; }
            .cat-btn { padding: 6px 12px; font-size: 12px; }
            .pos-search-wrap input { font-size: 13px; padding: 9px 12px 9px 34px; }

            /* FAB full-width on very small screens */
            .cart-fab {
                left: 16px;
                right: 16px;
                bottom: 16px;
                border-radius: 14px;
                padding: 14px 18px;
                justify-content: space-between;
            }

            /* Drawer takes more height */
            .cart-drawer { max-height: 92vh; }
            .cart-drawer-header { padding: 12px 14px 10px; }
            .cart-drawer-body { padding: 10px 14px; }
            .cart-drawer-footer { padding: 10px 14px; }

            /* KHQR: stack vertically on phone */
            .khqr-body {
                grid-template-columns: 1fr;
                overflow-y: auto;
                max-height: calc(96vh - 60px);
            }
            .khqr-left {
                border-right: none;
                border-bottom: 1px solid #f0f0f0;
                padding: 16px;
                max-height: none;
                overflow: visible;
            }
            .khqr-right {
                padding: 16px;
                overflow-y: auto;
            }
            .khqr-modal {
                border-radius: 14px;
                max-height: 96vh;
                overflow-y: auto;
            }
            .khqr-header { padding: 12px 16px; }
            .khqr-title  { font-size: 14px; }
            .khqr-amount-val { font-size: 20px; }
            .khqr-skeleton { width: 190px; height: 190px; }

            /* Modals full-width on phone */
            .remove-modal-box  { padding: 24px 18px; }
            .checkout-success-box { padding: 28px 20px; }
            .atc-modal-box { padding: 18px 16px 16px; border-radius: 16px; }

            /* Toast full-width */
            .toast { left: 16px; right: 16px; bottom: 16px; border-radius: 10px; font-size: 12px; }

            /* Cart item adjustments */
            .cart-item { gap: 8px; padding: 8px 6px; }
            .cart-item-thumb { width: 36px; height: 36px; }
            .cart-item-thumb-placeholder { width: 36px; height: 36px; font-size: 12px; }
            .cart-item-name { font-size: 12px; }
            .cart-item-total { font-size: 11px; min-width: 44px; }
            .qty-btn { width: 22px; height: 22px; font-size: 13px; }
            .qty-value { font-size: 12px; }
        }

        /* ════════════════════════════════════════
           RESPONSIVE — VERY SMALL (≤375px)
        ════════════════════════════════════════ */
        @media (max-width: 375px) {
            .product-grid { gap: 6px; }
            .product-card-img-wrap { height: 95px; }
            .product-card-name { font-size: 11px; }
            .category-heading { font-size: 12px; }
            .khqr-skeleton { width: 160px; height: 160px; }
        }

        /* ════════════════════════════════════════
           TOUCH IMPROVEMENTS
        ════════════════════════════════════════ */
        @media (hover: none) and (pointer: coarse) {
            /* Always show add badge on touch devices */
            .product-card .product-card-add-badge { opacity: 1; transform: scale(1); }
            /* Larger tap targets */
            .qty-btn { width: 26px; height: 26px; font-size: 14px; }
            .cat-btn { padding: 9px 16px; }
            .cart-item-remove { padding: 6px; font-size: 14px; }
        }
    </style>
@endsection

@section('content')

    {{-- ════ ADD TO CART MODAL ════ --}}
    <div class="remove-modal" id="addToCartModal" style="z-index:100001;" onclick="handleCheckout()">
        <div class="atc-modal-box">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                <div id="atcThumbWrap"></div>
                <div style="flex:1;min-width:0;">
                    <div id="atcName" style="font-size:15px;font-weight:700;color:#1f2937;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></div>
                    <div id="atcBrand" style="font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:0.05em;margin-top:3px;"></div>
                    <div id="atcPrice" style="font-size:14px;font-weight:600;color:#ff0000;font-family:'DM Mono',monospace;margin-top:5px;"></div>
                </div>
            </div>
            <div style="border-top:1px solid #f3f4f6;margin-bottom:18px;"></div>
            <div style="margin-bottom:18px;">
                <span class="atc-section-label">
                    <i class="fas fa-tag" style="font-size:9px;margin-right:4px;color:#9ca3af;"></i>Discount
                </span>
                <div class="atc-chip-row" id="discountChips">
                    <button class="atc-chip active-disc" data-val="0"    onclick="selectDiscount(this)">No Discount</button>
                    <button class="atc-chip"             data-val="5%"   onclick="selectDiscount(this)">5%</button>
                    <button class="atc-chip"             data-val="10%"  onclick="selectDiscount(this)">10%</button>
                    <button class="atc-chip"             data-val="20%"  onclick="selectDiscount(this)">20%</button>
                    <button class="atc-chip"             data-val="50%"  onclick="selectDiscount(this)">50%</button>
                    <button class="atc-chip"             data-val="100%" onclick="selectDiscount(this)">100%</button>
                </div>
            </div>
            <div style="margin-bottom:22px;">
                <span class="atc-section-label">
                    <i class="fas fa-tint" style="font-size:9px;margin-right:4px;color:#9ca3af;"></i>Sugar Level
                </span>
                <div class="atc-chip-row" id="sugarChips">
                    <button class="atc-chip active-sugar" data-val="100%" onclick="selectSugar(this)">100%</button>
                    <button class="atc-chip"              data-val="75%"  onclick="selectSugar(this)">75%</button>
                    <button class="atc-chip"              data-val="50%"  onclick="selectSugar(this)">50%</button>
                    <button class="atc-chip"              data-val="25%"  onclick="selectSugar(this)">25%</button>
                    <button class="atc-chip"              data-val="0%"   onclick="selectSugar(this)">No Sugar 🚫</button>
                </div>
            </div>
            <div class="remove-modal-actions">
                <button class="btn-cancel-remove" onclick="closeAddToCartModal()">
                    <i class="fas fa-times" style="margin-right:5px;"></i>Cancel
                </button>
                <button class="btn-confirm-remove" onclick="confirmAddToCart()"
                        style="background:#111;display:flex;align-items:center;justify-content:center;gap:7px;">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            </div>
        </div>
    </div>

    {{-- ════ KHQR CHECKOUT MODAL ════ --}}
    <div class="khqr-backdrop" id="khqrBackdrop">
        <div class="khqr-modal">
            <div class="khqr-header">
                <div class="khqr-header-left">
                    <div class="khqr-logo">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <rect x="3"  y="3"  width="7" height="7" rx="1" fill="white"/>
                            <rect x="14" y="3"  width="7" height="7" rx="1" fill="white"/>
                            <rect x="3"  y="14" width="7" height="7" rx="1" fill="white"/>
                            <rect x="14" y="14" width="3" height="3" rx="0.5" fill="white"/>
                            <rect x="19" y="14" width="2" height="2" rx="0.5" fill="white"/>
                            <rect x="14" y="19" width="2" height="2" rx="0.5" fill="white"/>
                            <rect x="18" y="18" width="3" height="3" rx="0.5" fill="white"/>
                        </svg>
                    </div>
                    <div>
                        <div class="khqr-title">Checkout — KHQR Payment</div>
                        <div class="khqr-subtitle" id="khqrOrderRef">Connecting to Bakong…</div>
                    </div>
                </div>
                <button class="khqr-close" onclick="closeKhqrModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="khqr-body">
                <div class="khqr-left">
                    <div class="khqr-section-label">Order summary</div>
                    <div class="khqr-item-list" id="khqrItemList"></div>
                    <div class="khqr-totals">
                        <div class="khqr-total-row"><span>Subtotal</span><span id="khqrSubtotal">$0.00</span></div>
                        <div class="khqr-total-row disc" id="khqrDiscRow" style="display:none;">
                            <span><i class="fas fa-tag" style="font-size:10px;margin-right:3px;"></i>Discount</span>
                            <span id="khqrDiscount">-$0.00</span>
                        </div>
                        <div class="khqr-total-row grand"><span>Total</span><span id="khqrGrand">$0.00</span></div>
                    </div>
                    <div class="khqr-pay-chip">
                        <div class="khqr-pay-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <rect x="3"  y="3"  width="7" height="7" rx="1" fill="white"/>
                                <rect x="14" y="3"  width="7" height="7" rx="1" fill="white"/>
                                <rect x="3"  y="14" width="7" height="7" rx="1" fill="white"/>
                                <rect x="14" y="14" width="3" height="3" rx="0.5" fill="white"/>
                                <rect x="19" y="14" width="2" height="2" rx="0.5" fill="white"/>
                                <rect x="14" y="19" width="2" height="2" rx="0.5" fill="white"/>
                                <rect x="18" y="18" width="3" height="3" rx="0.5" fill="white"/>
                            </svg>
                        </div>
                        <div>
                            <div class="khqr-pay-name">KHQR · Bakong</div>
                            <div class="khqr-pay-acc">{{ env('BAKONG_MERCHANT_ID', 'yourshop@bakong') }}</div>
                        </div>
                        <div class="khqr-sel">Selected</div>
                    </div>
                </div>
                <div class="khqr-right">
                    <div class="khqr-amount-card">
                        <span class="khqr-amount-label">Total due</span>
                        <span class="khqr-amount-val" id="khqrAmountDisplay">$0.00</span>
                    </div>
                    <div class="khqr-expiry-warn" id="khqrExpiryWarn">
                        <i class="fas fa-clock" style="font-size:12px;"></i>
                        <span id="khqrExpiryWarnText">QR expires soon — regenerating…</span>
                    </div>
                    <div class="khqr-error" id="khqrError">
                        <i class="fas fa-exclamation-circle"></i>
                        <span id="khqrErrorText">Failed to generate QR.</span>
                        <button onclick="retryGenerate()">Retry</button>
                    </div>
                    <div class="khqr-skeleton" id="khqrSkeleton">
                        <i class="fas fa-circle-notch"></i>
                        <span id="khqrSkeletonText">Connecting to Bakong API…</span>
                    </div>
                    <div class="khqr-qr-frame" id="khqrQrFrame">
                        <div id="khqrCodeEl"></div>
                        <div class="khqr-qr-center"><div class="khqr-qr-inner"></div></div>
                    </div>
                    <div class="khqr-merchant-name">{{ env('BAKONG_MERCHANT_NAME', 'My Shop') }}</div>
                    <div class="khqr-merchant-acc">{{ env('BAKONG_MERCHANT_ID', 'yourshop@bakong') }} · {{ env('BAKONG_MERCHANT_CITY', 'Phnom Penh') }}</div>
                    <div id="khqrQrView" style="display:flex;flex-direction:column;align-items:center;width:100%;">
                        <div class="khqr-status-row">
                            <div class="khqr-dot" id="khqrDot"></div>
                            <span class="khqr-status-text" id="khqrStatusText">Connecting to Bakong…</span>
                        </div>
                        <div class="khqr-poll-info" id="khqrPollInfo"></div>
                        <div class="khqr-actions">
                            <button class="khqr-btn-cancel" onclick="closeKhqrModal()">Cancel</button>
                            <button class="khqr-btn-paid" id="khqrPaidBtn" onclick="confirmKhqrPaid()" disabled>
                                <i class="fas fa-check-circle"></i> Mark as Paid
                            </button>
                        </div>
                    </div>
                    <div class="khqr-success" id="khqrSuccessView">
                        <div class="khqr-success-circle"><i class="fas fa-check"></i></div>
                        <div class="khqr-success-title">Payment received!</div>
                        <div class="khqr-success-sub" id="khqrSuccessSub">Paid via KHQR · Bakong</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ EXISTING MODALS ════ --}}
    <div class="checkout-success-modal" id="checkoutSuccessModal">
        <div class="checkout-success-box">
            <i class="fas fa-check-circle"></i>
            <h3>Order Placed!</h3>
            <p id="checkoutSummaryText">Your order has been successfully processed.</p>
            <button onclick="closeSuccessModal()">Done</button>
        </div>
    </div>

    <div class="remove-modal" id="removeModal">
        <div class="remove-modal-box">
            <div class="remove-modal-icon"><i class="fas fa-trash"></i></div>
            <h3>Remove Item?</h3>
            <p id="removeModalText">This item will be removed from your cart.</p>
            <div class="remove-modal-actions">
                <button class="btn-cancel-remove" onclick="closeRemoveModal()">Cancel</button>
                <button class="btn-confirm-remove" id="removeConfirmBtn">Remove</button>
            </div>
        </div>
    </div>

    <div class="remove-modal" id="clearModal">
        <div class="remove-modal-box">
            <div class="remove-modal-icon"><i class="fas fa-trash-alt"></i></div>
            <h3>Clear Cart?</h3>
            <p>All items will be removed from your cart.</p>
            <div class="remove-modal-actions">
                <button class="btn-cancel-remove" onclick="closeClearModal()">Cancel</button>
                <button class="btn-confirm-remove" onclick="confirmClearCart()">Clear All</button>
            </div>
        </div>
    </div>

    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toastText">Done.</span>
    </div>

    {{-- ════ MAIN CONTENT ════ --}}
    <div class="content-section">
        <div class="pos-wrapper">

            {{-- LEFT: Products --}}
            <div class="pos-products-panel">
                @php
                    $grouped = $products
                        ->filter(fn($p) => $p->add_to_pos == 1)
                        ->groupBy(fn($p) => $p->category->name ?? 'N/A');
                @endphp

                <div style="margin-bottom:16px;">
                    <div id="categoryButtons" style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:10px;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;">
                        <button class="cat-btn active" data-cat="" onclick="setCatFilter(this, '')">All</button>
                        @foreach($grouped as $categoryName => $items)
                            <button class="cat-btn"
                                    data-cat="{{ strtolower($categoryName) }}"
                                    onclick="setCatFilter(this, '{{ strtolower($categoryName) }}')">
                                {{ $categoryName }}
                            </button>
                        @endforeach
                    </div>
                    <div class="pos-search-wrap" style="margin-bottom:0;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="posSearch" placeholder="Search products by name or brand...">
                    </div>
                </div>

                <div id="productGrid">
                    @php
                        $grouped = $products
                            ->filter(fn($p) => $p->add_to_pos == 1)
                            ->groupBy(fn($p) => $p->category->name ?? 'N/A');
                    @endphp
                    @forelse($grouped as $categoryName => $items)
                        <div class="category-group" data-category="{{ strtolower($categoryName) }}">
                            <div class="category-heading">{{ $categoryName }}</div>
                            <div class="product-grid">
                                @foreach($items as $product)
                                    <div class="product-card {{ $product->stock <= 0 ? 'out-of-stock' : '' }}"
                                         data-id="{{ $product->id }}"
                                         data-name="{{ $product->name }}"
                                         data-brand="{{ $product->category->name ?? 'N/A' }}"
                                         data-price="{{ $product->price }}"
                                         data-stock="{{ $product->stock }}"
                                         data-image="{{ $product->images->isNotEmpty() ? asset('images/products/' . $product->images->first()->image) : '' }}"
                                         onclick="addToCart(this)">
                                        <div class="product-card-img-wrap">
                                            @if($product->images->isNotEmpty())
                                                <img class="product-card-img"
                                                     src="{{ asset('images/products/' . $product->images->first()->image) }}"
                                                     alt="{{ $product->name }}"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="product-card-img-placeholder" style="display:none;"><i class="fas fa-box-open"></i></div>
                                            @else
                                                <div class="product-card-img-placeholder"><i class="fas fa-box-open"></i></div>
                                            @endif
                                            <div class="product-card-add-badge">+</div>
                                        </div>
                                        <div class="product-card-body">
                                            <div class="product-card-name">{{ $product->name }}</div>
                                            <div class="product-card-brand">{{ $product->category->name ?? 'N/A' }}</div>
                                            <div class="product-card-footer">
                                                <div class="product-card-price">${{ number_format($product->price, 2) }}</div>
                                                <div class="product-card-stock {{ $product->stock <= 5 ? 'low' : '' }}">{{ $product->stock }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="no-results">No active products available.</div>
                    @endforelse
                </div>
            </div>

            {{-- RIGHT: Cart (desktop only — hidden on tablet/mobile) --}}
            <div class="pos-cart-panel" id="desktopCartPanel">
                <h3>
                    <i class="fas fa-shopping-cart"></i> Cart
                    <span id="cartCount" style="background:#111;color:#fff;border-radius:999px;font-size:11px;padding:2px 8px;margin-left:4px;">0</span>
                </h3>

                <div class="cart-items" id="cartItems">
                    <div class="cart-empty"><i class="fas fa-shopping-cart"></i><span>No items added yet</span></div>
                </div>

                <div class="cart-summary">
                    <div class="summary-row"><span>Subtotal</span><span id="subtotal">$0.00</span></div>
                    <div class="summary-row" id="discountRow" style="display:none;">
                        <span style="color:#10b981;display:flex;align-items:center;gap:4px;">
                            <i class="fas fa-tag" style="font-size:10px;"></i> Discount
                        </span>
                        <span id="discountAmount" style="color:#10b981;">-$0.00</span>
                    </div>
                    <div class="summary-row total"><span>Total</span><span id="grandTotal">$0.00</span></div>
                </div>

                <form id="posForm" action="{{ route('pos.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="items"          id="posItemsInput">
                    <input type="hidden" name="total"          id="posTotalInput">
                    <input type="hidden" name="payment_method" id="posPaymentMethod" value="khqr">
                    <input type="hidden" name="cash_given"     id="posCashGiven" value="">

                    {{-- Payment method toggle --}}
                    <div class="pay-method-row" style="margin-top:14px;">
                        <button type="button" class="pay-method-btn active-khqr" id="btnPayKhqr" onclick="setPayMethod('khqr')">
                            <i class="fas fa-qrcode" style="font-size:13px;"></i> KHQR
                        </button>
                        <button type="button" class="pay-method-btn" id="btnPayCash" onclick="setPayMethod('cash')">
                            <i class="fas fa-money-bill-wave" style="font-size:13px;"></i> Cash
                        </button>
                    </div>

                    {{-- Cash input (shown only when Cash is selected) --}}
                    <div class="cash-input-wrap" id="cashInputWrap">
                        <span class="cash-input-label">
                            <i class="fas fa-hand-holding-usd" style="font-size:10px;margin-right:4px;"></i>Cash received from customer
                        </span>
                        <div class="cash-input-prefix">
                            <input type="number"
                                   class="cash-input-field"
                                   id="cashInput"
                                   placeholder="0.00"
                                   min="0"
                                   step="0.01"
                                   oninput="updateChange()">
                        </div>
                        <div class="cash-change-row">
                            <span class="cash-change-label">
                                <i class="fas fa-coins" style="font-size:10px;margin-right:4px;"></i>Change
                            </span>
                            <span class="cash-change-val" id="cashChangeVal">—</span>
                        </div>
                    </div>

                    <button type="button" class="btn-checkout" id="checkoutBtn" disabled onclick="handleCheckout()">
                        <i class="fas fa-qrcode" id="checkoutBtnIcon"></i>
                        <span id="checkoutBtnText">Checkout via KHQR</span>
                    </button>
                </form>

                <button class="btn-clear-cart" onclick="clearCart()">
                    <i class="fas fa-trash"></i> Clear Cart
                </button>

                {{-- Customer Display Button --}}
                <button class="btn-customer-display" onclick="openCustomerDisplay()">
                    <i class="fas fa-desktop"></i> Customer Display
                </button>
            </div>

        </div>
    </div>

    {{-- ════ MOBILE/TABLET CART FAB + DRAWER ════ --}}

    {{-- Floating Action Button --}}
    <button class="cart-fab" id="cartFab" onclick="openCartDrawer()">
        <span style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-shopping-cart" style="font-size:15px;"></i>
            <span>Cart</span>
            <span class="cart-fab-count" id="fabCartCount">0</span>
        </span>
        <span class="cart-fab-total" id="fabCartTotal">$0.00</span>
    </button>

    {{-- Drawer Overlay --}}
    <div class="cart-drawer-overlay" id="cartDrawerOverlay" onclick="closeCartDrawer()"></div>

    {{-- Bottom Sheet Drawer --}}
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-drawer-handle"></div>
        <div class="cart-drawer-header">
            <h3>
                <i class="fas fa-shopping-cart"></i> Cart
                <span id="drawerCartCount" style="background:#111;color:#fff;border-radius:999px;font-size:11px;padding:2px 8px;margin-left:4px;">0</span>
            </h3>
            <button class="cart-drawer-close" onclick="closeCartDrawer()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-drawer-body">
            <div class="cart-items" id="drawerCartItems">
                <div class="cart-empty"><i class="fas fa-shopping-cart"></i><span>No items added yet</span></div>
            </div>
        </div>
        <div class="cart-drawer-footer">
            {{-- Summary --}}
            <div style="border-top:2px solid #f3f4f6;padding-top:10px;margin-bottom:10px;">
                <div class="summary-row"><span>Subtotal</span><span id="drawerSubtotal">$0.00</span></div>
                <div class="summary-row" id="drawerDiscountRow" style="display:none;">
                    <span style="color:#10b981;display:flex;align-items:center;gap:4px;">
                        <i class="fas fa-tag" style="font-size:10px;"></i> Discount
                    </span>
                    <span id="drawerDiscountAmount" style="color:#10b981;">-$0.00</span>
                </div>
                <div class="summary-row total"><span>Total</span><span id="drawerGrandTotal">$0.00</span></div>
            </div>

            {{-- Payment method --}}
            <div class="pay-method-row">
                <button type="button" class="pay-method-btn active-khqr" id="drawerBtnPayKhqr" onclick="setPayMethodDrawer('khqr')">
                    <i class="fas fa-qrcode" style="font-size:13px;"></i> KHQR
                </button>
                <button type="button" class="pay-method-btn" id="drawerBtnPayCash" onclick="setPayMethodDrawer('cash')">
                    <i class="fas fa-money-bill-wave" style="font-size:13px;"></i> Cash
                </button>
            </div>

            {{-- Cash input in drawer --}}
            <div class="cash-input-wrap" id="drawerCashInputWrap">
                <span class="cash-input-label">
                    <i class="fas fa-hand-holding-usd" style="font-size:10px;margin-right:4px;"></i>Cash received
                </span>
                <div class="cash-input-prefix">
                    <input type="number" class="cash-input-field" id="drawerCashInput"
                           placeholder="0.00" min="0" step="0.01" oninput="updateDrawerChange()">
                </div>
                <div class="cash-change-row">
                    <span class="cash-change-label"><i class="fas fa-coins" style="font-size:10px;margin-right:4px;"></i>Change</span>
                    <span class="cash-change-val" id="drawerCashChangeVal">—</span>
                </div>
            </div>

            <button type="button" class="btn-checkout" id="drawerCheckoutBtn" disabled onclick="handleDrawerCheckout()"
                    style="margin-top:8px;">
                <i class="fas fa-qrcode" id="drawerCheckoutBtnIcon"></i>
                <span id="drawerCheckoutBtnText">Checkout via KHQR</span>
            </button>

            <div style="display:flex;gap:8px;margin-top:7px;">
                <button class="btn-clear-cart" style="flex:1;margin-top:0;" onclick="clearCart()">
                    <i class="fas fa-trash"></i> Clear
                </button>
                <button class="btn-customer-display" style="flex:1;margin-top:0;" onclick="openCustomerDisplay()">
                    <i class="fas fa-desktop"></i> Display
                </button>
            </div>
        </div>
    </div>

    {{-- ════ SCRIPTS ════ --}}
    <script>
    const overlay     = document.getElementById('loading-overlay');
    const loadingText = document.getElementById('loading-text');
    function showLoading(msg) {
        if (loadingText) loadingText.textContent = msg || 'Loading...';
        if (overlay) overlay.style.display = 'flex';
    }

    /* ─────────────────────────────────────────────
       STATE
    ───────────────────────────────────────────── */
    let cart            = {};
    let activeCat       = '';
    let pendingRemoveId = null;
    let khqrMd5         = null;
    let khqrOrderRef    = null;
    let pollTimer       = null;
    let expiryTimer     = null;
    let pollCount       = 0;
    let currentTotal    = 0;
    let khqrExpiresAt   = null;
    let pendingCard      = null;
    let selectedDiscount = '0';
    let selectedSugar    = '100%';
    let activePayMethod  = 'khqr';
    let drawerPayMethod  = 'khqr';

    const POLL_INTERVAL_MS = 5000;
    const POLL_MAX_CYCLES  = 60;
    const EXPIRY_WARN_SECS = 60;

    /* ─────────────────────────────────────────────
       RESPONSIVE HELPERS
    ───────────────────────────────────────────── */
    function isMobileTablet() {
        return window.innerWidth <= 860;
    }

    /* ─────────────────────────────────────────────
       CART DRAWER (mobile/tablet)
    ───────────────────────────────────────────── */
    function openCartDrawer() {
        document.getElementById('cartDrawer').classList.add('open');
        document.getElementById('cartDrawerOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeCartDrawer() {
        document.getElementById('cartDrawer').classList.remove('open');
        document.getElementById('cartDrawerOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }

    /* Swipe-down to close drawer */
    (function() {
        const drawer = document.getElementById('cartDrawer');
        let startY = 0, isDragging = false;

        drawer.addEventListener('touchstart', function(e) {
            const body = drawer.querySelector('.cart-drawer-body');
            if (body && body.scrollTop > 0) return; // don't close if scrolled
            startY = e.touches[0].clientY;
            isDragging = true;
        }, { passive: true });

        drawer.addEventListener('touchmove', function(e) {
            if (!isDragging) return;
            const dy = e.touches[0].clientY - startY;
            if (dy > 0) {
                drawer.style.transform = `translateY(${dy}px)`;
                drawer.style.transition = 'none';
            }
        }, { passive: true });

        drawer.addEventListener('touchend', function(e) {
            if (!isDragging) return;
            isDragging = false;
            const dy = e.changedTouches[0].clientY - startY;
            drawer.style.transition = '';
            if (dy > 90) {
                drawer.style.transform = '';
                closeCartDrawer();
            } else {
                drawer.style.transform = '';
            }
        });
    })();

    /* ─────────────────────────────────────────────
       CUSTOMER DISPLAY — BroadcastChannel
    ───────────────────────────────────────────── */
    const cdChannel = new BroadcastChannel('cart-sync');

    function openCustomerDisplay() {
        closeCartDrawer();
        window.open('{{ route("pos.display") }}', 'customer-display', 'width=1280,height=720,toolbar=0,menubar=0,location=0');
    }

    function broadcastCart() {
        const keys = Object.keys(cart);
        let subtotal = 0, total = 0;
        const items = keys.map(key => {
            const item = cart[key];
            const lineTotal = calculateItemTotal(item);
            subtotal += item.price * item.qty;
            total    += lineTotal;
            return {
                name:      item.name,
                brand:     item.brand,
                image:     item.image,
                price:     item.price,
                qty:       item.qty,
                discount:  item.discount,
                sugar:     item.sugar,
                lineTotal: lineTotal,
            };
        });
        const discount = subtotal - total;
        const payload  = { items, subtotal, total, discount };

        cdChannel.postMessage({ type: 'cart-update', data: payload });
        try { localStorage.setItem('cd_cart', JSON.stringify(payload)); } catch(e) {}
    }

    function broadcastClear() {
        cdChannel.postMessage({ type: 'cart-clear' });
        try { localStorage.removeItem('cd_cart'); } catch(e) {}
    }

    function broadcastSuccess(total) {
        cdChannel.postMessage({ type: 'checkout-success', data: { total } });
        try { localStorage.removeItem('cd_cart'); } catch(e) {}
    }

    function makeCartKey(productId, discount, sugar) {
        return `${productId}::${discount}::${sugar}`;
    }

    /* ─────────────────────────────────────────────
       ADD TO CART MODAL
    ───────────────────────────────────────────── */
    function addToCart(card) {
        pendingCard      = card;
        selectedDiscount = '0';
        selectedSugar    = '100%';

        document.querySelectorAll('#discountChips .atc-chip').forEach(b => {
            b.classList.toggle('active-disc', b.dataset.val === '0');
        });
        document.querySelectorAll('#sugarChips .atc-chip').forEach(b => {
            b.classList.toggle('active-sugar', b.dataset.val === '100%');
        });

        document.getElementById('atcName').textContent  = card.dataset.name;
        document.getElementById('atcBrand').textContent = card.dataset.brand;
        document.getElementById('atcPrice').textContent = '$' + parseFloat(card.dataset.price).toFixed(2);

        document.getElementById('atcThumbWrap').innerHTML = card.dataset.image
            ? `<img src="${card.dataset.image}" style="width:56px;height:56px;border-radius:12px;object-fit:contain;border:1px solid #ebebeb;flex-shrink:0;padding:4px;">`
            : `<div style="width:56px;height:56px;border-radius:12px;background:#f7f7f7;border:1px solid #ebebeb;display:flex;align-items:center;justify-content:center;color:#d4d4d4;font-size:20px;flex-shrink:0;"><i class="fas fa-box-open"></i></div>`;

        document.getElementById('addToCartModal').style.display = 'flex';
    }

    function selectDiscount(btn) {
        selectedDiscount = btn.dataset.val;
        document.querySelectorAll('#discountChips .atc-chip').forEach(b => {
            b.classList.toggle('active-disc', b === btn);
        });
    }

    function selectSugar(btn) {
        selectedSugar = btn.dataset.val;
        document.querySelectorAll('#sugarChips .atc-chip').forEach(b => {
            b.classList.toggle('active-sugar', b === btn);
        });
    }

    function confirmAddToCart() {
        if (!pendingCard) return;

        const card      = pendingCard;
        const productId = card.dataset.id;
        const stock     = parseInt(card.dataset.stock);

        const usedQty = Object.values(cart)
            .filter(i => i.productId === productId)
            .reduce((s, i) => s + i.qty, 0);

        if (usedQty >= stock) {
            closeAddToCartModal();
            showToast('Maximum stock reached for this product.', 'error');
            pendingCard = null;
            return;
        }

        const cartKey = makeCartKey(productId, selectedDiscount, selectedSugar);

        if (cart[cartKey]) {
            cart[cartKey].qty++;
        } else {
            cart[cartKey] = {
                cartKey, productId,
                name:     card.dataset.name,
                brand:    card.dataset.brand,
                price:    parseFloat(card.dataset.price),
                stock,
                qty:      1,
                image:    card.dataset.image || '',
                discount: selectedDiscount,
                sugar:    selectedSugar,
            };
        }

        renderCart();
        closeAddToCartModal();
        showToast(`"${card.dataset.name}" added to cart.`, 'success');
        pendingCard = null;
    }

    function closeAddToCartModal() {
        document.getElementById('addToCartModal').style.display = 'none';
        pendingCard = null;
    }

    /* ─────────────────────────────────────────────
       CART RENDER — updates both desktop panel + mobile drawer
    ───────────────────────────────────────────── */
    function renderCart() {
        const keys = Object.keys(cart);

        // ── EMPTY STATE ──
        if (!keys.length) {
            const emptyHtml = '<div class="cart-empty"><i class="fas fa-shopping-cart"></i><span>No items added yet</span></div>';

            ['cartItems', 'drawerCartItems'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.innerHTML = emptyHtml;
            });

            ['cartCount','drawerCartCount','fabCartCount'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '0';
            });
            ['subtotal','drawerSubtotal'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '$0.00';
            });
            ['grandTotal','drawerGrandTotal','fabCartTotal'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '$0.00';
            });

            ['discountRow','drawerDiscountRow'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });

            ['checkoutBtn','drawerCheckoutBtn'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.disabled = true;
            });

            broadcastClear();
            return;
        }

        let subtotal = 0, total = 0, count = 0, html = '';

        keys.forEach(key => {
            const item      = cart[key];
            subtotal       += item.price * item.qty;
            const lineTotal = calculateItemTotal(item);
            total          += lineTotal;
            count          += item.qty;

            const thumb = item.image
                ? `<img class="cart-item-thumb" src="${item.image}" alt="${item.name}">`
                : `<div class="cart-item-thumb-placeholder"><i class="fas fa-box-open"></i></div>`;

            const discBadge = (item.discount && item.discount !== '0')
                ? `<span style="display:inline-block;font-size:9.5px;font-weight:600;color:#ef4444;background:#fef2f2;padding:1px 6px;border-radius:20px;margin-left:3px;">${item.discount}</span>`
                : '';

            const sugarBadge = `<span style="display:inline-block;font-size:9.5px;font-weight:600;color:#6366f1;background:#eef2ff;padding:1px 6px;border-radius:20px;margin-left:3px;">${item.sugar}</span>`;

            html += `
                <div class="cart-item">
                    ${thumb}
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price" style="margin-top:3px;">
                            $${item.price.toFixed(2)}${discBadge}${sugarBadge}
                        </div>
                    </div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" data-cart-key="${key}" data-delta="-1">−</button>
                        <span class="qty-value">${item.qty}</span>
                        <button class="qty-btn" data-cart-key="${key}" data-delta="1">+</button>
                    </div>
                    <div class="cart-item-total">$${lineTotal.toFixed(2)}</div>
                    <button class="cart-item-remove" data-cart-key="${key}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`;
        });

        // Render into BOTH containers
        ['cartItems', 'drawerCartItems'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = html;
        });

        // Update counts
        ['cartCount','drawerCartCount','fabCartCount'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = count;
        });
        ['subtotal','drawerSubtotal'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = `$${subtotal.toFixed(2)}`;
        });
        ['grandTotal','drawerGrandTotal'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = `$${total.toFixed(2)}`;
        });

        const fabTotal = document.getElementById('fabCartTotal');
        if (fabTotal) fabTotal.textContent = `$${total.toFixed(2)}`;

        const savings = subtotal - total;
        ['discountRow','drawerDiscountRow'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            if (savings > 0.001) {
                el.style.display = 'flex';
                const amtEl = document.getElementById(id === 'discountRow' ? 'discountAmount' : 'drawerDiscountAmount');
                if (amtEl) amtEl.textContent = `-$${savings.toFixed(2)}`;
            } else {
                el.style.display = 'none';
            }
        });

        ['checkoutBtn','drawerCheckoutBtn'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.disabled = false;
        });

        document.getElementById('posItemsInput').value = JSON.stringify(
            keys.map(key => ({
                id:       cart[key].productId,
                qty:      cart[key].qty,
                price:    cart[key].price,
                discount: cart[key].discount,
                sugar:    cart[key].sugar,
            }))
        );
        document.getElementById('posTotalInput').value = total.toFixed(2);
        currentTotal = total;

        if (activePayMethod === 'cash') updateChange();
        if (drawerPayMethod === 'cash') updateDrawerChange();

        broadcastCart();
    }

    function calculateItemTotal(item) {
        let t = item.price * item.qty;
        if (item.discount && item.discount !== '0') {
            if (item.discount.includes('%')) {
                t -= t * (parseFloat(item.discount) / 100);
            } else if (item.discount.includes('$')) {
                t -= parseFloat(item.discount);
            }
        }
        return Math.max(0, t);
    }

    /* ─────────────────────────────────────────────
       CART EVENT DELEGATION (both desktop + drawer)
    ───────────────────────────────────────────── */
    function handleCartClick(e) {
        const qtyBtn = e.target.closest('.qty-btn');
        if (qtyBtn) {
            const cartKey = qtyBtn.dataset.cartKey;
            const delta   = parseInt(qtyBtn.dataset.delta);
            if (cartKey) changeQty(cartKey, delta);
            return;
        }
        const removeBtn = e.target.closest('.cart-item-remove');
        if (removeBtn) {
            const cartKey = removeBtn.dataset.cartKey;
            if (cartKey) removeItem(cartKey);
        }
    }

    document.getElementById('cartItems').addEventListener('click', handleCartClick);
    document.getElementById('drawerCartItems').addEventListener('click', handleCartClick);

    /* ─────────────────────────────────────────────
       CART ACTIONS
    ───────────────────────────────────────────── */
    function changeQty(cartKey, delta) {
        if (!cart[cartKey]) return;

        if (delta > 0) {
            const usedQty = Object.values(cart)
                .filter(i => i.productId === cart[cartKey].productId)
                .reduce((s, i) => s + i.qty, 0);
            if (usedQty >= cart[cartKey].stock) return;
        }

        cart[cartKey].qty += delta;
        if (cart[cartKey].qty <= 0) delete cart[cartKey];
        renderCart();
    }

    function removeItem(cartKey) {
        pendingRemoveId = cartKey;
        document.getElementById('removeModalText').textContent =
            `"${cart[cartKey]?.name || 'this item'}" will be removed from your cart.`;
        document.getElementById('removeModal').style.display  = 'flex';
        document.getElementById('removeConfirmBtn').onclick   = confirmRemove;
    }

    function confirmRemove() {
        if (pendingRemoveId && cart[pendingRemoveId]) {
            const name = cart[pendingRemoveId].name;
            delete cart[pendingRemoveId];
            renderCart();
            showToast(`"${name}" removed from cart.`, 'success');
        }
        closeRemoveModal();
    }

    function closeRemoveModal() {
        document.getElementById('removeModal').style.display = 'none';
        pendingRemoveId = null;
    }

    function clearCart() {
        if (!Object.keys(cart).length) return;
        document.getElementById('clearModal').style.display = 'flex';
    }

    function confirmClearCart() {
        cart = {};
        renderCart();
        closeClearModal();
        showToast('Cart cleared.', 'success');
        broadcastClear();
    }

    function closeClearModal() {
        document.getElementById('clearModal').style.display = 'none';
    }

    /* ─────────────────────────────────────────────
       TOAST
    ───────────────────────────────────────────── */
    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toast');
        document.getElementById('toastText').textContent = msg;
        toast.querySelector('i').className = type === 'success'
            ? 'fas fa-check-circle'
            : 'fas fa-times-circle';
        toast.className = `toast ${type}`;
        requestAnimationFrame(() => toast.classList.add('show'));
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.remove('show'), 2800);
    }

    /* ─────────────────────────────────────────────
       SEARCH & FILTER
    ───────────────────────────────────────────── */
    function setCatFilter(btn, cat) {
        activeCat = cat;
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        filterProducts();
    }

    function filterProducts() {
        const q = document.getElementById('posSearch').value.toLowerCase();
        document.querySelectorAll('.category-group').forEach(group => {
            if (activeCat && group.dataset.category !== activeCat) {
                group.classList.add('hidden');
                return;
            }
            let visible = 0;
            group.querySelectorAll('.product-card').forEach(c => {
                const match = c.dataset.name.toLowerCase().includes(q) ||
                              c.dataset.brand.toLowerCase().includes(q);
                c.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            group.classList.toggle('hidden', visible === 0);
        });
    }

    document.getElementById('posSearch').addEventListener('input', filterProducts);

    /* ─────────────────────────────────────────────
       SUCCESS MODAL
    ───────────────────────────────────────────── */
    function closeSuccessModal() {
        document.getElementById('checkoutSuccessModal').style.display = 'none';
        cart = {};
        renderCart();
    }

    @if(session('pos_success'))
        document.getElementById('checkoutSummaryText').textContent = '{{ session('pos_success') }}';
        document.getElementById('checkoutSuccessModal').style.display = 'flex';
    @endif

    /* ─────────────────────────────────────────────
       PAYMENT METHOD TOGGLE — DESKTOP
    ───────────────────────────────────────────── */
    function setPayMethod(method) {
        activePayMethod = method;

        document.getElementById('btnPayKhqr').className = 'pay-method-btn' + (method === 'khqr' ? ' active-khqr' : '');
        document.getElementById('btnPayCash').className = 'pay-method-btn' + (method === 'cash' ? ' active-cash' : '');

        const cashWrap = document.getElementById('cashInputWrap');
        cashWrap.style.display = method === 'cash' ? 'block' : 'none';

        const icon = document.getElementById('checkoutBtnIcon');
        const text = document.getElementById('checkoutBtnText');

        if (method === 'cash') {
            icon.className           = 'fas fa-money-bill-wave';
            text.textContent         = 'Checkout — Cash';
            document.getElementById('checkoutBtn').style.background = '#16a34a';
        } else {
            icon.className           = 'fas fa-qrcode';
            text.textContent         = 'Checkout via KHQR';
            document.getElementById('checkoutBtn').style.background = '#111';
        }

        document.getElementById('posPaymentMethod').value = method;
        if (method === 'cash') updateChange();
    }

    /* ─────────────────────────────────────────────
       PAYMENT METHOD TOGGLE — DRAWER (mobile)
    ───────────────────────────────────────────── */
    function setPayMethodDrawer(method) {
        drawerPayMethod = method;

        document.getElementById('drawerBtnPayKhqr').className = 'pay-method-btn' + (method === 'khqr' ? ' active-khqr' : '');
        document.getElementById('drawerBtnPayCash').className = 'pay-method-btn' + (method === 'cash' ? ' active-cash' : '');

        const cashWrap = document.getElementById('drawerCashInputWrap');
        cashWrap.style.display = method === 'cash' ? 'block' : 'none';

        const icon = document.getElementById('drawerCheckoutBtnIcon');
        const text = document.getElementById('drawerCheckoutBtnText');

        if (method === 'cash') {
            icon.className = 'fas fa-money-bill-wave';
            text.textContent = 'Checkout — Cash';
            document.getElementById('drawerCheckoutBtn').style.background = '#16a34a';
        } else {
            icon.className = 'fas fa-qrcode';
            text.textContent = 'Checkout via KHQR';
            document.getElementById('drawerCheckoutBtn').style.background = '#111';
        }

        document.getElementById('posPaymentMethod').value = method;
        if (method === 'cash') updateDrawerChange();
    }

    /* ─────────────────────────────────────────────
       CASH CHANGE CALCULATOR — DESKTOP
    ───────────────────────────────────────────── */
    function updateChange() {
        const total  = parseFloat(document.getElementById('posTotalInput').value) || currentTotal;
        const given  = parseFloat(document.getElementById('cashInput').value)     || 0;
        const change = given - total;
        const el     = document.getElementById('cashChangeVal');

        document.getElementById('posCashGiven').value = given > 0 ? given.toFixed(2) : '';

        if (!given) {
            el.textContent = '—';
            el.className   = 'cash-change-val';
            return;
        }

        el.textContent = change >= 0
            ? `$${change.toFixed(2)}`
            : `-$${Math.abs(change).toFixed(2)}`;
        el.className   = 'cash-change-val' + (change < 0 ? ' insufficient' : '');
    }

    /* ─────────────────────────────────────────────
       CASH CHANGE CALCULATOR — DRAWER
    ───────────────────────────────────────────── */
    function updateDrawerChange() {
        const total  = parseFloat(document.getElementById('posTotalInput').value) || currentTotal;
        const given  = parseFloat(document.getElementById('drawerCashInput').value) || 0;
        const change = given - total;
        const el     = document.getElementById('drawerCashChangeVal');

        document.getElementById('posCashGiven').value = given > 0 ? given.toFixed(2) : '';

        if (!given) {
            el.textContent = '—';
            el.className   = 'cash-change-val';
            return;
        }

        el.textContent = change >= 0
            ? `$${change.toFixed(2)}`
            : `-$${Math.abs(change).toFixed(2)}`;
        el.className   = 'cash-change-val' + (change < 0 ? ' insufficient' : '');
    }

    /* ─────────────────────────────────────────────
       UNIFIED CHECKOUT HANDLER — DESKTOP
    ───────────────────────────────────────────── */
    function handleCheckout() {
        if (activePayMethod === 'khqr') {
            openKhqrModal();
            return;
        }

        const total = parseFloat(document.getElementById('posTotalInput').value) || currentTotal;
        const given = parseFloat(document.getElementById('cashInput').value)     || 0;

        if (given < total) {
            showToast('Cash given is less than the total amount.', 'error');
            document.getElementById('cashInput').focus();
            return;
        }

        document.getElementById('posPaymentMethod').value = 'cash';
        document.getElementById('posCashGiven').value     = given.toFixed(2);
        _submitCheckout(total);
    }

    /* ─────────────────────────────────────────────
       UNIFIED CHECKOUT HANDLER — DRAWER (mobile)
    ───────────────────────────────────────────── */
    function handleDrawerCheckout() {
        if (drawerPayMethod === 'khqr') {
            closeCartDrawer();
            openKhqrModal();
            return;
        }

        const total = parseFloat(document.getElementById('posTotalInput').value) || currentTotal;
        const given = parseFloat(document.getElementById('drawerCashInput').value) || 0;

        if (given < total) {
            showToast('Cash given is less than the total amount.', 'error');
            document.getElementById('drawerCashInput').focus();
            return;
        }

        document.getElementById('posPaymentMethod').value = 'cash';
        document.getElementById('posCashGiven').value     = given.toFixed(2);
        _submitCheckout(total);
    }

    function _submitCheckout(total) {
        document.getElementById('posItemsInput').value = JSON.stringify(
            Object.keys(cart).map(key => ({
                id:       cart[key].productId,
                qty:      cart[key].qty,
                price:    cart[key].price,
                discount: cart[key].discount,
                sugar:    cart[key].sugar,
            }))
        );
        document.getElementById('posTotalInput').value = total.toFixed(2);
        broadcastSuccess(total);
        document.getElementById('posForm').submit();
    }

    /* ─────────────────────────────────────────────
       KHQR — OPEN / CLOSE
    ───────────────────────────────────────────── */
    function openKhqrModal() {
        const keys = Object.keys(cart);
        if (!keys.length) return;

        let subtotal = 0, total = 0, html = '';

        keys.forEach(key => {
            const item      = cart[key];
            subtotal       += item.price * item.qty;
            const lineTotal = calculateItemTotal(item);
            total          += lineTotal;

            const thumb = item.image
                ? `<img class="khqr-thumb" src="${item.image}" alt="${item.name}">`
                : `<div class="khqr-thumb-ph"><i class="fas fa-box-open"></i></div>`;

            const discBadge = (item.discount && item.discount !== '0')
                ? `<span class="khqr-disc">${item.discount} off</span>` : '';

            html += `
                <div class="khqr-item-row">
                    ${thumb}
                    <div class="khqr-item-info">
                        <div class="khqr-item-name">${item.name} ×${item.qty}</div>
                        <div class="khqr-item-meta">
                            $${item.price.toFixed(2)}
                            ${discBadge}
                            <span class="khqr-sugar-badge">${item.sugar}</span>
                        </div>
                    </div>
                    <div class="khqr-item-amt">$${lineTotal.toFixed(2)}</div>
                </div>`;
        });

        document.getElementById('khqrItemList').innerHTML        = html;
        document.getElementById('khqrSubtotal').textContent      = `$${subtotal.toFixed(2)}`;
        document.getElementById('khqrGrand').textContent         = `$${total.toFixed(2)}`;
        document.getElementById('khqrAmountDisplay').textContent = `$${total.toFixed(2)}`;

        const savings = subtotal - total;
        const discRow = document.getElementById('khqrDiscRow');
        if (savings > 0.001) {
            document.getElementById('khqrDiscount').textContent = `-$${savings.toFixed(2)}`;
            discRow.style.display = 'flex';
        } else {
            discRow.style.display = 'none';
        }

        currentTotal = total;

        setKhqrState('loading');
        document.getElementById('khqrExpiryWarn').style.display = 'none';
        document.getElementById('khqrPaidBtn').disabled          = true;
        document.getElementById('khqrOrderRef').textContent      = 'Connecting to Bakong…';
        document.getElementById('khqrDot').className             = 'khqr-dot';
        document.getElementById('khqrStatusText').textContent    = 'Connecting to Bakong…';
        document.getElementById('khqrPollInfo').textContent      = '';

        // document.getElementById('khqrBackdrop').classList.add('open');
        generateKhqr();
    }

    function closeKhqrModal() {
        // document.getElementById('khqrBackdrop').classList.remove('open');
        stopPolling();
        clearTimeout(expiryTimer);
        cdChannel.postMessage({ type: 'checkout-qr-hide' });
    }

    /* ─────────────────────────────────────────────
       KHQR — UI STATE
    ───────────────────────────────────────────── */
    function setKhqrState(state) {
        document.getElementById('khqrSkeleton').style.display    = state === 'loading'  ? 'flex'  : 'none';
        document.getElementById('khqrQrFrame').style.display     = state === 'qr'       ? 'block' : 'none';
        document.getElementById('khqrError').style.display       = state === 'error'    ? 'flex'  : 'none';
        document.getElementById('khqrSuccessView').style.display = state === 'success'  ? 'flex'  : 'none';
        document.getElementById('khqrQrView').style.display      = state === 'success'  ? 'none'  : 'flex';
    }

    function setDotState(state) {
        const dot = document.getElementById('khqrDot');
        dot.className = 'khqr-dot';
        if (state === 'green') dot.classList.add('green');
        if (state === 'grey')  dot.classList.add('grey');
        if (state === 'amber') dot.classList.add('amber');
    }

    /* ─────────────────────────────────────────────
       KHQR — GENERATE QR
    ───────────────────────────────────────────── */
    function generateKhqr() {
        stopPolling();
        clearTimeout(expiryTimer);
        document.getElementById('khqrExpiryWarn').style.display  = 'none';
        document.getElementById('khqrSkeletonText').textContent  = 'Generating QR…';
        setKhqrState('loading');

        fetch('{{ route("pos.khqr.generate") }}', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ amount: currentTotal }),
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Failed');

            khqrMd5       = data.md5;
            khqrOrderRef  = data.order_ref;
            khqrExpiresAt = new Date(data.expires_at);

            document.getElementById('khqrOrderRef').textContent = `Ref: ${khqrOrderRef}`;

            document.getElementById('khqrCodeEl').innerHTML = '';
            new QRCode(document.getElementById('khqrCodeEl'), {
                text:         data.qr,
                width:        200,
                height:       200,
                correctLevel: QRCode.CorrectLevel.M,
            });

            setKhqrState('qr');
            cdChannel.postMessage({
                type: 'checkout-qr',
                data: {
                    qr:       data.qr,
                    total:    currentTotal,
                    subtotal: parseFloat(document.getElementById('khqrSubtotal').textContent.replace('$','')),
                    discount: parseFloat(document.getElementById('khqrDiscount')?.textContent.replace('-$','') || 0),
                    items:    Object.values(cart).map(item => ({
                        name:      item.name,
                        image:     item.image,
                        price:     item.price,
                        qty:       item.qty,
                        sugar:     item.sugar,
                        discount:  item.discount,
                        lineTotal: calculateItemTotal(item),
                    })),
                }
            });

            setDotState('waiting');
            document.getElementById('khqrStatusText').textContent = 'Waiting for payment…';
            document.getElementById('khqrPaidBtn').disabled = false;

            startPolling();
            scheduleExpiryWarning();
        })
        .catch(err => {
            document.getElementById('khqrErrorText').textContent = err.message || 'Failed to generate QR.';
            setKhqrState('error');
            document.getElementById('khqrOrderRef').textContent = 'Generation failed';
        });
    }

    function retryGenerate() {
        generateKhqr();
    }

    /* ─────────────────────────────────────────────
       KHQR — EXPIRY
    ───────────────────────────────────────────── */
    function scheduleExpiryWarning() {
        clearTimeout(expiryTimer);
        if (!khqrExpiresAt) return;

        const warnAt   = khqrExpiresAt.getTime() - Date.now() - (EXPIRY_WARN_SECS * 1000);
        const expireIn = khqrExpiresAt.getTime() - Date.now();

        if (warnAt <= 0) {
            showExpiryWarn();
        } else {
            expiryTimer = setTimeout(showExpiryWarn, warnAt);
        }

        setTimeout(() => {
            if (document.getElementById('khqrBackdrop').classList.contains('open')) {
                generateKhqr();
            }
        }, expireIn);
    }

    function showExpiryWarn() {
        document.getElementById('khqrExpiryWarn').style.display  = 'flex';
        document.getElementById('khqrExpiryWarnText').textContent = 'QR expires soon — regenerating…';
    }

    /* ─────────────────────────────────────────────
       KHQR — POLLING
    ───────────────────────────────────────────── */
    function startPolling() {
        pollCount = 0;
        pollTimer = setInterval(pollPayment, POLL_INTERVAL_MS);
    }

    function stopPolling() {
        clearInterval(pollTimer);
        pollTimer = null;
    }

    function pollPayment() {
        if (!khqrMd5) return;
        pollCount++;

        const remaining = Math.max(0, Math.ceil((khqrExpiresAt - Date.now()) / 1000));
        document.getElementById('khqrPollInfo').textContent =
            remaining > 0 ? `QR expires in ${remaining}s · check #${pollCount}` : '';

        if (pollCount >= POLL_MAX_CYCLES) {
            stopPolling();
            setDotState('grey');
            document.getElementById('khqrStatusText').textContent = 'Session timed out. Please retry.';
            document.getElementById('khqrPaidBtn').disabled = true;
            return;
        }

        fetch('{{ route("pos.khqr.check") }}', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ md5: khqrMd5 }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.paid) {
                stopPolling();
                handlePaymentSuccess();
            }
        })
        .catch(() => { /* silent — keep polling */ });
    }

    /* ─────────────────────────────────────────────
       KHQR — MANUAL VERIFY
    ───────────────────────────────────────────── */
    function confirmKhqrPaid() {
        if (!khqrMd5) return;

        document.getElementById('khqrPaidBtn').disabled       = true;
        document.getElementById('khqrStatusText').textContent = 'Verifying…';
        setDotState('amber');

        fetch('{{ route("pos.khqr.check") }}', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ md5: khqrMd5 }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.paid) {
                stopPolling();
                handlePaymentSuccess();
            } else {
                setDotState('waiting');
                document.getElementById('khqrStatusText').textContent = 'Not confirmed yet. Try again or wait.';
                document.getElementById('khqrPaidBtn').disabled = false;
            }
        })
        .catch(() => {
            setDotState('waiting');
            document.getElementById('khqrStatusText').textContent = 'Check failed. Please retry.';
            document.getElementById('khqrPaidBtn').disabled = false;
        });
    }

    /* ─────────────────────────────────────────────
       KHQR — PAYMENT SUCCESS
    ───────────────────────────────────────────── */
    function handlePaymentSuccess() {
        stopPolling();
        setKhqrState('success');
        setDotState('green');

        document.getElementById('khqrSuccessSub').textContent =
            `Paid $${currentTotal.toFixed(2)} · Ref: ${khqrOrderRef}`;

        document.getElementById('posItemsInput').value = JSON.stringify(
            Object.keys(cart).map(key => ({
                id:       cart[key].productId,
                qty:      cart[key].qty,
                price:    cart[key].price,
                discount: cart[key].discount,
                sugar:    cart[key].sugar,
            }))
        );
        document.getElementById('posTotalInput').value     = currentTotal.toFixed(2);
        document.getElementById('posPaymentMethod').value  = 'khqr';

        broadcastSuccess(currentTotal);

        setTimeout(() => document.getElementById('posForm').submit(), 1800);
    }

    /* ─────────────────────────────────────────────
       BACKDROP CLICK HANDLERS
    ───────────────────────────────────────────── */
    document.getElementById('removeModal').addEventListener('click', function(e) {
        if (e.target === this) closeRemoveModal();
    });
    document.getElementById('clearModal').addEventListener('click', function(e) {
        if (e.target === this) closeClearModal();
    });
    document.getElementById('addToCartModal').addEventListener('click', function(e) {
        if (e.target === this) closeAddToCartModal();
    });
    document.getElementById('khqrBackdrop').addEventListener('click', function(e) {
        if (e.target === this) closeKhqrModal();
    });

    /* ─────────────────────────────────────────────
       HIDE FAB WHEN CART IS EMPTY
    ───────────────────────────────────────────── */
    // FAB is always visible on mobile even when empty, but disabled
    // Optionally show/hide based on breakpoint resize
    window.addEventListener('resize', function() {
        // Sync drawer state if switching from mobile back to desktop
        if (!isMobileTablet()) {
            closeCartDrawer();
        }
    });
    </script>

@endsection
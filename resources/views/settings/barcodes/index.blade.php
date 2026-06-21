@extends('layouts.master')

@section('pageTitle')
    Barcode Generator
@endsection

@section('headerBlock')
<link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=Syne:wght@400;600;800&display=swap" rel="stylesheet">
<style>
    :root {
        --bg:        #0f1117;
        --surface:   #181c27;
        --border:    #252a38;
        --accent:    #00e5a0;
        --accent2:   #0066ff;
        --text:      #e8eaf2;
        --muted:     #6b7191;
        --white:     #ffffff;
        --radius:    12px;
        --mono:      'IBM Plex Mono', monospace;
        --sans:      'Syne', sans-serif;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body { background: var(--bg); color: var(--text); font-family: var(--sans); }

    .bc-page { padding: 36px 32px 60px; max-width: 1200px; margin: 0 auto; }

    .bc-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 28px; flex-wrap: wrap; gap: 14px;
    }

    .bc-title { display: flex; align-items: center; gap: 12px; }

    .bc-title-icon {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, var(--bg), var(--bg));
        border-radius: 5px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }

    .bc-title h2 {
        font-family: var(--sans); font-weight: 800;
        font-size: 1.6rem; letter-spacing: -0.5px; color: var(--bg);
    }

    .bc-title span {
        font-family: var(--mono); font-size: 0.7rem; color: var(--accent);
        display: block; margin-top: 2px; letter-spacing: 2px; text-transform: uppercase;
    }

    .btn-print-all {
        display: flex; align-items: center; gap: 8px;
        background: var(--accent); color: #000;
        font-family: var(--sans); font-weight: 600; font-size: 0.85rem;
        padding: 10px 22px; border: none; border-radius: 50px;
        cursor: pointer; transition: opacity .2s, transform .15s;
    }
    .btn-print-all:hover { opacity: .85; transform: translateY(-1px); }

    .bc-search { margin-bottom: 28px; }
    .bc-search form { display: flex; gap: 10px; }

    .bc-search-input {
        flex: 1; background: var(--surface); border: 1px solid var(--border);
        color: var(--text); font-family: var(--mono); font-size: 0.875rem;
        padding: 11px 20px; border-radius: 50px; outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .bc-search-input::placeholder { color: var(--muted); }
    .bc-search-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0,229,160,.12);
    }

    .btn-search {
        background: var(--accent2); color: var(--white);
        font-family: var(--sans); font-weight: 600; font-size: 0.85rem;
        padding: 11px 24px; border: none; border-radius: 50px;
        cursor: pointer; transition: opacity .2s;
    }
    .btn-search:hover { opacity: .85; }

    .bc-stats {
        display: flex; align-items: center; gap: 6px;
        font-family: var(--mono); font-size: 0.75rem;
        color: var(--muted); margin-bottom: 18px;
    }
    .bc-stats strong { color: var(--accent); }

    .bc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 18px;
    }

    /* ── Screen card ── */
    .bc-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 20px;
        display: flex; flex-direction: column; gap: 14px;
        transition: border-color .2s, box-shadow .2s, transform .2s;
        animation: fadeUp .4s ease both;
    }
    .bc-card:hover {
        border-color: rgba(0,229,160,.35);
        box-shadow: 0 8px 32px rgba(0,0,0,.35);
        transform: translateY(-3px);
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .bc-card:nth-child(1) { animation-delay: .04s; }
    .bc-card:nth-child(2) { animation-delay: .08s; }
    .bc-card:nth-child(3) { animation-delay: .12s; }
    .bc-card:nth-child(4) { animation-delay: .16s; }
    .bc-card:nth-child(5) { animation-delay: .20s; }
    .bc-card:nth-child(6) { animation-delay: .24s; }
    .bc-card:nth-child(7) { animation-delay: .28s; }
    .bc-card:nth-child(8) { animation-delay: .32s; }

    .bc-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }

    .bc-product-name {
        font-family: var(--sans); font-weight: 600; font-size: 0.92rem;
        color: var(--white); line-height: 1.3; flex: 1;
    }

    .bc-badge {
        font-family: var(--mono); font-size: 0.68rem;
        background: rgba(0,229,160,.12); color: var(--accent);
        border: 1px solid rgba(0,229,160,.25);
        padding: 2px 8px; border-radius: 50px;
        white-space: nowrap; flex-shrink: 0;
    }

    .bc-visual {
        background: #fff; border-radius: 8px; padding: 12px 10px 8px;
        display: flex; flex-direction: column; align-items: center; gap: 4px;
    }

    .bc-visual img {
        width: 100%; height: 60px;
        object-fit: contain; display: block;
        image-rendering: pixelated;
    }

    .bc-sku-label {
        font-family: var(--mono); font-size: 0.68rem;
        color: #444; letter-spacing: 1.5px; text-align: center;
    }

    .bc-card-foot { display: flex; justify-content: space-between; align-items: center; }

    .bc-price {
        font-family: var(--mono); font-weight: 600;
        font-size: 0.95rem; color: var(--accent);
    }

    .btn-print-single {
        display: flex; align-items: center; gap: 6px;
        background: transparent; border: 1px solid var(--border);
        color: var(--muted); font-family: var(--sans);
        font-size: 0.78rem; font-weight: 600;
        padding: 6px 14px; border-radius: 50px;
        cursor: pointer; transition: all .2s;
    }
    .btn-print-single:hover {
        border-color: var(--accent2); color: var(--white);
        background: rgba(0,102,255,.1);
    }

    .bc-empty {
        grid-column: 1 / -1; text-align: center;
        padding: 60px 20px; color: var(--muted);
        font-family: var(--mono); font-size: 0.85rem;
    }
    .bc-empty-icon { font-size: 3rem; margin-bottom: 16px; opacity: .35; }

    /* ════════════════════════════════════════════
       PRINT ALL — label style, barcode + SKU only
       ════════════════════════════════════════════ */
    @media print {
        /* Hide everything except the grid */
        body * { visibility: hidden; }
        .bc-grid, .bc-grid * { visibility: visible; }

        body { background: white !important; }

        .bc-page {
            position: absolute; top: 0; left: 0;
            padding: 10px; width: 100%;
        }

        /* Hide all UI except barcode area */
        .bc-card-head,
        .bc-card-foot,
        .btn-print-single,
        .scan-overlay { display: none !important; }

        .bc-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            max-width: 320px;
            margin: 0 auto;
        }

        /* Each card = clean label box */
        .bc-card {
            background: white !important;
            border: 1px solid #ccc !important;
            border-radius: 6px !important;
            box-shadow: none !important;
            transform: none !important;
            animation: none !important;
            padding: 10px 12px 8px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 0 !important;
            page-break-inside: avoid;
        }

        /* Barcode image — full width */
        .bc-visual {
            background: white !important;
            border-radius: 0 !important;
            padding: 0 !important;
            width: 100%;
        }

        .bc-visual img {
            width: 100% !important;
            height: 70px !important;
            object-fit: fill !important;
            image-rendering: pixelated !important;
        }

        /* SKU text below barcode */
        .bc-sku-label {
            font-family: 'IBM Plex Mono', monospace !important;
            font-size: 9px !important;
            letter-spacing: 1.5px !important;
            color: #111 !important;
            margin-top: 4px !important;
            text-align: center !important;
            visibility: visible !important;
        }
    }

    /* ── Scan button ── */
    .btn-scan {
        display: flex; align-items: center; gap: 8px;
        background: var(--surface); color: var(--accent);
        font-family: var(--sans); font-weight: 600; font-size: 0.85rem;
        padding: 10px 22px; border: 1px solid rgba(0,229,160,.35);
        border-radius: 50px; cursor: pointer; transition: all .2s;
    }
    .btn-scan:hover { background: rgba(0,229,160,.1); transform: translateY(-1px); }

    /* ── Scan modal ── */
    .scan-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,.75); backdrop-filter: blur(6px);
        z-index: 1000; align-items: center; justify-content: center;
    }
    .scan-overlay.open { display: flex; }

    .scan-modal {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 16px; padding: 32px; width: 100%; max-width: 420px;
        position: relative; animation: popIn .25s ease;
    }

    @keyframes popIn {
        from { opacity: 0; transform: scale(.94); }
        to   { opacity: 1; transform: scale(1); }
    }

    .scan-modal-close {
        position: absolute; top: 16px; right: 16px;
        background: none; border: none; color: var(--muted);
        font-size: 1.2rem; cursor: pointer; line-height: 1; transition: color .15s;
    }
    .scan-modal-close:hover { color: var(--white); }

    .scan-modal h3 {
        font-family: var(--sans); font-weight: 800;
        font-size: 1.2rem; color: var(--white); margin-bottom: 6px;
    }

    .scan-input-wrap { position: relative; margin-bottom: 16px; }

    .scan-overlay.has-result .scan-input-wrap {
        position: absolute; opacity: 0; pointer-events: none;
        height: 0; overflow: hidden; margin: 0;
    }

    .scan-input {
        width: 100%; background: var(--bg); border: 1px solid var(--border);
        color: var(--text); font-family: var(--mono); font-size: 1.05rem;
        letter-spacing: 3px; padding: 14px 48px 14px 18px;
        border-radius: 10px; outline: none;
        transition: border-color .2s, box-shadow .2s;
        caret-color: var(--accent);
    }
    .scan-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0,229,160,.12);
    }

    .scan-spinner {
        position: absolute; right: 14px; top: 50%;
        transform: translateY(-50%);
        width: 18px; height: 18px;
        border: 2px solid var(--border); border-top-color: var(--accent);
        border-radius: 50%; animation: spin .7s linear infinite; display: none;
    }
    .scan-spinner.active { display: block; }
    @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }

    .scan-result {
        display: none; background: var(--bg);
        border: 1px solid rgba(0,229,160,.25);
        border-radius: 12px; overflow: hidden; animation: fadeUp .3s ease;
    }
    .scan-result.show { display: block; }

    .scan-result-price-hero {
        padding: 28px 18px 24px; display: flex;
        flex-direction: column; align-items: center; gap: 6px;
        background: rgba(0,229,160,.06);
    }

    .scan-price-label {
        font-family: var(--mono); font-size: 0.62rem;
        letter-spacing: 3px; color: var(--muted); text-transform: uppercase;
    }

    .scan-price-big {
        font-family: var(--mono); font-weight: 600; font-size: 3.2rem;
        color: var(--accent); letter-spacing: -2px; line-height: 1; transition: color .4s;
    }

    .scan-result-name-row {
        padding: 12px 18px; border-top: 1px solid var(--border); text-align: center;
    }

    .scan-result-name {
        font-family: var(--sans); font-weight: 600;
        font-size: 0.88rem; color: var(--muted); line-height: 1.3;
    }

    .scan-error {
        display: none; margin-top: 14px;
        background: rgba(255,80,80,.1); border: 1px solid rgba(255,80,80,.25);
        border-radius: 8px; padding: 12px 16px;
        font-family: var(--mono); font-size: 0.8rem; color: #ff8080;
    }
    .scan-error.show { display: block; }

    .scan-history { margin-top: 20px; }
    .scan-history-title {
        font-family: var(--mono); font-size: 0.68rem; color: var(--muted);
        text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;
    }
    .scan-history-list {
        display: flex; flex-direction: column; gap: 6px;
        max-height: 140px; overflow-y: auto;
    }
    .scan-history-item {
        display: flex; justify-content: space-between; align-items: center;
        background: var(--bg); border: 1px solid var(--border);
        border-radius: 8px; padding: 8px 12px;
        font-family: var(--mono); font-size: 0.78rem;
        cursor: pointer; transition: border-color .15s;
    }
    .scan-history-item:hover { border-color: var(--accent2); }
    .scan-history-item .h-name  { color: var(--text); flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .scan-history-item .h-price { color: var(--accent); font-weight: 600; margin-left: 10px; }
</style>
@endsection

@section('content')
<div class="bc-page">

    {{-- Header --}}
    <div class="bc-header">
        <div class="bc-title">
            <div class="bc-title-icon">
                <i class="fa-solid fa-barcode"></i>
            </div>
            <div>
                <h2>Barcode Generator</h2>
                <span>Product Catalogue · SKU Barcodes</span>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <button class="btn-scan" onclick="openScan()">
                <i class="fas fa-barcode"></i> Scan Barcode
            </button>
            <button class="btn-print-all" onclick="window.print()">
                <i class="fas fa-print"></i> Print All
            </button>
        </div>
    </div>

    {{-- Search --}}
    <div class="bc-search">
        <form method="GET" action="{{ route('barcodes.index') }}">
            <input
                class="bc-search-input"
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by Product Name or SKU…"
                autocomplete="off"
            >
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>

    {{-- Stats --}}
    <div class="bc-stats">
        <strong>{{ $products->count() }}</strong>&nbsp;product{{ $products->count() !== 1 ? 's' : '' }} found
        @if(request('search'))
            &nbsp;·&nbsp;Filtered by: <strong>"{{ request('search') }}"</strong>
            &nbsp;·&nbsp;<a href="{{ route('barcodes.index') }}" style="color:var(--muted);text-decoration:underline;">Clear</a>
        @endif
    </div>

    {{-- Grid --}}
    <div class="bc-grid">
        @forelse ($products as $product)
            @php
                $sku    = $product->sku ?? (string)$product->id;
                $b64    = DNS1D::getBarcodePNG($sku, 'C128', 2, 60, [0,0,0], true);
                $imgSrc = 'data:image/png;base64,' . $b64;
            @endphp

            <div class="bc-card" id="card-{{ $product->id }}">
                <div class="bc-card-head">
                    <div class="bc-product-name">{{ $product->name }}</div>
                    <span class="bc-badge">{{ $sku }}</span>
                </div>

                <div class="bc-visual">
                    <img src="{{ $imgSrc }}"
                         alt="Barcode {{ $sku }}"
                         data-barcode-src="{{ $imgSrc }}"
                         data-sku="{{ $sku }}"
                         data-name="{{ $product->name }}"
                         data-price="{{ number_format($product->price, 2) }}">
                </div>

                <div class="bc-card-foot">
                    <span class="bc-price">${{ number_format($product->price, 2) }}</span>
                    <button class="btn-print-single" onclick="printCard('card-{{ $product->id }}')">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>

        @empty
            <div class="bc-empty">
                <div class="bc-empty-icon">⬚</div>
                <p>No products found{{ request('search') ? ' matching "' . request('search') . '"' : '' }}.</p>
            </div>
        @endforelse
    </div>

</div>

{{-- ── Scan Modal ── --}}
<div class="scan-overlay" role="dialog" aria-modal="true" aria-label="Barcode Scanner">
    <div class="scan-modal">
        <button class="scan-modal-close" onclick="closeScan()" title="Close">&times;</button>

        <h3><i class="fas fa-barcode"></i> Scan to Check Price</h3>

        <div class="scan-input-wrap">
            <input id="scanInput" class="scan-input" type="text"
                placeholder="Waiting for scan…" autocomplete="off" spellcheck="false">
            <div id="scanSpinner" class="scan-spinner"></div>
        </div>

        <div id="scanResult" class="scan-result">
            <div class="scan-result-price-hero">
                <span class="scan-price-label">PRICE</span>
                <span class="scan-price-big" id="resPrice">—</span>
            </div>
            <div class="scan-result-name-row">
                <div class="scan-result-name" id="resName">—</div>
            </div>
        </div>

        <div id="scanError" class="scan-error"></div>

        <div id="historyWrap" class="scan-history" style="display:none;">
            <div class="scan-history-title">Recent Scans</div>
            <div id="historyList" class="scan-history-list"></div>
        </div>
    </div>
</div>

<script>
    const PRODUCTS = @json($productMap);
    let scanHistory = [], debounceTimer = null;

    function openScan() {
        document.querySelector('.scan-overlay').classList.add('open');
        setTimeout(() => document.getElementById('scanInput').focus(), 100);
    }

    function closeScan() {
        document.querySelector('.scan-overlay').classList.remove('open');
        clearScanResult();
    }

    function clearScanResult() {
        document.getElementById('scanInput').value = '';
        document.getElementById('scanResult').classList.remove('show');
        document.getElementById('scanError').classList.remove('show');
        document.getElementById('scanSpinner').classList.remove('active');
        document.querySelector('.scan-overlay').classList.remove('has-result');
    }

    function handleScanInput(e) {
        const val = e.target.value.trim();
        clearTimeout(debounceTimer);
        if (!val) {
            document.getElementById('scanResult').classList.remove('show');
            document.getElementById('scanError').classList.remove('show');
            return;
        }
        document.getElementById('scanSpinner').classList.add('active');
        if (e.key === 'Enter' || e.type === 'input') {
            debounceTimer = setTimeout(() => lookupSKU(val), e.key === 'Enter' ? 0 : 350);
        }
    }

    function lookupSKU(sku) {
        document.getElementById('scanSpinner').classList.remove('active');
        const product = PRODUCTS[sku];
        if (product) {
            showResult(product);
        } else {
            document.getElementById('scanResult').classList.remove('show');
            const err = document.getElementById('scanError');
            err.textContent = `No product found for SKU: "${sku}"`;
            err.classList.add('show');
        }
    }

    function showResult(product) {
        document.getElementById('scanError').classList.remove('show');
        document.getElementById('resName').textContent  = product.name;
        document.getElementById('resPrice').textContent = '$' + product.price;
        document.querySelector('.scan-overlay').classList.add('has-result');
        document.getElementById('scanResult').classList.add('show');
        document.getElementById('scanInput').focus();

        scanHistory = scanHistory.filter(h => h.sku !== product.sku);
        scanHistory.unshift(product);
        if (scanHistory.length > 6) scanHistory.pop();
        renderHistory();

        const priceEl = document.getElementById('resPrice');
        priceEl.style.transition = 'none';
        priceEl.style.color = '#fff';
        setTimeout(() => { priceEl.style.transition = 'color .4s'; priceEl.style.color = ''; }, 60);
    }

    function renderHistory() {
        const list = document.getElementById('historyList');
        const wrap = document.getElementById('historyWrap');
        if (!scanHistory.length) { wrap.style.display = 'none'; return; }
        wrap.style.display = 'block';
        list.innerHTML = scanHistory.map(h => `
            <div class="scan-history-item" onclick="reScan('${h.sku}')">
                <span class="h-name">${h.name}</span>
                <span class="h-price">$${h.price}</span>
            </div>`).join('');
    }

    function reScan(sku) {
        document.getElementById('scanInput').value = sku;
        lookupSKU(sku);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('.scan-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeScan();
        });
        document.getElementById('scanInput').addEventListener('keydown', handleScanInput);
        document.getElementById('scanInput').addEventListener('input',   handleScanInput);
    });

    /* Print single card — barcode + SKU label only */
    function printCard(cardId) {
        const card  = document.getElementById(cardId);
        if (!card) return;

        const img    = card.querySelector('img[data-barcode-src]');
        const imgSrc = img ? img.getAttribute('data-barcode-src') : '';
        const sku    = img ? img.getAttribute('data-sku')   : '';

        const html = `<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Print</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&display=swap');
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background: white;
    display: flex; align-items: center; justify-content: center;
    min-height: 100vh; padding: 16px;
    font-family: 'IBM Plex Mono', monospace;
  }
  .label {
    background: white; border: 1px solid #ddd; border-radius: 6px;
    padding: 14px 18px 12px;
    display: inline-flex; flex-direction: column; align-items: center;
    min-width: 220px; max-width: 320px;
  }
  .label img {
    width: 100%; height: 80px;
    object-fit: fill; display: block;
    image-rendering: pixelated;
  }
  .label-sku {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px; letter-spacing: 1.5px;
    color: #111; margin-top: 5px; text-align: center;
  }
  @media print {
    body { min-height: unset; padding: 8px; }
    .label { border: none; }
  }
</style>
</head>
<body>
  <div class="label">
    <img src="${imgSrc}" alt="${sku}">
  </div>
  <script>
    document.fonts.ready.then(function() {
      setTimeout(function() { window.print(); window.close(); }, 300);
    });
  <\/script>
</body>
</html>`;

        const iframe = document.createElement('iframe');
        iframe.style.cssText = 'position:fixed;left:-9999px;width:0;height:0;border:none;';
        document.body.appendChild(iframe);
        iframe.contentDocument.open();
        iframe.contentDocument.write(html);
        iframe.contentDocument.close();
        iframe.contentWindow.onafterprint = () => iframe.remove();
    }
</script>
@endsection
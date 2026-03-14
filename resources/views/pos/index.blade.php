@extends('layouts.pos')

@section('pageTitle')
    Point of Sale
@endsection

@section('headerBlock')
    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        body, .pos-wrapper, .pos-products-panel, .pos-cart-panel {
            font-family: 'DM Sans', sans-serif;
        }

        .pos-wrapper {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start;
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
        .pos-search-wrap {
            position: relative;
            margin-bottom: 16px;
        }

        .pos-search-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 14px;
            pointer-events: none;
        }

        .pos-search-wrap input {
            width: 80%;
            padding: 10px 14px 10px 38px;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
            font-family: 'DM Sans', sans-serif;
        }

        .pos-search-wrap input:focus { border-color: #111; }

        /* ── CATEGORY GROUP ── */
        .category-group {
            margin-bottom: 22px;
        }

        .category-group.hidden { display: none; }

       .category-heading {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 10px 2px;
            letter-spacing: -0.03em;
            text-transform: uppercase;
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .category-heading::after {
            content: '';
            flex: 1;
            height: 1px;
            border-top: 2px dotted #d1d5db;
        }

        /* ── PRODUCT GRID ── */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        /* ── PRODUCT CARD ── */
        .product-card {
            position: relative;
            border: 1px solid #ebebeb;
            border-radius: 12px;
            cursor: pointer;
            background: #fff;
            overflow: hidden;
            transition: border-color 0.2s, box-shadow 0.25s, transform 0.2s;
            display: flex;
            flex-direction: column;
            animation: cardFadeUp 0.3s ease both;
        }

        .product-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 12px;
            background: rgba(0,0,0,0);
            transition: background 0.2s;
            pointer-events: none;
        }

        .product-card:hover {
            border-color: #a5b4fc;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            transform: translateY(-3px);
        }

        .product-card:active { transform: translateY(0px) scale(0.98); }

        .product-card.out-of-stock {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Image area */
        .product-card-img-wrap {
            position: relative;
            width: 100%;
            height: 200px;
            background: #f7f7f7;
            overflow: hidden;
        }

        .product-card-img {
            width: 100%;
            height:100%;
            object-fit: cover;
            display: block;
            transition: transform 0.35s ease;
        }

        .product-card:hover .product-card-img { transform: scale(1.04); }

        .product-card-img-placeholder {
            width: 100%;
            height: 100%;
            background: #f7f7f7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d4d4d4;
            font-size: 22px;
        }

        /* Add badge on hover */
        .product-card-add-badge {
            position: absolute;
            bottom: 8px;
            right: 8px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #111;
            color: #fff;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0.7);
            transition: opacity 0.2s, transform 0.2s;
            font-weight: 300;
            line-height: 1;
        }

        .product-card:hover .product-card-add-badge {
            opacity: 1;
            transform: scale(1);
        }

        /* Card body */
        .product-card-body {
            padding: 10px 11px 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .product-card-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #111;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        .product-card-brand {
            font-size: 10.5px;
            color: #aaa;
            font-weight: 400;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .product-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
        }

        .product-card-price {
            font-family: 'DM Mono', monospace;
            font-size: 13px;
            font-weight: 500;
            color: #111;
            letter-spacing: -0.02em;
        }

        .product-card-stock {
            font-size: 10px;
            font-weight: 500;
            color: #bbb;
            background: #f5f5f5;
            padding: 2px 7px;
            border-radius: 20px;
            letter-spacing: 0.01em;
        }

        .product-card-stock.low {
            color: #ef4444;
            background: #fef2f2;
        }

        /* ── CART PANEL ── */
        .pos-cart-panel {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 20px;
            position: sticky;
            top: 20px;
        }

        .pos-cart-panel h3 {
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 14px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-items {
            min-height: 200px;
            max-height: calc(100vh - 420px);
            overflow-y: auto;
            margin-bottom: 16px;
            padding-right: 5px;
        }

        .cart-items::-webkit-scrollbar { width: 5px; }
        .cart-items::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }

        .cart-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 120px;
            color: #d1d5db;
            font-size: 13px;
            gap: 8px;
        }

        .cart-empty i { font-size: 28px; }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 8px;
            border-radius: 10px;
            margin-bottom: 3px;
            border: 1px solid transparent;
            transition: background 0.15s, border-color 0.15s;
        }

        .cart-item:hover {
            background: #fafafa;
            border-color: #ebebeb;
        }

        .cart-item-thumb {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            object-fit: cover;
            background: #f3f4f6;
            flex-shrink: 0;
            border: 1px solid #ebebeb;
        }

        .cart-item-thumb-placeholder {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background: #f7f7f7;
            border: 1px solid #ebebeb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d4d4d4;
            font-size: 14px;
            flex-shrink: 0;
        }

        .cart-item-info { flex: 1; min-width: 0; }

        .cart-item-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #111;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        .cart-item-price {
            font-size: 11px;
            color: #aaa;
            font-weight: 400;
            margin-top: 1px;
        }

        /* Category badge in cart */
        .cart-item-category {
            display: inline-block;
            font-size: 10px;
            font-weight: 500;
            color: #6366f1;
            background: #eef2ff;
            padding: 1px 7px;
            border-radius: 20px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .cart-item-qty { display: flex; align-items: center; gap: 4px; }

        .qty-btn {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            transition: background 0.15s;
        }

        .qty-btn:hover { background: #111; border-color: #111; color: #fff; }

        .qty-value {
            font-size: 12px;
            font-weight: 600;
            min-width: 16px;
            text-align: center;
            color: #1f2937;
        }

        .cart-item-total {
            font-size: 12px;
            font-weight: 700;
            color: #111;
            min-width: 52px;
            text-align: right;
            font-family: 'DM Mono', monospace;
        }

        .cart-item-remove {
            background: none;
            border: none;
            color: #d1d5db;
            cursor: pointer;
            font-size: 12px;
            padding: 2px;
            transition: color 0.15s;
        }

        .cart-item-remove:hover { color: #ef4444; }

        .cart-summary { border-top: 2px solid #f3f4f6; padding-top: 12px; }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .summary-row.total {
            font-size: 14px;
            font-weight: 700;
            color: #1f2937;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #e5e7eb;
        }

        .btn-checkout {
            width: 100%;
            padding: 11px;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity 0.2s, transform 0.15s;
            font-family: 'DM Sans', sans-serif;
            letter-spacing: -0.01em;
        }

        .btn-checkout:hover { opacity: 0.85; transform: translateY(-1px); }

        .btn-checkout:disabled {
            background: #d1d5db;
            cursor: not-allowed;
            transform: none;
        }

        .btn-clear-cart {
            width: 100%;
            padding: 8px;
            background: #fff;
            color: #ef4444;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 7px;
            transition: background 0.15s;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-clear-cart:hover { background: #fef2f2; }

        .no-results {
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
            padding: 40px 0;
        }

        /* Success modal */
        .checkout-success-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 99998;
            justify-content: center;
            align-items: center;
        }

        .checkout-success-box {
            background: #fff;
            border-radius: 16px;
            padding: 40px 32px;
            text-align: center;
            max-width: 360px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .checkout-success-box i { font-size: 48px; color: #10b981; margin-bottom: 16px; }

        .checkout-success-box h3 {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .checkout-success-box p { font-size: 14px; color: #6b7280; margin-bottom: 20px; }

        .checkout-success-box button {
            padding: 10px 28px;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
        }

        /* Stagger animation */
        @keyframes cardFadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .product-card:nth-child(1)  { animation-delay: 0.03s; }
        .product-card:nth-child(2)  { animation-delay: 0.06s; }
        .product-card:nth-child(3)  { animation-delay: 0.09s; }
        .product-card:nth-child(4)  { animation-delay: 0.12s; }
        .product-card:nth-child(5)  { animation-delay: 0.15s; }
        .product-card:nth-child(6)  { animation-delay: 0.18s; }
        .product-card:nth-child(7)  { animation-delay: 0.21s; }
        .product-card:nth-child(8)  { animation-delay: 0.24s; }
        .product-card:nth-child(n+9){ animation-delay: 0.27s; }
    </style>
@endsection

@section('content')

    {{-- Success Modal --}}
    <div class="checkout-success-modal" id="checkoutSuccessModal">
        <div class="checkout-success-box">
            <i class="fas fa-check-circle"></i>
            <h3>Order Placed!</h3>
            <p id="checkoutSummaryText">Your order has been successfully processed.</p>
            <button onclick="closeSuccessModal()">Done</button>
        </div>
    </div>

    <div class="content-section">
        <div class="pos-wrapper">

            {{-- LEFT: Product Panel --}}
            <div class="pos-products-panel">
                @php
                    $grouped = $products
                        ->filter(fn($p) => $p->add_to_pos == 1)
                        ->groupBy(fn($p) => $p->category->name ?? 'N/A');
                @endphp

                {{-- Search --}}
               <div style="display:flex;gap:10px;margin-bottom:16px;">
                    <div class="pos-search-wrap" style="margin-bottom:0;flex:1;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="posSearch" placeholder="Search products by name or brand...">
                    </div>
                    <select id="categoryFilter" style="padding:10px 24px;border:1px solid #e5e7eb;border-radius:24px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;color:#374151;cursor:pointer;background:#fff;min-width:240px;">
                        <option value="">All Categories</option>
                        @foreach($grouped as $categoryName => $items)
                            <option value="{{ strtolower($categoryName) }}">{{ $categoryName }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Products grouped by category --}}
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
                                                <div class="product-card-img-placeholder" style="display:none;">
                                                    <i class="fas fa-box-open"></i>
                                                </div>
                                            @else
                                                <div class="product-card-img-placeholder">
                                                    <i class="fas fa-box-open"></i>
                                                </div>
                                            @endif
                                            <div class="product-card-add-badge">+</div>
                                        </div>

                                        <div class="product-card-body">
                                            <div class="product-card-name">{{ $product->name }}</div>
                                            <div class="product-card-brand">{{ $product->category->name ?? 'N/A' }}</div>
                                            <div class="product-card-footer">
                                                <div class="product-card-price">${{ number_format($product->price, 2) }}</div>
                                                <div class="product-card-stock {{ $product->stock <= 5 ? 'low' : '' }}">
                                                    {{ $product->stock }}
                                                </div>
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

            {{-- RIGHT: Cart Panel --}}
            <div class="pos-cart-panel">
                <h3>
                    <i class="fas fa-shopping-cart"></i> Cart
                    <span id="cartCount" style="background:#111;color:#fff;border-radius:999px;font-size:11px;padding:2px 8px;margin-left:4px;">0</span>
                </h3>

                <div class="cart-items" id="cartItems">
                    <div class="cart-empty">
                        <i class="fas fa-shopping-cart"></i>
                        <span>No items added yet</span>
                    </div>
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="summary-row" id="discountRow" style="display:none;">
                        <span style="color:#10b981;display:flex;align-items:center;gap:4px;">
                            <i class="fas fa-tag" style="font-size:10px;"></i> Discount
                        </span>
                        <span id="discountAmount" style="color:#10b981;">-$0.00</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="grandTotal">$0.00</span>
                    </div>
                </div>

                <form id="posForm" action="{{ route('pos.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="items" id="posItemsInput">
                    <input type="hidden" name="total" id="posTotalInput">
                    <button type="submit" class="btn-checkout" id="checkoutBtn" disabled>
                        <i class="fas fa-check-circle"></i> Checkout
                    </button>
                </form>

                <button class="btn-clear-cart" onclick="clearCart()">
                    <i class="fas fa-trash"></i> Clear Cart
                </button>
            </div>

        </div>
    </div>

    <script>
        const overlay     = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        function showLoading(msg) {
            loadingText.textContent = msg || 'Loading...';
            overlay.style.display = 'flex';
        }

        let cart = {};

        /* ── ADD TO CART ── */
        function addToCart(card) {
            const id    = card.dataset.id;
            const stock = parseInt(card.dataset.stock);
            if (cart[id]) {
                if (cart[id].qty >= stock) return;
                cart[id].qty++;
            } else {
                cart[id] = {
                    name:     card.dataset.name,
                    brand:    card.dataset.brand,
                    price:    parseFloat(card.dataset.price),
                    stock:    stock,
                    qty:      1,
                    image:    card.dataset.image || '',
                    discount: "0"
                };
            }
            renderCart();
        }

        /* ── QTY ── */
        function changeQty(id, delta) {
            if (!cart[id]) return;
            cart[id].qty += delta;
            if (cart[id].qty <= 0) delete cart[id];
            else if (cart[id].qty > cart[id].stock) cart[id].qty = cart[id].stock;
            renderCart();
        }

        /* ── REMOVE / CLEAR ── */
        function removeItem(id) { delete cart[id]; renderCart(); }
        function clearCart()    { cart = {}; renderCart(); }

        /* ── DISCOUNT ── */
        function setDiscount(id, value) {
            if (!cart[id]) return;
            cart[id].discount = value;
            renderCart();
        }

        function calculateItemTotal(item) {
            let total = item.price * item.qty;
            if (!item.discount || item.discount === "0") return total;
            if (item.discount.includes("%")) {
                total -= total * (parseFloat(item.discount) / 100);
            }
            if (item.discount.includes("$")) {
                total -= parseFloat(item.discount);
            }
            return total < 0 ? 0 : total;
        }

        /* ── RENDER CART ── */
        function renderCart() {
            const container   = document.getElementById('cartItems');
            const cartCount   = document.getElementById('cartCount');
            const subtotalEl  = document.getElementById('subtotal');
            const totalEl     = document.getElementById('grandTotal');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const ids = Object.keys(cart);

            if (ids.length === 0) {
                container.innerHTML   = '<div class="cart-empty"><i class="fas fa-shopping-cart"></i><span>No items added yet</span></div>';
                cartCount.textContent = '0';
                subtotalEl.textContent = '$0.00';
                totalEl.textContent    = '$0.00';
                document.getElementById('discountRow').style.display = 'none';
                checkoutBtn.disabled = true;
                return;
            }

            let subtotal = 0, total = 0, count = 0, html = '';

            ids.forEach(id => {
                const item = cart[id];
                subtotal += item.price * item.qty;
                const itemTotal = calculateItemTotal(item);
                total += itemTotal;
                count += item.qty;

                const thumbHtml = item.image
                    ? `<img class="cart-item-thumb" src="${item.image}" alt="${item.name}">`
                    : `<div class="cart-item-thumb-placeholder"><i class="fas fa-box-open"></i></div>`;

                html += `
                <div class="cart-item">
                    ${thumbHtml}
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <span class="cart-item-category">${item.brand}</span>
                        <div class="cart-item-price">$${item.price.toFixed(2)} each</div>
                        <select onchange="setDiscount('${id}', this.value)" style="font-size:11px;margin-top:4px;padding:2px 4px;font-family:inherit;">
                            <option value="0"   ${item.discount=="0"  ?"selected":""}>No Discount</option>
                            <option value="5%"  ${item.discount=="5%" ?"selected":""}>5%</option>
                            <option value="10%" ${item.discount=="10%"?"selected":""}>10%</option>
                            <option value="15%" ${item.discount=="15%"?"selected":""}>15%</option>
                            <option value="1$"  ${item.discount=="1$" ?"selected":""}>$1</option>
                            <option value="2$"  ${item.discount=="2$" ?"selected":""}>$2</option>
                            <option value="5$"  ${item.discount=="5$" ?"selected":""}>$5</option>
                        </select>
                    </div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" onclick="changeQty('${id}', -1)">−</button>
                        <span class="qty-value">${item.qty}</span>
                        <button class="qty-btn" onclick="changeQty('${id}', 1)">+</button>
                    </div>
                    <div class="cart-item-total">$${itemTotal.toFixed(2)}</div>
                    <button class="cart-item-remove" onclick="removeItem('${id}')"><i class="fas fa-times"></i></button>
                </div>`;
            });

            container.innerHTML    = html;
            cartCount.textContent  = count;
            subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
            totalEl.textContent    = `$${total.toFixed(2)}`;

            const discountRow = document.getElementById('discountRow');
            const discountEl  = document.getElementById('discountAmount');
            const savings = subtotal - total;
            if (savings > 0) {
                discountEl.textContent = `-$${savings.toFixed(2)}`;
                discountRow.style.display = 'flex';
            } else {
                discountRow.style.display = 'none';
            }

            checkoutBtn.disabled = false;

            document.getElementById('posItemsInput').value = JSON.stringify(
                ids.map(id => ({ id, qty: cart[id].qty, price: cart[id].price, discount: cart[id].discount }))
            );
            document.getElementById('posTotalInput').value = total.toFixed(2);
        }

        /* ── SEARCH (hides entire category group if no match) ── */
        document.getElementById('posSearch').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.category-group').forEach(group => {
                let visible = 0;
                group.querySelectorAll('.product-card').forEach(card => {
                    const match = card.dataset.name.toLowerCase().includes(q) ||
                                  card.dataset.brand.toLowerCase().includes(q);
                    card.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                group.classList.toggle('hidden', visible === 0);
            });
        });

        /* ── FORM SUBMIT ── */
        document.getElementById('posForm').addEventListener('submit', function () {
            showLoading('Processing order...');
        });

        /* ── SUCCESS MODAL ── */
        function closeSuccessModal() {
            document.getElementById('checkoutSuccessModal').style.display = 'none';
            clearCart();
        }

        @if(session('pos_success'))
            document.getElementById('checkoutSummaryText').textContent = '{{ session('pos_success') }}';
            document.getElementById('checkoutSuccessModal').style.display = 'flex';
        @endif
        function filterProducts() {
            const q   = document.getElementById('posSearch').value.toLowerCase();
            const cat = document.getElementById('categoryFilter').value.toLowerCase();

            document.querySelectorAll('.category-group').forEach(group => {
                const groupCat = group.dataset.category;
                if (cat && groupCat !== cat) {
                    group.classList.add('hidden');
                    return;
                }
                let visible = 0;
                group.querySelectorAll('.product-card').forEach(card => {
                    const match = card.dataset.name.toLowerCase().includes(q) ||
                                card.dataset.brand.toLowerCase().includes(q);
                    card.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                group.classList.toggle('hidden', visible === 0);
            });
        }

        document.getElementById('posSearch').addEventListener('input', filterProducts);
        document.getElementById('categoryFilter').addEventListener('change', filterProducts);
    </script>

@endsection
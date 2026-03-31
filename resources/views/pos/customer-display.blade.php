<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display — {{ env('BAKONG_MERCHANT_NAME', 'My Shop') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #0a0a0a;
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            user-select: none;
        }

        /* ── HEADER ── */
        .cd-header {
            background: #111;
            border-bottom: 1px solid #1d1d1d;
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .cd-logo { font-size: 20px; font-weight: 700; letter-spacing: -0.04em; color: #fff; }
        .cd-logo span { color: #ff4545; }
        .cd-clock-wrap { text-align: right; }
        .cd-time { font-family: 'DM Mono', monospace; font-size: 26px; font-weight: 500; color: #fff; letter-spacing: 0.04em; line-height: 1; }
        .cd-date { font-size: 11px; color: #ffffff; margin-top: 4px; }

        /* ── IDLE SCREEN ── */
        .cd-idle {
            flex: 1; display: flex; flex-direction: column; align-items: center;
            justify-content: center; gap: 20px; transition: opacity 0.5s;
        }
        .cd-idle-icon { width: 100px; height: 100px; border-radius: 50%; background: #161616; border: 1px solid #1d1d1d; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #ffffff; }
        .cd-idle-title { font-size: 24px; font-weight: 600; color: #ffffff; letter-spacing: -0.03em; }
        .cd-idle-sub   { font-size: 14px; color: #ffffff; }
        /* Slider Container */
        .cd-idle-slider {
            width: 1500px;
            height: 500px;
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #1d1d1d;
            background: #161616;
            margin-bottom: 30px;
        }

        /* Individual Slide Images */
        .idle-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            transform: scale(1.1); /* បង្កើត Effect Zoom បន្តិច */
        }

        /* Active Slide */
        .idle-slide.active {
            opacity: 1;
            transform: scale(1);
        }
        /* ── ACTIVE LAYOUT ── */
        .cd-active { flex: 1; display: none; flex-direction: column; overflow: hidden; }

        /* ── ITEM LIST ── */
        .cd-items { flex: 1; overflow-y: auto; padding: 24px 32px 16px; }
        .cd-items::-webkit-scrollbar { width: 4px; }
        .cd-items::-webkit-scrollbar-thumb { background: #1d1d1d; border-radius: 4px; }

        .cd-item { display: flex; align-items: center; gap: 16px; padding: 14px 0; border-bottom: 1px solid #141414; animation: slideIn 0.28s ease both; }
        .cd-item:last-child { border-bottom: none; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-12px); } to { opacity: 1; transform: translateX(0); } }

        .cd-item-thumb { width: 60px; height: 60px; border-radius: 12px; object-fit: contain; background: #161616; border: 1px solid #1d1d1d; padding: 6px; flex-shrink: 0; }
        .cd-item-thumb-ph { width: 60px; height: 60px; border-radius: 12px; background: #161616; border: 1px solid #1d1d1d; display: flex; align-items: center; justify-content: center; color: #2a2a2a; font-size: 22px; flex-shrink: 0; }
        .cd-item-body { flex: 1; min-width: 0; }
        .cd-item-name { font-size: 16px; font-weight: 600; color: #eee; letter-spacing: -0.02em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cd-item-meta { display: flex; align-items: center; gap: 8px; margin-top: 5px; }
        .cd-item-qty  { font-size: 13px; color: #ffffff; font-weight: 500; }
        .cd-item-sugar { font-size: 11px; font-weight: 600; color: #818cf8; background: rgba(99,102,241,0.1); padding: 2px 9px; border-radius: 20px; }
        .cd-item-disc  { font-size: 11px; font-weight: 600; color: #f87171; background: rgba(239,68,68,0.1); padding: 2px 9px; border-radius: 20px; }
        .cd-item-unit  { font-size: 13px; color: #ffffff; margin-left: auto; padding-right: 8px; font-family: 'DM Mono', monospace; }
        .cd-item-total { font-family: 'DM Mono', monospace; font-size: 18px; font-weight: 500; color: #ff4545; flex-shrink: 0; min-width: 80px; text-align: right; }

        /* ── SUMMARY BAR ── */
        .cd-summary { background: #0f0f0f; border-top: 1px solid #1d1d1d; padding: 18px 32px 24px; flex-shrink: 0; }
        .cd-summary-inner { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; }
        .cd-summary-left { flex: 1; }
        .cd-summary-rows { margin-bottom: 10px; }
        .cd-summary-row { display: flex; justify-content: space-between; font-size: 13px; color: #ffffff; margin-bottom: 5px; }
        .cd-summary-row.disc { color: #4ade80; }
        .cd-summary-row span:last-child { font-family: 'DM Mono', monospace; }
        .cd-total-block { display: flex; align-items: baseline; gap: 14px; }
        .cd-total-label { font-size: 16px; color: #ffffff; font-weight: 500; }
        .cd-total-val { font-family: 'DM Mono', monospace; font-size: 56px; font-weight: 500; color: #fff; letter-spacing: -0.05em; line-height: 1; }
        .cd-item-count { text-align: right; flex-shrink: 0; }
        .cd-item-count-num { font-family: 'DM Mono', monospace; font-size: 48px; font-weight: 500; color: #ffffff; line-height: 1; letter-spacing: -0.05em; }
        .cd-item-count-label { font-size: 12px; color: #ffffff; text-align: right; margin-top: 4px; }

        /* ════════════════════════════════════════
           QR CHECKOUT SCREEN
        ════════════════════════════════════════ */
        .cd-qr-screen {
            position: fixed; inset: 0;
            background: #0a0a0a;
            display: none;
            z-index: 100;
            animation: fadeIn 0.35s ease both;
        }
        .cd-qr-screen.visible { display: flex; }

        .cd-qr-inner {
            flex: 1;
            display: flex;
            align-items: stretch;
            overflow: hidden;
        }

        /* Left panel — cart summary */
        .cd-qr-left {
            width: 420px;
            flex-shrink: 0;
            background: #111;
            border-right: 1px solid #1d1d1d;
            display: flex;
            flex-direction: column;
            padding: 28px 28px 24px;
            overflow: hidden;
        }
        .cd-qr-left-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.12em;
            color: #ffffff;
            text-transform: uppercase;
            margin-bottom: 18px;
        }
        .cd-qr-item-list {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .cd-qr-item-list::-webkit-scrollbar { width: 3px; }
        .cd-qr-item-list::-webkit-scrollbar-thumb { background: #1d1d1d; border-radius: 4px; }

        .cd-qr-item-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #161616;
        }
        .cd-qr-item-row:last-child { border-bottom: none; }
        .cd-qr-thumb { width: 44px; height: 44px; border-radius: 10px; object-fit: contain; background: #161616; border: 1px solid #1d1d1d; padding: 5px; flex-shrink: 0; }
        .cd-qr-thumb-ph { width: 44px; height: 44px; border-radius: 10px; background: #161616; border: 1px solid #1d1d1d; display: flex; align-items: center; justify-content: center; color: #2a2a2a; font-size: 16px; flex-shrink: 0; }
        .cd-qr-item-info { flex: 1; min-width: 0; }
        .cd-qr-item-name { font-size: 13px; font-weight: 600; color: #ccc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cd-qr-item-meta { display: flex; align-items: center; gap: 6px; margin-top: 4px; flex-wrap: wrap; }
        .cd-qr-item-qty  { font-size: 11px; color: #ffffff; }
        .cd-qr-item-sugar { font-size: 10px; font-weight: 600; color: #818cf8; background: rgba(99,102,241,0.1); padding: 1px 7px; border-radius: 20px; }
        .cd-qr-item-disc  { font-size: 10px; font-weight: 600; color: #f87171; background: rgba(239,68,68,0.1); padding: 1px 7px; border-radius: 20px; }
        .cd-qr-item-amt { font-family: 'DM Mono', monospace; font-size: 13px; color: #ff4545; flex-shrink: 0; }

        .cd-qr-totals { border-top: 1px solid #1a1a1a; padding-top: 14px; }
        .cd-qr-total-row { display: flex; justify-content: space-between; font-size: 12px; color: #ffffff; margin-bottom: 5px; }
        .cd-qr-total-row span:last-child { font-family: 'DM Mono', monospace; }
        .cd-qr-total-row.disc { color: #4ade80; }
        .cd-qr-total-row.grand { font-size: 15px; color: #888; margin-top: 8px; padding-top: 8px; border-top: 1px solid #1d1d1d; }
        .cd-qr-total-row.grand span:last-child { font-size: 22px; color: #fff; font-weight: 500; }

        /* Right panel — QR */
        .cd-qr-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0;
            padding: 40px 60px;
        }

        .cd-qr-instruction {
            font-size: 13px;
            color: #ffffff;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cd-qr-instruction::before,
        .cd-qr-instruction::after { content: ''; flex: 1; height: 1px; background: #1a1a1a; }

        /* QR code wrapper */
        .cd-qr-box {
            position: relative;
            background: #fff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 0 0 1px rgba(255,255,255,0.06), 0 24px 60px rgba(0,0,0,0.6);
            margin-bottom: 24px;
            width: 260px;
            margin-left: 60px;
            /* height: 250px; */
        }
        /* KHQR center logo */
        .cd-qr-center {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 40px; height: 40px;
            background: #fff;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .cd-qr-center-inner {
            width: 30px; height: 30px;
            background: #E8005A;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
        }

        /* Amount pill */
        .cd-qr-amount {
            font-family: 'DM Mono', monospace;
            font-size: 52px;
            font-weight: 500;
            color: #fff;
            letter-spacing: -0.05em;
            line-height: 1;
            margin-bottom: 8px;
        }

        .cd-qr-merchant {
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 4px;
        }
        .cd-qr-acc {
            font-size: 11px;
            color: #ffffff;
            margin-bottom: 28px;
        }

        /* Scanning pulse */
        @keyframes scanPulse { 0%,100%{opacity:0.4;transform:scaleX(1)} 50%{opacity:1;transform:scaleX(0.85)} }
        .cd-qr-scan-bar {
            width: 200px;
            height: 2px;
            background: #E8005A;
            border-radius: 2px;
            animation: scanPulse 2s ease-in-out infinite;
        }
        .cd-qr-scan-label {
            font-size: 12px;
            color: #ffffff;
            margin-top: 12px;
        }

        /* Skeleton (while QR is loading) */
        .cd-qr-skeleton {
            width: 220px; height: 220px;
            background: #161616;
            border-radius: 16px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .cd-qr-skeleton i { font-size: 28px; color: #2a2a2a; animation: spin 1s linear infinite; }
        .cd-qr-skeleton span { font-size: 12px; color: #2a2a2a; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── THANK YOU OVERLAY ── */
        .cd-thankyou {
            position: fixed; inset: 0; background: #0a0a0a;
            display: none; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 20px; z-index: 200;
            animation: fadeIn 0.4s ease both;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .cd-thankyou-circle {
            width: 96px; height: 96px; border-radius: 50%;
            background: rgba(74,222,128,0.08);
            border: 1px solid rgba(74,222,128,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 44px; color: #4ade80; margin-bottom: 8px;
            animation: popIn 0.5s cubic-bezier(0.34,1.5,0.64,1) both 0.1s;
        }
        @keyframes popIn { from { opacity: 0; transform: scale(0.6); } to { opacity: 1; transform: scale(1); } }
        .cd-thankyou h2 { font-size: 42px; font-weight: 700; color: #fff; letter-spacing: -0.05em; }
        .cd-thankyou-amount { font-family: 'DM Mono', monospace; font-size: 64px; font-weight: 500; color: #4ade80; letter-spacing: -0.05em; line-height: 1; }
        .cd-thankyou-sub { font-size: 16px; color: #444; margin-top: 4px; }

        /* ── STATUS PILL ── */
        .cd-status { position: fixed; bottom: 18px; right: 22px; display: flex; align-items: center; gap: 7px; font-size: 11px; color: #2a2a2a; pointer-events: none; }
        .cd-status-dot { width: 6px; height: 6px; border-radius: 50%; background: #2a2a2a; flex-shrink: 0; }
        .cd-status-dot.live { background: #4ade80; animation: statusPulse 2.5s ease-in-out infinite; }
        @keyframes statusPulse { 0%,100%{opacity:1} 50%{opacity:0.25} }
    </style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="cd-header">
        <div class="cd-logo">{{ env('BAKONG_MERCHANT_NAME', 'My Shop') }} <span>●</span></div>
        <div class="cd-clock-wrap">
            <div class="cd-time" id="cdTime">--:--:--</div>
            <div class="cd-date" id="cdDate"></div>
        </div>
    </div>

    {{-- ── IDLE ── --}}
    {{-- <div class="cd-idle" id="cdIdle">
        <div class="cd-idle-icon"><i class="fas fa-store"></i></div>
        <div class="cd-idle-title">Welcome</div>
        <div class="cd-idle-sub">Waiting for your order…</div>
    </div> --}}

    {{-- ── IDLE WITH SLIDE ── --}}
<div class="cd-idle" id="cdIdle">
    <div class="cd-idle-slider">
        <img src="/image/slide/cover_1.jpg" class="idle-slide active">
        <img src="/image/slide/cover_2.jpg" class="idle-slide">
        <img src="/image/slide/cover_3.jpg" class="idle-slide">
    </div>
    <div class="cd-idle-title">Welcome to Z’SHOP</div>
    <div class="cd-idle-sub">Please select your items at the counter</div>
</div>
    {{-- ── ACTIVE CART ── --}}
    <div class="cd-active" id="cdActive">
        <div class="cd-items" id="cdItemList"></div>
        <div class="cd-summary">
            <div class="cd-summary-inner">
                <div class="cd-summary-left">
                    <div class="cd-summary-rows">
                        <div class="cd-summary-row">
                            <span>Subtotal</span>
                            <span id="cdSubtotal">$0.00</span>
                        </div>
                        <div class="cd-summary-row disc" id="cdDiscRow" style="display:none;">
                            <span><i class="fas fa-tag" style="font-size:10px;margin-right:4px;"></i>Discount</span>
                            <span id="cdDiscount">-$0.00</span>
                        </div>
                    </div>
                    <div class="cd-total-block">
                        <span class="cd-total-label">Total</span>
                        <span class="cd-total-val" id="cdTotal">$0.00</span>
                    </div>
                </div>
                <div class="cd-item-count">
                    <div class="cd-item-count-num" id="cdItemCount">0</div>
                    <div class="cd-item-count-label">items</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ QR CHECKOUT SCREEN ════ --}}
    <div class="cd-qr-screen" id="cdQrScreen">
        <div class="cd-qr-inner">

            {{-- Left: order summary --}}
            <div class="cd-qr-left">
                <div class="cd-qr-left-label">Order Summary</div>
                <div class="cd-qr-item-list" id="cdQrItemList"></div>
                <div class="cd-qr-totals">
                    <div class="cd-qr-total-row">
                        <span>Subtotal</span>
                        <span id="cdQrSubtotal">$0.00</span>
                    </div>
                    <div class="cd-qr-total-row disc" id="cdQrDiscRow" style="display:none;">
                        <span><i class="fas fa-tag" style="font-size:9px;margin-right:3px;"></i>Discount</span>
                        <span id="cdQrDiscount">-$0.00</span>
                    </div>
                    <div class="cd-qr-total-row grand">
                        <span>Total</span>
                        <span id="cdQrGrand">$0.00</span>
                    </div>
                </div>

                {{-- Merchant chip --}}
                <div style="margin-top:16px;background:#0f0f0f;border:1px solid #1a1a1a;border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:10px;">
                    <div style="width:28px;height:28px;border-radius:6px;background:#E8005A;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="3" width="7" height="7" rx="1" fill="white"/>
                            <rect x="14" y="3" width="7" height="7" rx="1" fill="white"/>
                            <rect x="3" y="14" width="7" height="7" rx="1" fill="white"/>
                            <rect x="14" y="14" width="3" height="3" rx="0.5" fill="white"/>
                            <rect x="19" y="14" width="2" height="2" rx="0.5" fill="white"/>
                            <rect x="14" y="19" width="2" height="2" rx="0.5" fill="white"/>
                            <rect x="18" y="18" width="3" height="3" rx="0.5" fill="white"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:12px;font-weight:600;color:#888;">KHQR · Bakong</div>
                        <div style="font-size:10px;color:#333;">{{ env('BAKONG_MERCHANT_ID', 'yourshop@bakong') }}</div>
                    </div>
                    <div style="margin-left:auto;font-size:10px;font-weight:600;color:#4ade80;background:rgba(74,222,128,0.08);padding:2px 8px;border-radius:20px;">Selected</div>
                </div>
            </div>

            {{-- Right: QR code --}}
            <div class="cd-qr-right">
                <div style="width:100%;max-width:380px;text-align:center;">

                    <div class="cd-qr-instruction">Scan to pay</div>

                    {{-- Skeleton --}}
                    <div class="cd-qr-skeleton" id="cdQrSkeleton">
                        <i class="fas fa-circle-notch"></i>
                        <span>Generating QR…</span>
                    </div>

                    {{-- QR frame --}}
                    <div class="cd-qr-box" id="cdQrBox" style="display:none;">
                        <div id="cdQrCodeEl"></div>
                        <div class="cd-qr-center">
                            <div class="cd-qr-center-inner">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <rect x="3" y="3" width="7" height="7" rx="1" fill="white"/>
                                    <rect x="14" y="3" width="7" height="7" rx="1" fill="white"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1" fill="white"/>
                                    <rect x="14" y="14" width="3" height="3" rx="0.5" fill="white"/>
                                    <rect x="19" y="14" width="2" height="2" rx="0.5" fill="white"/>
                                    <rect x="14" y="19" width="2" height="2" rx="0.5" fill="white"/>
                                    <rect x="18" y="18" width="3" height="3" rx="0.5" fill="white"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="cd-qr-amount" id="cdQrAmount">$0.00</div>
                    <div class="cd-qr-merchant">{{ env('BAKONG_MERCHANT_NAME', 'My Shop') }}</div>
                    <div class="cd-qr-acc">{{ env('BAKONG_MERCHANT_ID', 'yourshop@bakong') }} · {{ env('BAKONG_MERCHANT_CITY', 'Phnom Penh') }}</div>

                    <div class="cd-qr-scan-bar"></div>
                    <div class="cd-qr-scan-label">Waiting for payment confirmation…</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── THANK YOU ── --}}
    <div class="cd-thankyou" id="cdThankyou">
        <div class="cd-thankyou-circle"><i class="fas fa-check"></i></div>
        <h2>Thank you!</h2>
        <div class="cd-thankyou-amount" id="cdPaidAmount">$0.00</div>
        <div class="cd-thankyou-sub">Payment received · Have a great day!</div>
    </div>

    {{-- ── STATUS DOT ── --}}
    <div class="cd-status">
        <div class="cd-status-dot" id="cdDot"></div>
        <span id="cdSignalText">Waiting for POS…</span>
    </div>

    <script>
    /* ── CLOCK ── */
    function updateClock() {
        const now = new Date();
        document.getElementById('cdTime').textContent = now.toLocaleTimeString('en-US', { hour12: false });
        document.getElementById('cdDate').textContent = now.toLocaleDateString('en-US', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }
    updateClock();
    setInterval(updateClock, 1000);

    /* ── BROADCAST CHANNEL ── */
    const channel = new BroadcastChannel('cart-sync');
    const dot = document.getElementById('cdDot');
    const signalText = document.getElementById('cdSignalText');

    channel.onmessage = (event) => {
        const { type, data } = event.data;
        dot.className = 'cd-status-dot live';
        signalText.textContent = 'Connected';

        if (type === 'cart-update')      renderCart(data);
        if (type === 'cart-clear')       showIdle();
        if (type === 'checkout-qr')      showQrScreen(data);
        if (type === 'checkout-qr-hide') hideQrScreen();
        if (type === 'checkout-success') showThankyou(data.total);
    };

    /* ── LOAD FROM LOCALSTORAGE ── */
    try {
        const saved = localStorage.getItem('cd_cart');
        if (saved) {
            const parsed = JSON.parse(saved);
            if (parsed && parsed.items && parsed.items.length) {
                renderCart(parsed);
                dot.className = 'cd-status-dot live';
                signalText.textContent = 'Restored';
            }
        }
    } catch(e) {}

    /* ── RENDER CART ── */
    function renderCart(data) {
        if (!data || !data.items || !data.items.length) { showIdle(); return; }

        hideQrScreen();
        document.getElementById('cdIdle').style.display   = 'none';
        document.getElementById('cdActive').style.display = 'flex';
        document.getElementById('cdThankyou').style.display = 'none';

        let totalQty = 0, html = '';
        data.items.forEach((item, i) => {
            totalQty += item.qty;
            const thumb = item.image
                ? `<img class="cd-item-thumb" src="${item.image}" alt="${item.name}">`
                : `<div class="cd-item-thumb-ph"><i class="fas fa-box-open"></i></div>`;
            const discBadge = (item.discount && item.discount !== '0')
                ? `<span class="cd-item-disc">${item.discount} off</span>` : '';
            html += `
                <div class="cd-item" style="animation-delay:${i * 0.04}s">
                    ${thumb}
                    <div class="cd-item-body">
                        <div class="cd-item-name">${item.name}</div>
                        <div class="cd-item-meta">
                            <span class="cd-item-qty">×${item.qty}</span>
                            <span class="cd-item-sugar">${item.sugar}</span>
                            ${discBadge}
                        </div>
                    </div>
                    <div class="cd-item-unit">$${parseFloat(item.price).toFixed(2)}</div>
                    <div class="cd-item-total">$${parseFloat(item.lineTotal).toFixed(2)}</div>
                </div>`;
        });

        document.getElementById('cdItemList').innerHTML          = html;
        document.getElementById('cdSubtotal').textContent        = `$${parseFloat(data.subtotal).toFixed(2)}`;
        document.getElementById('cdTotal').textContent           = `$${parseFloat(data.total).toFixed(2)}`;
        document.getElementById('cdItemCount').textContent       = totalQty;

        const discRow = document.getElementById('cdDiscRow');
        if (parseFloat(data.discount) > 0.001) {
            document.getElementById('cdDiscount').textContent = `-$${parseFloat(data.discount).toFixed(2)}`;
            discRow.style.display = 'flex';
        } else {
            discRow.style.display = 'none';
        }

        const list = document.getElementById('cdItemList');
        setTimeout(() => { list.scrollTop = list.scrollHeight; }, 100);
    }

    /* ── SHOW QR SCREEN ── */
    let qrInstance = null;

    function showQrScreen(data) {
        // data = { qr, total, items, subtotal, discount, order_ref }
        document.getElementById('cdIdle').style.display     = 'none';
        document.getElementById('cdActive').style.display   = 'none';
        document.getElementById('cdThankyou').style.display = 'none';

        const screen = document.getElementById('cdQrScreen');
        screen.classList.add('visible');

        // Amount
        document.getElementById('cdQrAmount').textContent = `$${parseFloat(data.total).toFixed(2)}`;

        // Totals
        document.getElementById('cdQrSubtotal').textContent = `$${parseFloat(data.subtotal).toFixed(2)}`;
        document.getElementById('cdQrGrand').textContent    = `$${parseFloat(data.total).toFixed(2)}`;

        const discRow = document.getElementById('cdQrDiscRow');
        if (data.discount && parseFloat(data.discount) > 0.001) {
            document.getElementById('cdQrDiscount').textContent = `-$${parseFloat(data.discount).toFixed(2)}`;
            discRow.style.display = 'flex';
        } else {
            discRow.style.display = 'none';
        }

        // Item list
        let html = '';
        if (data.items && data.items.length) {
            data.items.forEach(item => {
                const thumb = item.image
                    ? `<img class="cd-qr-thumb" src="${item.image}" alt="${item.name}">`
                    : `<div class="cd-qr-thumb-ph"><i class="fas fa-box-open"></i></div>`;
                const discBadge = (item.discount && item.discount !== '0')
                    ? `<span class="cd-qr-item-disc">${item.discount} off</span>` : '';
                html += `
                    <div class="cd-qr-item-row">
                        ${thumb}
                        <div class="cd-qr-item-info">
                            <div class="cd-qr-item-name">${item.name} ×${item.qty}</div>
                            <div class="cd-qr-item-meta">
                                <span class="cd-qr-item-qty">$${parseFloat(item.price).toFixed(2)}</span>
                                <span class="cd-qr-item-sugar">${item.sugar}</span>
                                ${discBadge}
                            </div>
                        </div>
                        <div class="cd-qr-item-amt">$${parseFloat(item.lineTotal).toFixed(2)}</div>
                    </div>`;
            });
        }
        document.getElementById('cdQrItemList').innerHTML = html;

        // QR code
        document.getElementById('cdQrSkeleton').style.display = 'flex';
        document.getElementById('cdQrBox').style.display      = 'none';

        const el = document.getElementById('cdQrCodeEl');
        el.innerHTML = '';
        if (qrInstance) { try { qrInstance.clear(); } catch(e){} qrInstance = null; }

        if (data.qr) {
            setTimeout(() => {
                qrInstance = new QRCode(el, {
                    text:         data.qr,
                    width:        220,
                    height:       220,
                    correctLevel: QRCode.CorrectLevel.M,
                });
                document.getElementById('cdQrSkeleton').style.display = 'none';
                document.getElementById('cdQrBox').style.display      = 'block';
            }, 150);
        }
    }

    /* ── HIDE QR SCREEN ── */
    function hideQrScreen() {
        document.getElementById('cdQrScreen').classList.remove('visible');
    }

    /* ── IDLE ── */
    function showIdle() {
        hideQrScreen();
        document.getElementById('cdIdle').style.display     = 'flex';
        document.getElementById('cdActive').style.display   = 'none';
        document.getElementById('cdThankyou').style.display = 'none';
    }

    /* ── THANK YOU ── */
    function showThankyou(total) {
        hideQrScreen();
        document.getElementById('cdPaidAmount').textContent    = `$${parseFloat(total).toFixed(2)}`;
        document.getElementById('cdThankyou').style.display    = 'flex';
        document.getElementById('cdActive').style.display      = 'none';
        document.getElementById('cdIdle').style.display        = 'none';
        setTimeout(showIdle, 6000);
    }
    function startIdleSlider() {
        const slides = document.querySelectorAll('.idle-slide');
        let currentSlide = 0;

        if (slides.length === 0) return;

        setInterval(() => {
            // ដក class active ចេញពី slide បច្ចុប្បន្ន
            slides[currentSlide].classList.remove('active');
            
            // ប្តូរទៅ slide បន្ទាប់
            currentSlide = (currentSlide + 1) % slides.length;
            
            // ដាក់ class active ទៅ slide ថ្មី
            slides[currentSlide].classList.add('active');
        }, 5000); // ៥ វិនាទី ប្តូរម្តង
    }

    // ហៅ Function ឱ្យដំណើរការ
    startIdleSlider();
    </script>
</body>
</html>
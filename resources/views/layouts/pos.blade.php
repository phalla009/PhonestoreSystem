<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('pageTitle', 'POS')</title>
  <link rel="icon" type="image/x-icon" href="/image/logokr.jpg" />
  <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  @yield('headerBlock')
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #f3f4f6; font-family: sans-serif; }

    .pos-topbar {
        background: #1e2333;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .pos-topbar-logo {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .pos-topbar-logo .l1 { color: #ff4d4d; }
    .pos-topbar-logo .l2 { color: #ffa500; }
    .pos-topbar-logo .l3 { color: #ffffff; margin-left: 8px; }

    .pos-topbar-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .pos-topbar-user {
        font-size: 13px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pos-topbar-user i { color: #6366f1; }

    .pos-back-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 7px 24px;
        background: #dbad2e;
        color: #ffffff;
        border-radius: 5px;
        font-size: 13px;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }

    .pos-back-btn:hover {
        background: #d9a61a;
        color: #fff;
    }

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

    /* ── STOCK ALERT BANNER ── */
    .stock-alert-banner {
        background: #fff7ed;
        border-bottom: 1px solid #fed7aa;
        padding: 10px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .stock-alert-banner .alert-icon {
        color: #ea580c;
        font-size: 15px;
        flex-shrink: 0;
    }

    .stock-alert-banner .alert-label {
        font-size: 13px;
        font-weight: 600;
        color: #9a3412;
        flex-shrink: 0;
    }

    .stock-alert-items {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .stock-alert-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #ffedd5;
        border: 1px solid #fed7aa;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 12px;
        color: #7c2d12;
        font-weight: 500;
    }

    .stock-alert-chip .chip-stock {
        font-weight: 700;
        color: #ea580c;
    }
  </style>
</head>
<body>

  <div class="pos-topbar">
    {{-- <div class="pos-topbar-logo">
      <span class="l1">K</span><span class="l2"> R</span><span class="l3">System</span>
    </div> --}}
    <h3 style="color: white;">
        <i class="fas fa-cash-register"></i> POS
    </h3>
    <div class="pos-topbar-right">
      <div class="pos-topbar-user">
        <i class="fas fa-user"></i>
        {{ Auth::user()->name }} — {{ Auth::user()->role->role_name ?? 'No Role' }}
      </div>
      <a href="{{ route('dashboard') }}" class="pos-back-btn">
        <i class="fas fa-chevron-left"></i> Back
      </a>
    </div>
  </div>

  {{-- ── STOCK ALERT BANNER (only shows when low stock products exist) ── --}}
  @php
      $lowStockProducts = isset($products)
          ? $products->filter(fn($p) => $p->add_to_pos == 1 && $p->stock > 0 && $p->stock < 10)
          : collect();
  @endphp

  @if($lowStockProducts->isNotEmpty())
  <div class="stock-alert-banner">
      <i class="fas fa-triangle-exclamation alert-icon"></i>
      <span class="alert-label">Low Stock Alert:</span>
      <div class="stock-alert-items">
          @foreach($lowStockProducts as $lp)
              <span class="stock-alert-chip">
                  <i class="fas fa-box" style="font-size:10px;"></i>
                  {{ $lp->name }}
                  <span class="chip-stock">({{ $lp->stock }} left)</span>
              </span>
          @endforeach
      </div>
  </div>
  @endif

  <div id="loading-overlay">
    <div class="spinner"></div>
    <div id="loading-text">Loading...</div>
  </div>

  <div class="pos-page-wrap">
    @yield('content')
  </div>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('pageTitle', 'Dashboard')</title>
  <link rel="icon" type="image/x-icon" href="/image/logokr.jpg" />
  <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  @yield('headerBlock')

  <style>
    .menu-item { padding: 10px 20px; margin: 5px 0; text-align: center; cursor: pointer; width: 150px; position: relative; }
    .submenu { max-height: 0; overflow: hidden; margin-top: 0; transition: max-height 0.3s ease, margin-top 0.3s ease; }
    li.menu-container.open .submenu { max-height: 500px; margin-top: 5px; }
    .submenu a { display: block; margin: 5px 0; padding: 10px 20px; text-align: center; text-decoration: none; color: white; }
    .submenu a:hover { background-color: #37546c; }
    .icon i { margin-right: 8px; font-size: 18px; }
    .sidebar { height: 100vh; overflow-y: auto; overflow-x: hidden; }
    .dropdown-arrow { float: right; font-size: 15px; margin-top: 2px; margin-left: 80px; transition: transform 0.3s ease; }
    li.menu-container.open .dropdown-arrow i { transform: rotate(90deg); }
    .sidebar-menu :hover a { font-size: 17px; padding-left: 10px; transition: all 0.2s ease; }
    .l1 { color: #ff4d4d; } .l2 { color: #ffa500; } .l3 { color: #ffffff; }
    .sidebar-menu li:last-child a:hover { color: red; }
    #logout-confirm { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999; }
    .confirm-box { color: red; background: white; padding: 20px 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.3); text-align: center; font-family: Arial, sans-serif; }
    .confirm-box p { font-size: 18px; margin-bottom: 20px; }
    .confirm-box button { background-color: #ea0000; width: 100px; border: none; color: white; padding: 10px 18px; margin: 0 10px; font-size: 16px; border-radius: 24px; cursor: pointer; }
    .confirm-box button#confirm-no { background-color: #6c757d; }
    .icon-container { font-size: 28px; color: red; margin-bottom: 15px; }
    #logout-link:hover { color: red; }
    .confirm-box button:hover { opacity: 0.7; }
    .sidebar-header .user-info { margin-top:20px; display: flex; align-items: center; gap: 8px; font-size: 16px; margin-bottom: -10px; }
    .sidebar-header .user-info i { font-size: 18px; color: #afdeff; }

    #loading-overlay {
      position: fixed;
      top:0; left:0; right:0; bottom:0;
      background: rgba(255,255,255,0.8);
      display: none;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      z-index: 99999;
    }
    .spinner {
      border:6px solid #f3f3f3;
      border-top:6px solid #3498db;
      border-radius:50%;
      width:60px;
      height:60px;
      animation:spin 1s linear infinite;
    }
    @keyframes spin {0%{transform:rotate(0deg);} 100%{transform:rotate(360deg);}}
    #loading-text { margin-top:15px; font-size:16px; color:#333; }
  </style>
</head>
<body>

<div class="dashboard">
  <div class="sidebar">
    <div class="sidebar-header">
      <div id="logo">
        <span class="l1">K</span>
        <span class="l2">R</span>
        <span class="l3" style="margin-left: 10px;">System</span>
      </div>
      <p class="user-info">
        <i class="fas fa-user"></i>
        <span>{{ Auth::user()->name }}</span>
        - <span class="user-role">{{ Auth::user()->role->role_name ?? 'No Role' }}</span>
      </p>
    </div>
    <ul class="sidebar-menu">

      {{-- Dashboard --}}
      @if(Auth::user()->hasPermission('dashboard'))
      <li><a href="{{ route('dashboard') }}"><span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a></li>
      @endif

      {{-- Products --}}
      @if(Auth::user()->hasPermission('products') || Auth::user()->hasPermission('categories'))
      <li class="menu-container">
        {{-- FIX: ប្រើ data-toggle="submenu" ជំនួស href="javascript:void(0)" --}}
        <a href="javascript:void(0)" class="submenu-toggle">
          <span class="icon"><i class="fas fa-box-open"></i></span> Products
          <span class="dropdown-arrow"><i class="fas fa-chevron-right"></i></span>
        </a>
        <div class="submenu">
          @if(Auth::user()->hasPermission('products'))
          <a href="{{ route('products.index') }}"><span class="icon"><i class="fas fa-box"></i></span> Products</a>
          @endif
          @if(Auth::user()->hasPermission('categories'))
          <a href="{{ route('categories.index') }}"><span class="icon"><i class="fas fa-tags"></i></span> Category</a>
          @endif
        </div>
      </li>
      @endif

      {{-- Orders --}}
      @if(Auth::user()->hasPermission('orders'))
      <li><a href="{{ route('orders.index') }}"><span class="icon"><i class="fas fa-shopping-cart"></i></span> Orders</a></li>
      @endif

      {{-- Customers --}}
      @if(Auth::user()->hasPermission('customers'))
      <li><a href="{{ route('customers.index') }}"><span class="icon"><i class="fas fa-users"></i></span> Customers</a></li>
      @endif

      {{-- Payments --}}
      @if(Auth::user()->hasPermission('payments'))
      <li><a href="{{ route('payments.index') }}"><span class="icon"><i class="fas fa-credit-card"></i></span> All Payments</a></li>
      @endif

      {{-- Inventory --}}
      @if(Auth::user()->hasPermission('inventory'))
      <li><a href="{{ route('inventorys.index') }}"><span class="icon"><i class="fas fa-boxes"></i></span> Inventory</a></li>
      @endif

      {{-- Reports --}}
      @if(Auth::user()->hasPermission('reports'))
      <li><a href="{{ route('reports.index') }}"><span class="icon"><i class="fas fa-chart-line"></i></span> Reports</a></li>
      @endif

      {{-- Users --}}
      {{-- FIX: permission check គួរតែ 'usermanagers' (plural) ត្រូវតរង្វាំ --}}
      @if(Auth::user()->hasPermission('usermanagers') || Auth::user()->hasPermission('userroles'))
      <li class="menu-container">
        <a href="javascript:void(0)" class="submenu-toggle">
          <span class="icon"><i class="fas fa-users"></i></span> Users
          <span class="dropdown-arrow"><i class="fas fa-chevron-right"></i></span>
        </a>
        <div class="submenu">
          @if(Auth::user()->hasPermission('usermanagers'))
          <a href="{{ route('usermanagers.index') }}"><span class="icon"><i class="fas fa-user"></i></span> User Managers</a>
          @endif
          @if(Auth::user()->hasPermission('userroles'))
          <a href="{{ route('userroles.index') }}"><span class="icon"><i class="fas fa-user-shield"></i></span> User Roles</a>
          @endif
        </div>
      </li>
      @endif

      {{-- Settings --}}
      @if(Auth::user()->hasPermission('settings'))
      <li><a href="#"><span class="icon"><i class="fas fa-cog"></i></span> Setting</a></li>
      @endif

      {{-- Logout --}}
      <li>
        <a id="logout-link"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
      </li>

    </ul>
  </div>

  <div class="main-content">
    <div id="content">@yield('content')</div>
  </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay">
  <div class="spinner"></div>
  <div id="loading-text">Loading...</div>
</div>

<!-- Logout confirm -->
<div id="logout-confirm" style="display:none;">
  <div class="confirm-box">
    <div class="icon-container"><i class="fas fa-sign-out-alt"></i></div>
    <p>Are you sure you want to logout?</p>
    <button id="confirm-yes">Yes</button>
    <button id="confirm-no">No</button>
  </div>
</div>

<script>
  const overlay = document.getElementById('loading-overlay');

  // =============================================
  // FIX 1: Submenu toggle — ប្រើ class .submenu-toggle
  // ដាក់ class នេះតែនៅលើ parent link ជំនួស querySelectorAll ទូទៅ
  // =============================================
  document.querySelectorAll('.submenu-toggle').forEach(function(toggleLink) {
    toggleLink.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation(); // ❌ កុំឱ្យ event bubble ឡើង
      const parent = this.closest('.menu-container');
      if (parent) {
        parent.classList.toggle('open');
      }
    });
  });

  // =============================================
  // FIX 2: Loading overlay — apply តែ links ដែលមាន href ពិតប្រាកដ
  // ត្រូវ exclude: javascript:void(0), #, submenu-toggle
  // =============================================
  document.querySelectorAll('.sidebar-menu a').forEach(function(link) {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');

      // Skip: no href, void, hash, logout, submenu toggle
      if (
        !href ||
        href === 'javascript:void(0)' ||
        href === '#' ||
        this.id === 'logout-link' ||
        this.classList.contains('submenu-toggle')
      ) {
        return; // ចេញ — កុំ redirect
      }

      // មាន href ពិតប្រាកដ → បង្ហាញ loading ហើយ navigate
      e.preventDefault();
      overlay.style.display = 'flex';
      window.location.href = href;
    });
  });

  // =============================================
  // Logout confirm
  // =============================================
  const logoutLink    = document.getElementById('logout-link');
  const logoutConfirm = document.getElementById('logout-confirm');
  const confirmYes    = document.getElementById('confirm-yes');
  const confirmNo     = document.getElementById('confirm-no');
  const logoutForm    = document.getElementById('logout-form');

  logoutLink.addEventListener('click', function(e) {
    e.preventDefault();
    logoutConfirm.style.display = 'flex';
  });
  confirmYes.addEventListener('click', function() { logoutForm.submit(); });
  confirmNo.addEventListener('click',  function() { logoutConfirm.style.display = 'none'; });

  // =============================================
  // Auto-open submenu ប្រសិនបើ URL ត្រូវគ្នា
  // =============================================
  const currentPath = window.location.pathname;
  document.querySelectorAll('.submenu a').forEach(function(link) {
    if (link.getAttribute('href') && currentPath.startsWith(link.getAttribute('href').replace(window.location.origin, ''))) {
      const parent = link.closest('.menu-container');
      if (parent) parent.classList.add('open');
    }
  });
</script>

</body>
</html>
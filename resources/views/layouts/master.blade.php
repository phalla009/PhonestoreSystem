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
    /* Submenu styles */
    .submenu { max-height: 0; overflow: hidden; margin-top: 0; transition: max-height 0.3s ease, margin-top 0.3s ease; }
    li.menu-container.open .submenu { max-height: 500px; margin-top: 5px; }
    .submenu a { display: block; margin: 5px 0; padding: 10px 20px; text-align: center; text-decoration: none; color: white; }
    .submenu a:hover { background-color: #37546c; }
    .icon i { margin-right: 8px; font-size: 18px; }
    .dropdown-arrow { float: right; font-size: 15px; margin-top: 2px; margin-left: 80px; transition: transform 0.3s ease; }
    li.menu-container.open .dropdown-arrow i { transform: rotate(90deg); }
    .sidebar-menu :hover a { font-size: 17px; padding-left: 10px; transition: all 0.2s ease; }
    .l1 { color: #ff4d4d; } .l2 { color: #ffa500; } .l3 { color: #ffffff; }
    .sidebar-menu li:last-child a:hover { color: red; }
    .sidebar-header .user-info { margin-top: 20px; display: flex; align-items: center; gap: 8px; font-size: 16px; margin-bottom: -10px; }
    .sidebar-header .user-info i { font-size: 18px; color: #afdeff; }

    /* Loading overlay */
    #loading-overlay {
      position: fixed; top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(255,255,255,0.85);
      display: none;
      justify-content: center; align-items: center; flex-direction: column;
      z-index: 99999;
    }
    .spinner {
      border: 6px solid #f3f3f3; border-top: 6px solid #3498db;
      border-radius: 50%; width: 60px; height: 60px;
      animation: spin 1s linear infinite;
    }
    @keyframes spin { 0%{ transform:rotate(0deg); } 100%{ transform:rotate(360deg); } }
    #loading-text { margin-top: 15px; font-size: 16px; color: #333; }

    /* Logout modal */
    #logout-confirm {
      position: fixed; top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.45); backdrop-filter: blur(3px);
      display: none; align-items: center; justify-content: center; z-index: 9999;
    }
    .confirm-box {
      background: #fff; width: 320px; padding: 30px 25px; border-radius: 14px;
      text-align: center; box-shadow: 0 10px 35px rgba(0,0,0,0.25);
      animation: popup 0.3s ease; font-family: Arial, sans-serif;
    }
    .icon-container { font-size: 42px; color: #e74c3c; margin-bottom: 15px; }
    .confirm-box p { font-size: 18px; color: #333; margin-bottom: 25px; font-weight: 500; }
    .confirm-box button { width: 110px; padding: 10px; border: none; border-radius: 24px; font-size: 15px; cursor: pointer; transition: 0.2s; }
    #confirm-yes { background: #e74c3c; color: white; }
    #confirm-no  { background: #e0e0e0; color: #555; }
    #confirm-yes:hover { background: #c0392b; }
    #confirm-no:hover  { background: #cfcfcf; }
    #logout-link:hover { color: red; }
    @keyframes popup { from { transform:scale(0.8); opacity:0; } to { transform:scale(1); opacity:1; } }
  </style>
</head>
<body>

<!-- Burger button — styled entirely by main.css -->
<button class="burger-btn" id="burgerBtn" aria-label="Toggle sidebar">
  <span></span><span></span><span></span>
</button>

<!-- Sidebar backdrop -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="dashboard">

  <div class="sidebar" id="mainSidebar">
    <div class="sidebar-header">
      <div id="logo">
        <span class="l1">K</span><span class="l2">R</span>
        <span class="l3" style="margin-left:10px;">System</span>
      </div>
      <p class="user-info">
        <i class="fas fa-user"></i>
        <span>{{ Auth::user()->name }}</span>
        - <span class="user-role">{{ Auth::user()->role->role_name ?? 'No Role' }}</span>
      </p>
    </div>
    <ul class="sidebar-menu">

      @if(Auth::user()->hasPermission('dashboard'))
      <li><a href="{{ route('dashboard') }}"><span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a></li>
      @endif

      @if(Auth::user()->hasPermission('pos'))
      <li><a href="{{ route('pos.index') }}"><span class="icon"><i class="fas fa-cash-register"></i></span> POS</a></li>
      @endif

      @if(Auth::user()->hasPermission('products') || Auth::user()->hasPermission('categories'))
      <li class="menu-container">
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

      @if(Auth::user()->hasPermission('orders'))
      <li><a href="{{ route('orders.index') }}"><span class="icon"><i class="fas fa-shopping-cart"></i></span> Orders</a></li>
      @endif

      @if(Auth::user()->hasPermission('customers'))
      <li><a href="{{ route('customers.index') }}"><span class="icon"><i class="fas fa-users"></i></span> Customers</a></li>
      @endif

      @if(Auth::user()->hasPermission('payments'))
      <li><a href="{{ route('payments.index') }}"><span class="icon"><i class="fas fa-credit-card"></i></span> All Payments</a></li>
      @endif

      @if(Auth::user()->hasPermission('inventory'))
      <li><a href="{{ route('inventorys.index') }}"><span class="icon"><i class="fas fa-boxes"></i></span> Inventory</a></li>
      @endif

      @if(Auth::user()->hasPermission('reports'))
      <li><a href="{{ route('reports.index') }}"><span class="icon"><i class="fas fa-chart-line"></i></span> Reports</a></li>
      @endif

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

      @if(Auth::user()->hasPermission('settings'))
      <li class="menu-container">
        <a href="javascript:void(0)" class="submenu-toggle">
          <span class="icon"><i class="fas fa-cog"></i></span> Settings
          <span class="dropdown-arrow"><i class="fas fa-chevron-right"></i></span>
        </a>
        <div class="submenu">
          @if(Auth::user()->hasPermission('barcodes'))
          <a href="{{ route('barcodes.index') }}"><span class="icon"><i class="fas fa-barcode"></i></span> Barcode Generator</a>
          @endif
        </div>
      </li>
      @endif

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

<!-- Logout Modal -->
<div id="logout-confirm">
  <div class="confirm-box">
    <div class="icon-container"><i class="fas fa-sign-out-alt"></i></div>
    <p>Are you sure you want to logout?</p>
    <button id="confirm-yes">Yes, Logout!</button>
    <button id="confirm-no">No, Keep it!</button>
  </div>
</div>

<script>
  const overlay        = document.getElementById('loading-overlay');
  const burgerBtn      = document.getElementById('burgerBtn');
  const mainSidebar    = document.getElementById('mainSidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');

  // Hide overlay on every page load — prevents blocking after navigation
  window.addEventListener('load',     function() { overlay.style.display = 'none'; });
  window.addEventListener('pageshow', function() { overlay.style.display = 'none'; });

  // Sidebar open/close
  function openSidebar() {
    mainSidebar.classList.add('open');
    burgerBtn.classList.add('active');
    sidebarOverlay.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    mainSidebar.classList.remove('open');
    burgerBtn.classList.remove('active');
    sidebarOverlay.classList.remove('show');
    document.body.style.overflow = '';
  }

  burgerBtn.addEventListener('click', function() {
    mainSidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });
  sidebarOverlay.addEventListener('click', closeSidebar);
  window.addEventListener('resize', function() { if (window.innerWidth > 768) closeSidebar(); });

  // Submenu toggle
  document.querySelectorAll('.submenu-toggle').forEach(function(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const parent = this.closest('.menu-container');
      if (parent) parent.classList.toggle('open');
    });
  });

  // Sidebar nav links → close sidebar + loading overlay
  document.querySelectorAll('.sidebar-menu a').forEach(function(link) {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (!href || href === 'javascript:void(0)' || href === '#' ||
          this.id === 'logout-link' || this.classList.contains('submenu-toggle')) return;
      e.preventDefault();
      closeSidebar();
      overlay.style.display = 'flex';
      window.location.href = href;
    });
  });

  // Logout
  const logoutLink    = document.getElementById('logout-link');
  const logoutConfirm = document.getElementById('logout-confirm');
  const confirmYes    = document.getElementById('confirm-yes');
  const confirmNo     = document.getElementById('confirm-no');
  const logoutForm    = document.getElementById('logout-form');
  logoutLink.addEventListener('click', function(e) { e.preventDefault(); logoutConfirm.style.display = 'flex'; });
  confirmYes.addEventListener('click', function() { logoutForm.submit(); });
  confirmNo.addEventListener('click',  function() { logoutConfirm.style.display = 'none'; });

  // Auto-open active submenu
  const currentPath = window.location.pathname;
  document.querySelectorAll('.submenu a').forEach(function(link) {
    const href = link.getAttribute('href');
    if (href && currentPath.startsWith(href.replace(window.location.origin, ''))) {
      const parent = link.closest('.menu-container');
      if (parent) parent.classList.add('open');
    }
  });
</script>

</body>
</html>
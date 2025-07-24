<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('pageTitle', 'Dashboard')</title>
  <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  @yield('headerBlock')
  <style>
    .menu-item {
      padding: 10px 20px;
      margin: 5px 0;
      text-align: center;
      cursor: pointer;
      width: 150px;
      position: relative;
    }
    .submenu {
      display: none;
      margin-top: 5px;
    }
    li.menu-container.open .submenu {
      display: block;
    }
    .submenu a {
      display: block;
      margin: 5px 0;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      color: white;
    }
    .submenu a:hover {
      background-color: #37546c;
    }
    .icon i {
      margin-right: 8px;
      font-size: 18px;
    }
    .dropdown-arrow {
      float: right;
      font-size: 15px;
      margin-top: 2px;
      margin-left: 80px;
    }
    li.menu-container .dropdown-arrow i::before {
      content: "\f077";
    }
    li.menu-container.open .dropdown-arrow i::before {
      content: "\f078"; 
    }
    .sidebar-menu :hover a {
      font-size: 17px;
      padding-left: 10px;
      transition: all 0.2s ease;
    }
    .l1 { color: #ff4d4d; } 
    .l2 { color: #ffa500; } 
    .l3 { color: #ffffff; } 
    .sidebar-menu li:last-child a:hover {
      color: red;
    }
    #logout-confirm {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    .confirm-box {
      color: red;
      background: white;
      padding: 20px 30px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
      text-align: center;
      font-family: Arial, sans-serif;
    }
    .confirm-box p {
      font-size: 18px;
      margin-bottom: 20px;
    }
    .confirm-box button {
      background-color: #ea0000;
      width: 100px;
      border: none;
      color: white;
      padding: 10px 18px;
      margin: 0 10px;
      font-size: 16px;
      border-radius: 24px;
      cursor: pointer;
    }
    .confirm-box button#confirm-no {
      background-color: #6c757d;
    }
    .icon-container {
      font-size: 28px;
      color: red;
      margin-bottom: 15px;
    }
    #logout-link:hover {
      color: red;
    }
    .confirm-box button:hover {
      opacity: 0.7;
    }
    .website-button {
      margin-left: 10px;
      font-size: 14px;
      position: relative;
      color: #c7ddfa;
      text-decoration: none;
      padding-bottom: 3px;
      transition: color 0.3s ease;
    }
    .website-button::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      height: 1.5px; 
      width: 0;
      background-color: #ffffff;
      transition: width 0.3s ease;
    }
    .website-button:hover {
      color: #ffffff; 
    }
    .website-button:hover::after {
      width: 100%; 
    }
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
      <a href="#" class="website-button">
        <i class="fas fa-globe"></i> Website 
      </a>
       <a href="#" class="website-button">
         <i class="fab fa-facebook"></i> Page
      </a>
    </div>
    <ul class="sidebar-menu">
      <li>
        <a href="{{ route('dashboard') }}" data-section="dashboard">
          <span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard
        </a>
      </li>
      <li class="menu-container">
        <a href="javascript:void(0)">
          <span class="icon"><i class="fas fa-box-open"></i></span>
            Products  
          <span class="dropdown-arrow"><i class="fas fa-chevron-right"></i></span>
        </a>
        <div class="submenu">
          <a href="{{ route('products.index') }}">
            <span class="icon"><i class="fas fa-box"></i></span> Products
          </a>
          <a href="{{ route('categories.index') }}">
            <span class="icon"><i class="fas fa-mobile-alt"></i></span> Category
          </a>
        </div>
      </li>
      <li>
        <a href="{{ route('orders.index') }}" data-section="orders">
          <span class="icon"><i class="fas fa-shopping-cart"></i></span> Orders
        </a>
      </li>
      <li>
        <a href="{{ route('customers.index') }}" data-section="customers">
          <span class="icon"><i class="fas fa-users"></i></span> Customers
        </a>
      </li>

      <li>
        <a href="{{ route('payments.index') }}" data-section="payments">
          <span class="icon"><i class="fas fa-credit-card"></i></span> All Payments
        </a>
      </li>

      <li>
        <a href="{{ route('inventorys.index') }}" data-section="inventory">
          <span class="icon"><i class="fas fa-boxes"></i></span> Inventory
        </a>
      </li>

      <li>
        <a href="{{ route('reports.index') }}" data-section="report">
          <span class="icon"><i class="fas fa-chart-line"></i></span> Reports
        </a>
      </li>
      <li>
        <a id="logout-link">
          <span class="icon"><i class="fas fa-sign-out-alt"></i></span> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </li>
    <div id="logout-confirm" style="display:none;">
      <div class="confirm-box">
          <div class="icon-container">
            <i class="fas fa-sign-out-alt"></i>
          </div>
        <p>Are you sure you want to logout?</p>
        <button id="confirm-yes">Yes</button>
        <button id="confirm-no">No</button>
      </div>
    </div>
    </ul>
  </div>
  <div class="main-content">
    <div id="content">
      @yield('content')
    </div>
  </div>
</div>
<script>
  document.querySelectorAll('.menu-container > a').forEach(menu => {
    menu.addEventListener('click', function (e) {
      e.preventDefault(); 
      e.stopPropagation(); 
      const parent = this.parentElement;
      parent.classList.toggle('open');
    });
  });
  const logoutLink = document.getElementById('logout-link');
  const logoutConfirm = document.getElementById('logout-confirm');
  const confirmYes = document.getElementById('confirm-yes');
  const confirmNo = document.getElementById('confirm-no');
  const logoutForm = document.getElementById('logout-form');

  logoutLink.addEventListener('click', function(e) {
    e.preventDefault();
    logoutConfirm.style.display = 'flex';  
  });

  confirmYes.addEventListener('click', function() {
    logoutForm.submit(); 
  });

  confirmNo.addEventListener('click', function() {
    logoutConfirm.style.display = 'none';  
  });
</script>
</body>
</html>

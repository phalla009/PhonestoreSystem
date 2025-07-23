<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: rgb(232, 236, 235);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .wrapper {
      display: flex;
      max-width: 900px;
      width: 100%;
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .image-side {
      width: 50%;
      background: url('image/coverLogin.jpg') center/cover no-repeat;
      animation: zoomInOut 10s ease-in-out infinite;
    }

    .form-container {
      width: 50%;
      padding: 40px;
      animation: fadeInUp 1s ease 0.5s both;
    }

    .form-image {
      text-align: center;
      margin-bottom: 20px;
    }

    .form-image img {
      max-width: 100px;
      border-radius: 50%;
      animation: zoomIn 0.8s ease, rotateMe 5s linear infinite;
      transition: all 0.3s ease;
      filter: drop-shadow(12px 8px 8px rgba(105, 134, 128, 0.3));
    }
    /* Animation Definitions */
    @keyframes zoomIn {
      0% { transform: scale(0.8); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    @keyframes rotateMe {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

  .form-title {
      font-family: 'Arial', sans-serif;  
      font-size: 36px;                   
      font-weight: bold;                
      color: #333;                     
      text-align: center;           
      margin-bottom: 20px;           
      text-shadow: 2px 2px 5px rgb(71, 89, 255);
    }

    .form-group {
      margin-bottom: 25px;
      position: relative;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #555;
      font-weight: 500;
      font-size: 14px;
    }

    .form-group input {
      width: 100%;
      padding: 15px;
      border: 2px solid #e1e5e9;
      border-radius: 25px;
      font-size: 16px;
      background-color: #f8f9fa;
      transition: all 0.3s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: #667eea;
      background-color: white;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group.error input {
      border-color: #e74c3c;
      background-color: #fdf2f2;
    }

    .error-message {
      color: #e74c3c;
      font-size: 14px;
      margin-top: 8px;
      display: none;
      align-items: center;
    }

    .error-message.show {
      display: flex;
      animation: shake 0.4s;
    }

    .error-message::before {
      content: '⚠';
      margin-right: 6px;
      font-size: 16px;
    }

    .success-message {
      background: linear-gradient(135deg, #2ecc71, #27ae60);
      color: white;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: fadeInUp 0.5s ease;
    }

    .success-message::before {
      content: '✓';
      margin-right: 8px;
      font-size: 18px;
      font-weight: bold;
    }

    .submit-btn {
      width: 100%;
      padding: 15px;
      background: rgb(59, 59, 220);
      color: white;
      border: none;
      border-radius: 24px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 20px;
      animation: bounceIn 1s ease 1s both;
      transition: transform 0.3s ease;
    }

    .submit-btn:hover {
      transform: scale(1.05);
      animation: pulse 1s infinite;
    }

    .password-toggle {
      position: absolute;
      margin-top: 25px;
      right: 15px;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #666;
      cursor: pointer;
      font-size: 16px;
      transition: color 0.3s ease;
    }

    .password-toggle:hover {
      color: #667eea;
    }

    .required {
      color: #e74c3c;
    }

    /* Animations */
    @keyframes slideFadeIn {
      0% { opacity: 0; transform: translateX(-50px); }
      100% { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes zoomIn {
      0% { transform: scale(0.8); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    @keyframes fadeInScale {
      0% { transform: scale(0.9); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    @keyframes bounceIn {
      0%, 20%, 40%, 60%, 80%, 100% {
        transition-timing-function: ease-in-out;
      }
      0% { transform: scale(0.9); opacity: 0; }
      60% { transform: scale(1.05); opacity: 1; }
      80% { transform: scale(0.95); }
      100% { transform: scale(1); }
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      75% { transform: translateX(5px); }
    }

    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(59, 59, 220, 0.4);
      }
      70% {
        box-shadow: 0 0 0 10px rgba(59, 59, 220, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(59, 59, 220, 0);
      }
    }

    @keyframes zoomInOut {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.08); }
    }

    @media (max-width: 768px) {
      .wrapper {
        flex-direction: column;
      }

      .image-side {
        display: none;
      }

      .form-container {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="image-side"></div>

    <div class="form-container">
      <div class="form-image">
        <img src="image/login.jpg" alt="Login Icon">
      </div>

      <h1 class="form-title">Login</h1>

      @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="error-message show">
          {{ $errors->first() }}
        </div>
      @endif

      <form id="loginForm" method="POST" action="{{ route('login.submit') }}" novalidate>
        @csrf
        <div class="form-group">
          <label for="email">Email Address <span class="required">*</span></label>
          <input type="email" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required>
          <div class="error-message" id="email-error"></div>
        </div>

        <div class="form-group">
          <label for="password">Password <span class="required">*</span></label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
          <button type="button" class="password-toggle" id="togglePassword">👁️</button>
          <div class="error-message" id="password-error"></div>
        </div>

        <button type="submit" class="submit-btn">Login</button>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('loginForm');
      const togglePassword = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('password');

      togglePassword.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        togglePassword.textContent = type === 'password' ? '👁️' : '🙈';
      });

      form.addEventListener('submit', function (e) {
        let valid = true;
        clearErrors();

        const email = document.getElementById('email');
        const password = document.getElementById('password');

        if (email.value.trim() === '' || !/^\S+@\S+\.\S+$/.test(email.value)) {
          showError(email, 'Please enter a valid email address');
          valid = false;
        }

        if (password.value.trim().length < 8) {
          showError(password, 'Password must be at least 8 characters');
          valid = false;
        }

        if (!valid) {
          e.preventDefault();
        }
      });

      function showError(input, message) {
        const group = input.closest('.form-group');
        const error = group.querySelector('.error-message');
        group.classList.add('error');
        error.textContent = message;
        error.classList.add('show');
      }

      function clearErrors() {
        document.querySelectorAll('.form-group').forEach(group => {
          group.classList.remove('error');
          const error = group.querySelector('.error-message');
          if (error) {
            error.textContent = '';
            error.classList.remove('show');
          }
        });
      }
    });
  </script>
</body>
</html>

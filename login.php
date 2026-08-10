<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - FreshMart</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2fb36d;
      --primary-dark: #249458;
      --text: #1f2937;
      --muted: #6b7280;
      --bg: #f7f7f7;
      --card: #ffffff;
      --border: #e5e7eb;
      --danger: #ef4444;
      --success: #10b981;
      --shadow-color: 24 39 75;
    }
    * { box-sizing: border-box; }
    html, body { 
      margin: 0; 
      padding: 0; 
      font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; 
      color: var(--text); 
      background: var(--bg);
      min-height: 100vh;
    }
    a { color: inherit; text-decoration: none; }

    .container { 
      width: min(400px, 100% - 2rem); 
      margin: 0 auto; 
      padding: 2rem 0;
    }

    .header {
      text-align: center;
      margin-bottom: 2rem;
    }
    .brand {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 700;
      font-size: 1.5rem;
      color: var(--primary);
      margin-bottom: 0.5rem;
    }
    .brand svg { width: 32px; height: 32px; }

    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(var(--shadow-color) / 0.06);
      overflow: hidden;
    }

    .tabs {
      display: flex;
      border-bottom: 1px solid var(--border);
    }
    .tab {
      flex: 1;
      padding: 1rem;
      text-align: center;
      background: none;
      border: none;
      font-family: inherit;
      font-weight: 600;
      cursor: pointer;
      color: var(--muted);
      transition: all 0.2s;
    }
    .tab.active {
      color: var(--primary);
      border-bottom: 2px solid var(--primary);
    }
    .tab:hover:not(.active) {
      background: #f9fafb;
    }

    .tab-content {
      display: none;
      padding: 1.5rem;
    }
    .tab-content.active {
      display: block;
    }

    form {
      display: grid;
      gap: 1rem;
    }
    label {
      font-weight: 600;
      font-size: 0.9rem;
      color: var(--text);
    }
    input {
      width: 100%;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 0.75rem 1rem;
      font-family: inherit;
      font-size: 1rem;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(47, 179, 109, 0.1);
    }
    input.error {
      border-color: var(--danger);
    }

    .btn {
      width: 100%;
      border: none;
      background: var(--primary);
      color: #fff;
      padding: 0.75rem 1rem;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn:hover {
      background: var(--primary-dark);
    }
    .btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .error-message {
      color: var(--danger);
      font-size: 0.9rem;
      margin-top: 0.25rem;
    }
    .success-message {
      color: var(--success);
      font-size: 0.9rem;
      margin-top: 0.25rem;
    }

    .home-link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      color: var(--muted);
      font-weight: 600;
      margin-top: 1rem;
      padding: 0.5rem 1rem;
      border: 1px solid var(--border);
      border-radius: 10px;
      background: #fff;
    }
    .home-link:hover {
      color: var(--text);
      box-shadow: 0 4px 12px rgba(var(--shadow-color) / 0.1);
    }

    .password-toggle {
      position: relative;
    }
    .password-toggle input {
      padding-right: 3rem;
    }
    .toggle-btn {
      position: absolute;
      right: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--muted);
      cursor: pointer;
      padding: 0.25rem;
    }
    .toggle-btn:hover {
      color: var(--text);
    }
    
    textarea {
      width: 100%;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 0.75rem 1rem;
      font-family: inherit;
      font-size: 1rem;
      background: var(--background);
      color: var(--text);
      resize: vertical;
      min-height: 80px;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(47, 179, 109, 0.1);
    }
    
    textarea.error {
      border-color: var(--danger);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="brand">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M5 12c0-4.418 3.582-8 8-8a6 6 0 110 12H5z" fill="currentColor"/>
          <path d="M5 12c0 3.866 3.134 7 7 7 2.761 0 5-2.239 5-5H5z" fill="#1f8a58"/>
        </svg>
        FreshMart
      </div>
      <p style="color: var(--muted); margin: 0;">Welcome back! Please sign in to your account.</p>
    </div>

    <div class="card">
      <div class="tabs">
        <button class="tab active" data-tab="user-login">User Login</button>
        <button class="tab" data-tab="user-register">Register</button>
        <button class="tab" data-tab="admin-login">Admin</button>
      </div>

      <!-- User Login -->
      <div id="user-login" class="tab-content active">
        <form id="userLoginForm">
          <div>
            <label for="user-email">Email</label>
            <input type="email" id="user-email" name="email" placeholder="Enter your email" required>
          </div>
          <div>
            <label for="user-password">Password</label>
            <div class="password-toggle">
              <input type="password" id="user-password" name="password" placeholder="Enter your password" required>
              <button type="button" class="toggle-btn" onclick="togglePassword('user-password')">👁</button>
            </div>
          </div>
          <button type="submit" class="btn">Sign In</button>
          <div id="user-login-message"></div>
        </form>
      </div>

      <!-- User Registration -->
      <div id="user-register" class="tab-content">
        <form id="userRegisterForm">
          <div>
            <label for="reg-name">Full Name</label>
            <input type="text" id="reg-name" name="name" placeholder="Enter your full name" required>
          </div>
          <div>
            <label for="reg-email">Email</label>
            <input type="email" id="reg-email" name="email" placeholder="Enter your email" required>
          </div>
          <div>
            <label for="reg-password">Password</label>
            <div class="password-toggle">
              <input type="password" id="reg-password" name="password" placeholder="Create a password" required>
              <button type="button" class="toggle-btn" onclick="togglePassword('reg-password')">👁</button>
            </div>
          </div>
          <div>
            <label for="reg-confirm">Confirm Password</label>
            <div class="password-toggle">
              <input type="password" id="reg-confirm" name="confirm" placeholder="Confirm your password" required>
              <button type="button" class="toggle-btn" onclick="togglePassword('reg-confirm')">👁</button>
            </div>
          </div>
          <div>
            <label for="reg-phone">Phone Number</label>
            <input type="tel" id="reg-phone" name="phone" placeholder="Enter your phone number" required>
          </div>
          <div>
            <label for="reg-address">Address</label>
            <textarea id="reg-address" name="address" placeholder="Enter your full address" rows="3" required></textarea>
          </div>
          <button type="submit" class="btn">Create Account</button>
          <div id="user-register-message"></div>
        </form>
      </div>

      <!-- Admin Login -->
      <div id="admin-login" class="tab-content">
        <form id="adminLoginForm">
          <div>
            <label for="admin-username">Username</label>
            <input type="text" id="admin-username" name="username" placeholder="Enter admin username" required>
          </div>
          <div>
            <label for="admin-password">Password</label>
            <div class="password-toggle">
              <input type="password" id="admin-password" name="password" placeholder="Enter admin password" required>
              <button type="button" class="toggle-btn" onclick="togglePassword('admin-password')">👁</button>
            </div>
          </div>
          <button type="submit" class="btn">Admin Login</button>
          <div id="admin-login-message"></div>
        </form>
      </div>
    </div>

    <div style="text-align: center;">
      <p style="color: var(--muted); margin-top: 1rem; font-size: 0.9rem;">
        Welcome to FreshMart - Your Fresh Grocery Store
      </p>
    </div>
  </div>

  <script>
    // Tab switching
    document.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.tab;
        
        // Update active tab
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        // Update active content
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById(target).classList.add('active');
        
        // Clear messages
        document.querySelectorAll('[id$="-message"]').forEach(el => el.innerHTML = '');
      });
    });

    // Password toggle
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      input.type = input.type === 'password' ? 'text' : 'password';
    }

    // User registration
    document.getElementById('userRegisterForm').addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const messageEl = document.getElementById('user-register-message');
      
      const name = formData.get('name').trim();
      const email = formData.get('email').trim();
      const password = formData.get('password');
      const confirm = formData.get('confirm');
      const phone = formData.get('phone').trim();
      const address = formData.get('address').trim();
      
      // Validation
      if (password !== confirm) {
        showMessage(messageEl, 'Passwords do not match', 'error');
        return;
      }
      
      if (password.length < 6) {
        showMessage(messageEl, 'Password must be at least 6 characters', 'error');
        return;
      }
      
      if (!phone) {
        showMessage(messageEl, 'Phone number is required', 'error');
        return;
      }
      
      if (!address) {
        showMessage(messageEl, 'Address is required', 'error');
        return;
      }
      
      // Register via API
      fetch('api/user_register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, password, phone, address })
      })
      .then(res => res.json())
      .then(data => {
        if (data && data.success) {
          showMessage(messageEl, 'Account created successfully! You can now login.', 'success');
          e.target.reset();
        } else {
          showMessage(messageEl, data.error || 'Registration failed', 'error');
        }
      })
      .catch(() => showMessage(messageEl, 'Network error', 'error'));
    });

    // User login
    document.getElementById('userLoginForm').addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const messageEl = document.getElementById('user-login-message');
      
      const email = formData.get('email').trim();
      const password = formData.get('password');
      
      fetch('api/user_login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
      })
      .then(res => res.json())
      .then(data => {
        if (data && data.success) {
          showMessage(messageEl, 'Login successful! Redirecting...', 'success');
          setTimeout(() => { window.location.href = 'homepage.php'; }, 800);
        } else {
          showMessage(messageEl, data.error || 'Invalid email or password', 'error');
        }
      })
      .catch(() => showMessage(messageEl, 'Network error', 'error'));
    });

    // Admin login
    document.getElementById('adminLoginForm').addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const messageEl = document.getElementById('admin-login-message');
      
      const username = formData.get('username').trim();
      const password = formData.get('password');
      
      fetch('api/admin_login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
      })
      .then(res => res.json())
      .then(data => {
        if (data && data.success) {
          showMessage(messageEl, 'Admin login successful! Redirecting...', 'success');
          setTimeout(() => { window.location.href = 'admin.php'; }, 800);
        } else {
          showMessage(messageEl, data.error || 'Invalid admin credentials', 'error');
        }
      })
      .catch(() => showMessage(messageEl, 'Network error', 'error'));
    });

    function showMessage(element, message, type) {
      element.innerHTML = `<div class="${type}-message">${message}</div>`;
    }

    // Check if user is already logged in
    window.addEventListener('load', () => {
      // With server-side sessions we can't check here. Let users land on login.
      
      // Check if register parameter is in URL
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('register') === '1') {
        // Switch to register tab
        const registerTab = document.querySelector('[data-tab="user-register"]');
        if (registerTab) {
          registerTab.click();
        }
      }
    });
  </script>
</body>
</html> 
<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isUserLoggedIn()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - FreshMart Grocery</title>
  <meta name="description" content="Learn about FreshMart - your trusted neighborhood online grocery store delivering fresh produce and quality products.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2fb36d;
      --primary-dark: #249458;
      --accent: #ffd166;
      --text: #1f2937;
      --muted: #6b7280;
      --bg: #f7f7f7;
      --card: #ffffff;
      --border: #e5e7eb;
      --danger: #ef4444;
      --success: #10b981;
      --shadow-color: 24 39 75; /* rgb */
    }

    * { box-sizing: border-box; }

    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color: var(--text);
      background: var(--bg);
    }

    a { color: inherit; text-decoration: none; }
    img { display: block; max-width: 100%; height: auto; }
    button { font-family: inherit; }

    .container {
      width: min(1200px, 100% - 2rem);
      margin: 0 auto;
    }

    /* Header */
    header {
      position: sticky;
      top: 0;
      z-index: 50;
      background: rgba(255,255,255,0.9);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border);
    }

    .nav {
      display: grid;
      grid-template-columns: 180px 1fr 180px;
      align-items: center;
      gap: 1rem;
      padding: 0.75rem 0;
    }

    .brand {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 700;
      font-size: 1.25rem;
      color: var(--primary);
    }

    .brand-icon {
      width: 28px; height: 28px;
    }

    .nav-links {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 1rem;
      color: var(--muted);
      font-weight: 500;
    }

    .nav-links a { padding: 0.5rem 0.75rem; border-radius: 8px; }
    .nav-links a:hover { color: var(--text); background: #f1f5f9; }
    .nav-links a.active { color: var(--primary); background: rgba(47, 179, 109, 0.1); }

    .nav-actions {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 0.75rem;
    }

    .icon-btn {
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 42px; height: 42px;
      border-radius: 50%;
      border: 1px solid var(--border);
      background: #fff;
      cursor: pointer;
      transition: transform .12s ease, box-shadow .2s;
    }

    .icon-btn:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(var(--shadow-color) / 0.12); }

    .badge {
      position: absolute;
      top: -6px; right: -6px;
      min-width: 18px; height: 18px;
      padding: 0 5px;
      border-radius: 9px;
      background: var(--danger);
      color: #fff;
      font-size: 12px;
      line-height: 18px;
      text-align: center;
      font-weight: 600;
    }

    .user-menu {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.5rem 1rem;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      font-size: 0.9rem;
    }

    .user-name {
      color: var(--text);
      font-weight: 600;
    }

    .logout-btn {
      color: var(--danger);
      font-weight: 600;
      padding: 0.25rem 0.75rem;
      border-radius: 6px;
      transition: background 0.2s;
    }

    .logout-btn:hover {
      background: #fef2f2;
    }

    /* Main Content */
    main {
      padding: 2rem 0;
    }

    .page-header {
      text-align: center;
      margin-bottom: 3rem;
    }

    .page-header h1 {
      font-size: clamp(2rem, 4vw, 3rem);
      margin: 0 0 1rem;
      color: var(--text);
    }

    .page-header p {
      font-size: 1.2rem;
      color: var(--muted);
      margin: 0;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    .about-section {
      margin-bottom: 3rem;
    }

    .section-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      align-items: center;
      margin-bottom: 3rem;
    }

    .section-content h2 {
      font-size: 2rem;
      margin: 0 0 1rem;
      color: var(--text);
    }

    .section-content p {
      font-size: 1.1rem;
      line-height: 1.7;
      color: var(--muted);
      margin-bottom: 1.5rem;
    }

    .section-image {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 16px 30px rgba(var(--shadow-color) / 0.12);
    }

    .section-image img {
      width: 100%;
      height: 300px;
      object-fit: cover;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 2rem;
      margin: 3rem 0;
    }

    .stat-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 2rem 1.5rem;
      text-align: center;
      transition: transform .14s ease, box-shadow .2s;
    }

    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 16px 30px rgba(var(--shadow-color) / 0.12);
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 0.5rem;
    }

    .stat-label {
      font-size: 1rem;
      color: var(--muted);
      font-weight: 500;
    }

    .values-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 2rem;
      margin: 3rem 0;
    }

    .value-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 2rem;
      text-align: center;
    }

    .value-icon {
      width: 60px;
      height: 60px;
      background: rgba(47, 179, 109, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
    }

    .value-card h3 {
      font-size: 1.3rem;
      margin: 0 0 1rem;
      color: var(--text);
    }

    .value-card p {
      color: var(--muted);
      line-height: 1.6;
      margin: 0;
    }

    .team-section {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 3rem 2rem;
      text-align: center;
      margin: 3rem 0;
    }

    .team-section h2 {
      font-size: 2rem;
      margin: 0 0 1rem;
      color: var(--text);
    }

    .team-section p {
      font-size: 1.1rem;
      color: var(--muted);
      margin-bottom: 2rem;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      border: none;
      cursor: pointer;
      border-radius: 12px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      font-size: 0.95rem;
      transition: transform .12s ease, box-shadow .2s;
      text-decoration: none;
    }

    .btn-primary {
      background: var(--primary);
      color: #fff;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 10px 24px rgba(var(--shadow-color) / 0.12);
    }

    /* Footer */
    footer {
      border-top: 1px solid var(--border);
      background: #fff;
      margin-top: 3rem;
    }

    .footer-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 1rem;
      padding: 2rem 0;
    }

    .footer-grid h4 {
      margin: 0 0 0.5rem;
    }

    .footer-grid a {
      color: var(--muted);
      display: block;
      padding: 0.25rem 0;
    }

    .copyright {
      border-top: 1px solid var(--border);
      padding: 1rem 0;
      color: var(--muted);
      font-size: 0.9rem;
      text-align: center;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .section-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
      }
      
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .values-grid {
        grid-template-columns: 1fr;
      }
      
      .nav {
        grid-template-columns: 160px 1fr;
        grid-auto-flow: row;
        grid-template-areas: 'brand actions' 'links links';
      }
      
      .brand { grid-area: brand; }
      .nav-links { grid-area: links; justify-content: flex-start; overflow-x: auto; }
      .nav-actions { grid-area: actions; }
    }

    @media (max-width: 720px) {
      .nav {
        grid-template-columns: 1fr 1fr;
      }
      
      .nav-links {
        display: none;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .footer-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 460px) {
      .footer-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="container nav">
      <a class="brand" href="homepage.php" aria-label="FreshMart Home">
        <svg class="brand-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M5 12c0-4.418 3.582-8 8-8a6 6 0 110 12H5z" fill="currentColor"/>
          <path d="M5 12c0 3.866 3.134 7 7 7 2.761 0 5-2.239 5-5H5z" fill="#1f8a58"/>
        </svg>
        FreshMart
      </a>

      <nav class="nav-links" aria-label="Primary Navigation">
        <a href="homepage.php#categories">Categories</a>
        <a href="homepage.php#products">Products</a>
        <a href="homepage.php#deals">Deals</a>
        <a href="about.php" class="active">About</a>
        <a href="contact.php">Contact</a>
      </nav>

      <div class="nav-actions">
        <button class="icon-btn" id="cartButton" aria-label="View cart" onclick="window.location.href='cart.php'">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M6 6h15l-2 9H8L6 2H2" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="9" cy="21" r="1" fill="#111827"/>
            <circle cx="18" cy="21" r="1" fill="#111827"/>
          </svg>
          <span class="badge" id="cartCount" aria-live="polite">0</span>
        </button>
        <div class="user-menu">
          <span class="user-name">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
          <a href="login.php" class="logout-btn">Logout</a>
        </div>
      </div>
    </div>
  </header>

  <main class="container">
    <div class="page-header">
      <h1>About FreshMart</h1>
      <p>Your trusted neighborhood online grocery store, delivering fresh produce and quality products right to your doorstep.</p>
    </div>

    <div class="about-section">
      <div class="section-grid">
        <div class="section-content">
          <h2>Our Story</h2>
          <p>Founded with a simple mission to make fresh, quality groceries accessible to everyone, FreshMart has been serving communities with dedication and care. We believe that everyone deserves access to fresh produce, quality ingredients, and exceptional service.</p>
          <p>What started as a small local initiative has grown into a trusted online grocery platform, connecting customers with the freshest products from local farmers and trusted suppliers.</p>
        </div>
        <div class="section-image">
          <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1000&auto=format&fit=crop" alt="Fresh produce and groceries">
        </div>
      </div>

      <div class="section-grid">
        <div class="section-image">
          <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?q=80&w=1000&auto=format&fit=crop" alt="Modern grocery delivery">
        </div>
        <div class="section-content">
          <h2>Our Mission</h2>
          <p>To revolutionize the way people shop for groceries by providing a seamless, convenient, and reliable online shopping experience. We're committed to delivering the freshest products while supporting local communities and sustainable practices.</p>
          <p>Every order is carefully curated and delivered with the same care and attention we'd give to our own families.</p>
        </div>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number">3,000+</div>
        <div class="stat-label">Products Available</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">50,000+</div>
        <div class="stat-label">Happy Customers</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">60 min</div>
        <div class="stat-label">Average Delivery</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">4.8/5</div>
        <div class="stat-label">Customer Rating</div>
      </div>
    </div>

    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon">
          <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-10-5z" stroke="#2fb36d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 12l2 2 4-4" stroke="#2fb36d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3>Quality Assurance</h3>
        <p>Every product is carefully selected and quality-checked to ensure you receive only the freshest and best items.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">
          <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="#2fb36d" stroke-width="2"/>
            <path d="M12 6v6l4 2" stroke="#2fb36d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3>Fast Delivery</h3>
        <p>Quick and reliable delivery service ensuring your groceries reach you fresh and on time, every time.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">
          <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="#2fb36d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="7" r="4" stroke="#2fb36d" stroke-width="2"/>
          </svg>
        </div>
        <h3>Customer First</h3>
        <p>Your satisfaction is our priority. We're here to provide exceptional service and support whenever you need it.</p>
      </div>
    </div>

    <div class="team-section">
      <h2>Join Our Community</h2>
      <p>Become part of the FreshMart family and enjoy exclusive deals, early access to new products, and personalized recommendations tailored just for you.</p>
      <a href="homepage.php" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M5 12h14M13 5l7 7-7 7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Start Shopping
      </a>
    </div>
  </main>

  <footer>
    <div class="container footer-grid">
      <div>
        <h4>FreshMart</h4>
        <p class="muted">Your trusted neighborhood online grocery.</p>
      </div>
      <div>
        <h4>Shop</h4>
        <a href="homepage.php#categories">Categories</a>
        <a href="homepage.php#products">Popular</a>
        <a href="homepage.php#deals">Deals</a>
      </div>
      <div>
        <h4>Support</h4>
        <a href="#">Help Center</a>
        <a href="#">Shipping</a>
        <a href="#">Returns</a>
      </div>
      <div>
        <h4>Contact</h4>
        <a href="mailto:support@freshmart.example">support@freshmart.example</a>
        <a href="#">+1 (555) 123-4567</a>
      </div>
    </div>
    <div class="container copyright">© <span id="year"></span> FreshMart. All rights reserved.</div>
  </footer>

  <script>
    // Load cart count from database
    const userId = <?php echo getCurrentUserId() ?? 'null'; ?>;
    
    async function loadCartCount() {
      if (!userId) return;
      
      try {
        const response = await fetch(`api/cart.php?action=count&user_id=${userId}`);
        const data = await response.json();
        document.getElementById('cartCount').textContent = data.count || 0;
      } catch (error) {
        console.error('Error loading cart count:', error);
      }
    }

    // Set current year in footer
    document.getElementById('year').textContent = new Date().getFullYear();

    // Initialize cart count on page load
    loadCartCount();
  </script>
</body>
</html>

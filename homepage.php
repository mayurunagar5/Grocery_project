<?php
require_once 'config/database.php';

// Start session (no login required to view homepage)
startSession();

// Get user ID (will be null if not logged in)
$user_id = getCurrentUserId();
$is_logged_in = isUserLoggedIn();

// Load products from database
$pdo = getDBConnection();
$products = [];

if ($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT p.*, c.name as category 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1
            ORDER BY p.created_at DESC
        ");
        $products = $stmt->fetchAll();
    } catch (Exception $e) {
        // If error, use empty array
        $products = [];
    }
}

// Load categories for filtering
$categories = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
        $categories = $stmt->fetchAll();
    } catch (Exception $e) {
        $categories = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FreshMart Grocery - Fresh Food Delivered</title>
  <meta name="description" content="FreshMart Grocery - Fresh produce, dairy, bakery, meat and beverages delivered to your door.">
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
      justify-content: left;
      align-items: center;
      gap: 1rem;
      color: var(--muted);
      font-weight: 500;
    }

    .nav-links a { padding: 0.5rem 0.75rem; border-radius: 8px; }
    .nav-links a:hover { color: var(--text); background: #f1f5f9; }

    .nav-actions {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 0.75rem;
    }

    .search {
      position: relative;
      flex: 1 1 auto;
      max-width: 360px;
      margin-left: -45px;
    }

    .search input {
      width: 100%;
      border: 1px solid var(--border);
      background: #fff;
      border-radius: 12px;
      padding: 0.65rem 2.5rem 0.65rem 2.5rem;
      outline: none;
      transition: box-shadow .2s, border-color .2s;
    }

    .search input:focus {
      border-color: var(--primary);
      box-shadow: 0 8px 20px rgba(var(--shadow-color) / 0.12);
    }

    .search .icon-left, .search .icon-right {
      position: absolute;
      top: 50%; transform: translateY(-50%);
      color: var(--muted);
    }

    .search .icon-left { left: 10px; }
    .search .icon-right { right: 10px; cursor: pointer; }

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
      transform-origin: center;
    }
    .badge.pop { animation: pop 0.22s ease; }
    @keyframes pop {
      0% { transform: scale(1); }
      50% { transform: scale(1.2); }
      100% { transform: scale(1); }
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

    /* Hero */
    .hero {
      position: relative;
      overflow: hidden;
      border-bottom: 1px solid var(--border);
      background: linear-gradient(0deg, rgba(255,255,255,0.6), rgba(255,255,255,0.6)),
        url('https://images.unsplash.com/photo-1604719312566-8912e9227c6a?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Z3JvY2VyeSUyMHN0b3JlfGVufDB8fDB8fHww') center/cover no-repeat;
    }

    .hero-content {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 2rem;
      align-items: center;
      padding: clamp(2rem, 5vw, 4rem) 0;
    }

    .hero h1 {
      font-size: clamp(1.8rem, 3vw + 1rem, 3rem);
      line-height: 1.15;
      margin: 0 0 1rem 0;
    }

    .hero p {
      color: var(--muted);
      margin: 0 0 1.25rem 0;
    }

    .hero-cta {
      display: flex; gap: 0.75rem; flex-wrap: wrap;
    }

    .btn {
      display: inline-flex; align-items: center; gap: 0.5rem;
      border: none; cursor: pointer; border-radius: 12px;
      padding: 0.75rem 1rem; font-weight: 600; font-size: 0.95rem;
      transition: transform .12s ease, box-shadow .2s;
    }
    .btn-primary { background: var(--primary); color: #fff; }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 10px 24px rgba(var(--shadow-color) / 0.12); }
    .btn-secondary { background: #fff; color: var(--text); border: 1px solid var(--border); }
    .btn-secondary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(var(--shadow-color) / 0.12); }

    .hero-stats {
      display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; margin-top: 1.25rem;
    }
    .stat {
      background: rgba(255,255,255,0.85);
      border: 1px solid var(--border);
      border-radius: 12px; padding: 0.75rem 1rem;
      backdrop-filter: blur(6px);
    }
    .stat b { font-size: 1.15rem; color: var(--text); }
    .stat small { color: var(--muted); display: block; }

    /* Categories */
    section { padding: clamp(1.5rem, 4vw, 3rem) 0; }

    .section-head {
      display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; margin-bottom: 1rem;
    }
    .section-head h2 { margin: 0; font-size: 1.5rem; }
    .muted { color: var(--muted); }

    .categories {
      display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 0.85rem;
    }

    .cat-card {
      position: relative; overflow: hidden; border-radius: 14px; cursor: pointer;
      border: 1px solid var(--border); background: var(--card);
      transition: transform .14s ease, box-shadow .2s;
    }

    .cat-card:hover { transform: translateY(-3px); box-shadow: 0 16px 30px rgba(var(--shadow-color) / 0.12); }

    .cat-card img { height: 130px; width: 100%; object-fit: cover; }

    .cat-card .label {
      position: absolute; left: 10px; bottom: 10px; background: rgba(0,0,0,0.55); color: #fff; padding: 0.3rem 0.55rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600;
    }

    .chips { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .chip {
      background: #fff; border: 1px solid var(--border); color: var(--text);
      padding: 0.4rem 0.7rem; border-radius: 999px; cursor: pointer; font-weight: 600; font-size: 0.85rem;
    }
    .chip.active { background: var(--primary); color: #fff; border-color: var(--primary); }

    /* Products */
    .products {
      display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem;
    }

    .product-card {
      background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden;
      /*display: grid;*/ grid-template-rows: auto 1fr auto; transition: transform .14s ease, box-shadow .2s; cursor: default;
    }

    .product-card:hover { transform: translateY(-3px); box-shadow: 0 16px 30px rgba(var(--shadow-color) / 0.12); }

    .product-card .media { 
      aspect-ratio: 5/3; 
      background: #f3f4f6; 
      border: 1px solid #e5e7eb;
      border-radius: 8px 8px 0 0;
      overflow: hidden;
    }
    
    .product-card .media img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .product-card .body { padding: 0.75rem 0.9rem; }
    .product-card .title { font-weight: 600; margin: 0 0 0.35rem; font-size: 1rem; }
    .product-card .meta { color: var(--muted); font-size: 0.9rem; }

    .price-row { display: flex; align-items: center; justify-content: space-around; gap: 0.5rem; padding: 0.75rem 0.9rem; border-top: 1px solid var(--border); }
    .price { font-weight: 700; }

    .add-btn { background: var(--primary); color: #fff; border: none; border-radius: 10px; padding: 0.55rem 0.8rem; cursor: pointer; font-weight: 700; }
    .add-btn:hover { background: var(--primary-dark); }

    /* Custom Modal Dialog */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      opacity: 0;
      transition: opacity 0.2s ease;
    }
    .modal-overlay.show {
      opacity: 1;
    }
    .modal-dialog {
      background: #fff;
      border-radius: 12px;
      padding: 1.5rem;
      max-width: 400px;
      width: 90%;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
      transform: scale(0.9);
      transition: transform 0.2s ease;
    }
    .modal-overlay.show .modal-dialog {
      transform: scale(1);
    }
    .modal-dialog h3 {
      margin: 0 0 1rem 0;
      font-size: 1.25rem;
      color: var(--text);
    }
    .modal-dialog p {
      margin: 0 0 1.5rem 0;
      color: var(--muted);
    }
    .modal-buttons {
      display: flex;
      gap: 0.75rem;
      justify-content: flex-end;
    }
    .modal-btn {
      padding: 0.6rem 1.2rem;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.1s ease;
    }
    .modal-btn:hover {
      transform: translateY(-1px);
    }
    .modal-btn-primary {
      background: var(--primary);
      color: #fff;
    }
    .modal-btn-secondary {
      background: var(--border);
      color: var(--text);
    }

    /* Banner */
    .banner {
      background: linear-gradient(90deg, rgba(47,179,109,0.1), rgba(255,209,102,0.2));
      border: 1px dashed var(--primary);
      border-radius: 14px; padding: 1rem 1.25rem;
      display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    }

    /* Newsletter */
    .newsletter {
      background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 1.25rem;
      display: grid; grid-template-columns: 1.3fr 1fr; gap: 1rem;
    }

    .newsletter form {
      display: grid; grid-template-columns: 1fr auto; gap: 0.5rem;
    }

    .newsletter input[type="email"] {
      width: 100%; border: 1px solid var(--border); border-radius: 12px; padding: 0.7rem 0.9rem; outline: none;
    }

    .newsletter button { border-radius: 12px; }

    /* Footer */
    footer { border-top: 1px solid var(--border); background: #fff; }
    .footer-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; padding: 2rem 0; }
    .footer-grid h4 { margin: 0 0 0.5rem; }
    .footer-grid a { color: var(--muted); display: block; padding: 0.25rem 0; }
    .copyright { border-top: 1px solid var(--border); padding: 1rem 0; color: var(--muted); font-size: 0.9rem; text-align: center; }

    /* Utilities */
    .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; }

    /* Responsive */
    @media (max-width: 1024px) {
      .hero-content { grid-template-columns: 1fr; }
      .categories { grid-template-columns: repeat(3, minmax(0, 1fr)); }
      .products { grid-template-columns: repeat(3, minmax(0, 1fr)); }
      .newsletter { grid-template-columns: 1fr; }
      .nav { grid-template-columns: 160px 1fr; grid-auto-flow: row; grid-template-areas: 'brand actions' 'links links'; }
      .brand { grid-area: brand; }
      .nav-links { grid-area: links; justify-content: flex-start; overflow-x: auto; }
      .nav-actions { grid-area: actions; }
    }
    @media (max-width: 720px) {
      .nav { grid-template-columns: 1fr 1fr; }
      .nav-links { display: none; }
      .categories { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .products { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 460px) {
      .products { grid-template-columns: 1fr; }
      .footer-grid { grid-template-columns: 1fr; }
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
        <a href="#categories">Categories</a>
        <a href="#products">Products</a>
        <a href="#deals">Deals</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
      </nav>

      <div class="nav-actions">
        <div class="search" role="search">
          <span class="icon-left" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-4.35-4.35" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/><circle cx="10.5" cy="10.5" r="7.5" stroke="#94a3b8" stroke-width="2"/></svg>
          </span>
          <input id="searchInput" type="search" placeholder="Search for apples, milk, bread..." aria-label="Search products" />
          <span class="icon-right" id="clearSearch" title="Clear" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 6l12 12M18 6L6 18" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/></svg>
          </span>
        </div>
        <?php if ($is_logged_in): ?>
          <button class="icon-btn" id="cartButton" aria-label="View cart">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6 6h15l-2 9H8L6 2H2" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="21" r="1" fill="#111827"/><circle cx="18" cy="21" r="1" fill="#111827"/></svg>
            <span class="badge" id="cartCount" aria-live="polite">0</span>
          </button>
          <div class="user-menu">
            <span class="user-name">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
            <a href="api/logout.php" class="logout-btn">Logout</a>
          </div>
        <?php else: ?>
          <div class="user-menu">
            <a href="login.php" class="btn btn-primary" style="padding: 0.5rem 1rem; text-decoration: none; display: inline-flex; align-items: center;">Login</a>
            <a href="login.php?register=1" class="btn btn-secondary" style="padding: 0.5rem 1rem; text-decoration: none; display: inline-flex; align-items: center;">Register</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <section class="hero">
    <div class="container hero-content">
      <div>
        <h1>Fresh groceries, delivered fast</h1>
        <p style="color:black;font-size: 20px;font-weight: 500;">Shop farm-fresh produce, dairy, bakery, and more. Enjoy same-day delivery and great weekly deals.</p>
        <div class="hero-cta">
          <button class="btn btn-primary" id="shopNowBtn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Shop now
          </button>
          <!-- <a class="btn btn-secondary" href="#deals">View deals</a> -->
        </div>
        <div class="hero-stats">
          <div class="stat"><b>3,000+</b><small>Products available</small></div>
          <div class="stat"><b>60 min</b><small>Average delivery</small></div>
          <div class="stat"><b>4.8/5</b><small>Customer rating</small></div>
        </div>
      </div>
      <div aria-hidden="true">
        <img src="https://plus.unsplash.com/premium_photo-1686878940830-9031355ec98c?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Fresh vegetables and fruits" style="border-radius:16px; border:1px solid var(--border); box-shadow: 0 18px 40px rgba(var(--shadow-color) / 0.16);"/>
      </div>
    </div>
  </section>

  <main class="container">
    <section id="categories" aria-labelledby="categories-heading">
      <div class="section-head">
        <div>
          <h2 id="categories-heading">Shop by category</h2>
          <p class="muted">Browse our most popular grocery categories</p>
        </div>
        <div class="chips" id="categoryChips" role="tablist" aria-label="Category filters">
          <button class="chip active" data-category="all" role="tab" aria-selected="true">All</button>
          <?php foreach ($categories as $category): ?>
            <button class="chip" data-category="<?php echo htmlspecialchars($category['name']); ?>" role="tab"><?php echo htmlspecialchars($category['name']); ?></button>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="categories" aria-live="polite">
        <?php foreach ($categories as $category): ?>
          <article class="cat-card" data-category="<?php echo htmlspecialchars($category['name']); ?>" title="<?php echo htmlspecialchars($category['name']); ?>">
            <img src="<?php echo htmlspecialchars($category['image_url']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" onerror="this.src='https://via.placeholder.com/300x200?text=<?php echo urlencode($category['name']); ?>'">
            <span class="label"><?php echo htmlspecialchars($category['name']); ?></span>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section id="products" aria-labelledby="products-heading">
      <div class="section-head">
        <div>
          <h2 id="products-heading">Popular picks</h2>
          <p class="muted">Handpicked bestsellers this week</p>
        </div>
      </div>

      <div class="products" id="productGrid" aria-live="polite">
                 <?php if (empty($products)): ?>
           <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--muted);">
             <h3>No products available</h3>
             <p>Please check back later or contact admin to add products.</p>
           </div>
         <?php else: ?>
           <?php foreach ($products as $product): ?>
             <article class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
               <div class="media">
                 <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xNTAgMTAwQzE1MCAxMDAgMTUwIDEwMCAxNTAgMTAwWiIgZmlsbD0iIzlDQTNBRiIvPgo8dGV4dCB4PSIxNTAiIHk9IjExMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSIjNkI3MjgwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5ObyBJbWFnZTwvdGV4dD4KPC9zdmc+'">
               </div>
               <div class="body">
                 <h3 class="title"><?php echo htmlspecialchars($product['name']); ?></h3>
                 <div class="meta"><?php echo htmlspecialchars($product['description']); ?></div>
               </div>
               <div class="price-row">
                 <div class="price">₹<?php echo htmlspecialchars($product['price']); ?></div>
                 <button type="button" class="add-btn" data-product="<?php echo htmlspecialchars($product['name']); ?>" <?php echo ($product['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
                   <?php echo ($product['stock_quantity'] <= 0) ? 'Out of Stock' : 'Add to cart'; ?>
                 </button>
               </div>
             </article>
           <?php endforeach; ?>
         <?php endif; ?>
      </div>
    </section>

    <section id="deals" aria-labelledby="deals-heading">
      <div class="banner">
        <div>
          <h2 id="deals-heading" style="margin:0 0 0.25rem">This week’s hot deal</h2>
          <p class="muted" style="margin:0">Free delivery on orders over ₹300. Use code <b>FRESH30</b>.</p>
        </div>
        <a class="btn btn-primary" href="#products">Shop deals</a>
      </div>
    </section>

    <section aria-labelledby="newsletter-heading">
      <div class="newsletter">
        <div>
          <h2 id="newsletter-heading" style="margin-top:0">Get fresh offers in your inbox</h2>
          <p class="muted">Subscribe to our newsletter for exclusive discounts and weekly picks.</p>
        </div>
        <form id="newsletterForm" aria-label="Subscribe to newsletter">
          <label class="sr-only" for="email">Email address</label>
          <input id="email" type="email" placeholder="test123@gmail.com" required />
          <button class="btn btn-primary" type="submit">Subscribe</button>
        </form>
      </div>
    </section>
  </main>

  <footer id="contact">
    <div class="container footer-grid">
      <div>
        <h4>FreshMart</h4>
        <p class="muted">Your trusted neighborhood online grocery.</p>
      </div>
      <div>
        <h4>Shop</h4>
        <a href="#categories">Categories</a>
        <a href="#products">Popular</a>
        <a href="#deals">Deals</a>
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
    const productGridElement = document.getElementById('productGrid');
    const searchInputElement = document.getElementById('searchInput');
    const clearSearchElement = document.getElementById('clearSearch');
    const cartCountElement = document.getElementById('cartCount');
    const categoryChipsElement = document.getElementById('categoryChips');
    const shopNowButton = document.getElementById('shopNowBtn');

    // User ID from PHP
    const userId = <?php echo $user_id ? (int)$user_id : 'null'; ?>;
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

    // Cart state
    let cartItemCount = 0;

    // Custom modal function to show login prompt
    function showLoginModal() {
      // Remove existing modal if any
      const existingModal = document.getElementById('loginModal');
      if (existingModal) {
        existingModal.remove();
      }

      // Create modal
      const modal = document.createElement('div');
      modal.id = 'loginModal';
      modal.className = 'modal-overlay';
      modal.innerHTML = `
        <div class="modal-dialog">
          <h3>Login Required</h3>
          <p>You need to login or register to add items to cart. Would you like to go to the login page?</p>
          <div class="modal-buttons">
            <button class="modal-btn modal-btn-secondary" id="modalCancel">Cancel</button>
            <button class="modal-btn modal-btn-primary" id="modalConfirm">Go to Login</button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      
      // Show modal with animation
      requestAnimationFrame(() => {
        modal.classList.add('show');
      });

      // Handle cancel
      document.getElementById('modalCancel').addEventListener('click', () => {
        modal.classList.remove('show');
        setTimeout(() => modal.remove(), 200);
      });

      // Handle confirm
      document.getElementById('modalConfirm').addEventListener('click', () => {
        window.location.href = 'login.php';
      });

      // Close on overlay click
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.classList.remove('show');
          setTimeout(() => modal.remove(), 200);
        }
      });
    }

    // Load cart count from database
    async function loadCartCount() {
      if (!userId || !isLoggedIn) {
        // Hide cart badge if not logged in
        const cartCountEl = document.getElementById('cartCount');
        if (cartCountEl) {
          cartCountEl.style.display = 'none';
        }
        return;
      }
      
      try {
        const response = await fetch(`api/cart.php?action=count&user_id=${userId}`);
        const data = await response.json();
        updateCartBadge(data.count || 0);
      } catch (error) {
        console.error('Error loading cart count:', error);
      }
    }

    function updateCartBadge(newCount) {
      cartItemCount = newCount;
      cartCountElement.textContent = String(cartItemCount);
      cartCountElement.classList.add('pop');
      setTimeout(() => cartCountElement.classList.remove('pop'), 200);
    }

    // Filter products by search term and category
    function filterProducts() {
      const rawQuery = searchInputElement.value.trim().toLowerCase();
      const activeChip = categoryChipsElement.querySelector('.chip.active');
      const activeCategory = activeChip ? activeChip.dataset.category : 'all';

      const productCards = productGridElement.querySelectorAll('.product-card');
      productCards.forEach(card => {
        const productName = (card.dataset.name || '').toLowerCase();
        const productCategory = card.dataset.category;

        const matchesSearch = rawQuery === '' || productName.includes(rawQuery);
        const matchesCategory = activeCategory === 'all' || productCategory === activeCategory;

        card.style.display = matchesSearch && matchesCategory ? '' : 'none';
      });
    }

    // Attach events
    searchInputElement.addEventListener('input', filterProducts);
    clearSearchElement.addEventListener('click', () => {
      searchInputElement.value = '';
      filterProducts();
      searchInputElement.focus();
    });

    categoryChipsElement.addEventListener('click', (event) => {
      const target = event.target;
      if (!(target instanceof HTMLElement)) return;
      const chip = target.closest('.chip');
      if (!chip) return;

      categoryChipsElement.querySelectorAll('.chip').forEach(c => {
        c.classList.remove('active');
        c.setAttribute('aria-selected', 'false');
      });
      chip.classList.add('active');
      chip.setAttribute('aria-selected', 'true');
      filterProducts();

      // Smooth scroll to products on category click
      document.getElementById('products').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Add to cart interactions - use event delegation
    productGridElement.addEventListener('click', async function(event) {
      // Find the clicked button
      const btn = event.target.closest('.add-btn');
      if (!btn) return;

      // Prevent any default behavior
      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();

      // Check if button is disabled
      if (btn.disabled) {
        return;
      }

      // Check if user is logged in - show confirmation dialog if not
      const userNotLoggedIn = (!userId || userId === null || !isLoggedIn || isLoggedIn === false);
      
      if (userNotLoggedIn) {
        // Show custom modal dialog
        showLoginModal();
        return false;
      }
      
      // User is logged in, proceed with adding to cart
      const card = btn.closest('.product-card');
      if (!card) return;
      
      const nameEl = card.querySelector('.title');
      const name = nameEl ? nameEl.textContent.trim() : (btn.dataset.product || 'Item');
      
      // Get product ID from the card data attribute
      const productId = card.dataset.productId;
      
      if (!productId) {
        console.error('Product ID not found');
        alert('Error: Product ID not found');
        return;
      }

      try {
        const response = await fetch('api/cart.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            user_id: userId,
            product_id: productId,
            quantity: 1
          })
        });

        const result = await response.json();
        
        if (result.success) {
          // Reload cart count
          await loadCartCount();
          
          // Show success toast
          const toast = document.createElement('div');
          toast.textContent = `Added: ${name}`;
          Object.assign(toast.style, {
            position: 'fixed', bottom: '24px', right: '24px', background: '#10b981', color: '#fff', padding: '10px 12px',
            borderRadius: '10px', fontWeight: '600', boxShadow: '0 12px 30px rgba(24,39,75,.3)', zIndex: 100,
            opacity: '0', transform: 'translateY(8px)', transition: 'opacity .2s ease, transform .2s ease'
          });
          document.body.appendChild(toast);
          requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
          });
          setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(8px)';
            setTimeout(() => toast.remove(), 200);
          }, 1200);
        } else {
          // Check if item is already added
          if (result.already_added) {
            // Show "Already added" toast with warning color
            const toast = document.createElement('div');
            toast.textContent = `${name} is already in your cart`;
            Object.assign(toast.style, {
              position: 'fixed', bottom: '24px', right: '24px', background: '#f59e0b', color: '#fff', padding: '10px 12px',
              borderRadius: '10px', fontWeight: '600', boxShadow: '0 12px 30px rgba(24,39,75,.3)', zIndex: 100,
              opacity: '0', transform: 'translateY(8px)', transition: 'opacity .2s ease, transform .2s ease'
            });
            document.body.appendChild(toast);
            requestAnimationFrame(() => {
              toast.style.opacity = '1';
              toast.style.transform = 'translateY(0)';
            });
            setTimeout(() => {
              toast.style.opacity = '0';
              toast.style.transform = 'translateY(8px)';
              setTimeout(() => toast.remove(), 200);
            }, 2000);
          } else {
            // Show error toast
            const toast = document.createElement('div');
            toast.textContent = `Error: ${result.message}`;
            Object.assign(toast.style, {
              position: 'fixed', bottom: '24px', right: '24px', background: '#ef4444', color: '#fff', padding: '10px 12px',
              borderRadius: '10px', fontWeight: '600', boxShadow: '0 12px 30px rgba(24,39,75,.3)', zIndex: 100,
              opacity: '0', transform: 'translateY(8px)', transition: 'opacity .2s ease, transform .2s ease'
            });
            document.body.appendChild(toast);
            requestAnimationFrame(() => {
              toast.style.opacity = '1';
              toast.style.transform = 'translateY(0)';
            });
            setTimeout(() => {
              toast.style.opacity = '0';
              toast.style.transform = 'translateY(8px)';
              setTimeout(() => toast.remove(), 200);
            }, 3000);
          }
        }
      } catch (error) {
        console.error('Error adding to cart:', error);
        alert('Error adding item to cart. Please try again.');
      }
    });

    // Shop now scroll
    shopNowButton.addEventListener('click', () => {
      document.getElementById('products').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Cart button navigates to cart page (only if logged in)
    const cartButton = document.getElementById('cartButton');
    if (cartButton) {
      cartButton.addEventListener('click', () => {
        if (!isLoggedIn || !userId) {
          if (confirm('You need to login to view your cart. Would you like to go to the login page?')) {
            window.location.href = 'login.php';
          }
          return;
        }
        window.location.href = 'cart.php';
      });
    }

    // Set current year in footer
    document.getElementById('year').textContent = new Date().getFullYear();

    // Initialize cart count on page load
    loadCartCount();

    // Check authentication and update UI
    function updateAuthUI() {
      const currentUser = localStorage.getItem('currentUser');
      const currentAdmin = localStorage.getItem('currentAdmin');
      const loginLink = document.getElementById('loginLink');
      
      if (currentUser) {
        const user = JSON.parse(currentUser);
        loginLink.textContent = user.name;
        loginLink.onclick = () => {
          localStorage.removeItem('currentUser');
          window.location.reload();
        };
      } else if (currentAdmin) {
        const admin = JSON.parse(currentAdmin);
        loginLink.textContent = `Admin: ${admin.username}`;
        loginLink.onclick = () => {
          localStorage.removeItem('currentAdmin');
          window.location.reload();
        };
      } else {
        loginLink.textContent = 'Login';
        loginLink.onclick = null;
      }
    }

    // Initialize on page load
    updateAuthUI();

    // Improve keyboard focus ring on buttons when focused via keyboard only
    let lastInteractionWasKeyboard = false;
    document.addEventListener('keydown', () => { lastInteractionWasKeyboard = true; });
    document.addEventListener('mousedown', () => { lastInteractionWasKeyboard = false; });
    document.addEventListener('focusin', (e) => {
      if (lastInteractionWasKeyboard && e.target instanceof HTMLElement) {
        e.target.style.outline = '2px solid ' + getComputedStyle(document.documentElement).getPropertyValue('--primary');
        e.target.style.outlineOffset = '2px';
      }
    });
    document.addEventListener('focusout', (e) => {
      if (e.target instanceof HTMLElement) {
        e.target.style.outline = '';
        e.target.style.outlineOffset = '';
      }
    });
  </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - FreshMart</title>
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
      --warning: #f59e0b;
      --shadow-color: 24 39 75;
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

    header {
      position: sticky;
      top: 0;
      z-index: 50;
      background: #fff;
      border-bottom: 1px solid var(--border);
      box-shadow: 0 2px 8px rgba(var(--shadow-color) / 0.06);
    }
    .container { width: min(1200px, 100% - 2rem); margin: 0 auto; }
    .nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.75rem 0;
      gap: 1rem;
    }
    .brand {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 700;
      font-size: 1.25rem;
      color: var(--primary);
    }
    .brand svg { width: 28px; height: 28px; }
    .user-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .logout {
      color: var(--danger);
      border: 1px solid var(--danger);
      border-radius: 10px;
      padding: 0.45rem 0.7rem;
      font-weight: 600;
      background: #fff;
      cursor: pointer;
    }
    .logout:hover {
      background: var(--danger);
      color: #fff;
    }

    main { padding: 1.5rem 0; }
    .dashboard-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.5rem;
    }
    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    .stat-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 1rem;
      box-shadow: 0 4px 12px rgba(var(--shadow-color) / 0.06);
    }
    .stat-card h3 {
      margin: 0 0 0.5rem;
      color: var(--muted);
      font-size: 0.9rem;
      font-weight: 600;
    }
    .stat-card .value {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      border: none;
      border-radius: 10px;
      padding: 0.6rem 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-primary {
      background: var(--primary);
      color: #fff;
    }
    .btn-primary:hover {
      background: var(--primary-dark);
    }
    .btn-danger {
      background: var(--danger);
      color: #fff;
    }
    .btn-danger:hover {
      background: #dc2626;
    }
    .btn-warning {
      background: var(--warning);
      color: #fff;
    }
    .btn-warning:hover {
      background: #d97706;
    }

    .card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 14px;
      box-shadow: 0 8px 20px rgba(var(--shadow-color) / 0.06);
      overflow: hidden;
    }
    .card-header {
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .card-body {
      padding: 1.25rem;
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1rem;
    }
    .product-item {
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      background: #fff;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(var(--shadow-color) / 0.12);
    }
    .product-image {
      width: 100%;
      height: 160px;
      object-fit: cover;
      background: #f3f4f6;
      border: 1px solid #e5e7eb;
      border-radius: 8px 8px 0 0;
    }
    .product-content {
      padding: 1rem;
    }
    .product-title {
      font-weight: 600;
      margin: 0 0 0.5rem;
    }
    .product-meta {
      color: var(--muted);
      font-size: 0.9rem;
      margin-bottom: 0.75rem;
    }
    .product-actions {
      display: flex;
      gap: 0.5rem;
    }
    .product-actions button {
      padding: 0.4rem 0.8rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.85rem;
      transition: all 0.2s;
    }
    .product-actions button:first-child {
      background: var(--primary);
      color: white;
    }
    .product-actions button:first-child:hover {
      background: var(--primary-dark);
    }
    .product-actions button:last-child {
      background: var(--danger);
      color: white;
    }
    .product-actions button:last-child:hover {
      background: #dc2626;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
      pointer-events: none;
    }
    .modal.active {
      display: flex;
      pointer-events: auto;
    }
    .modal-content {
      background: #fff;
      border-radius: 16px;
      width: min(500px, 90%);
      max-height: 90vh;
      overflow-y: auto;
    }
    .modal-header {
      padding: 1.25rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .modal-body {
      padding: 1.25rem;
    }
    .close {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--muted);
    }
    .close:hover {
      color: var(--text);
    }

    form {
      display: grid;
      gap: 1rem;
    }
    label {
      font-weight: 600;
      font-size: 0.9rem;
    }
    input, textarea, select {
      width: 100%;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 0.7rem 0.9rem;
      font-family: inherit;
    }
    input:focus, textarea:focus, select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(47, 179, 109, 0.1);
    }
    .row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    /* Toast Notification Styles */
    .toast-container {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 10000;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      pointer-events: none;
    }
    .toast {
      min-width: 300px;
      max-width: 400px;
      padding: 1rem 1.25rem;
      border-radius: 12px;
      font-weight: 600;
      box-shadow: 0 12px 30px rgba(var(--shadow-color) / 0.3);
      pointer-events: auto;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      opacity: 0;
      transform: translateX(400px);
      transition: opacity 0.3s ease, transform 0.3s ease;
    }
    .toast.show {
      opacity: 1;
      transform: translateX(0);
    }
    .toast.hide {
      opacity: 0;
      transform: translateX(400px);
    }
    .toast-success {
      background: #10b981;
      color: #fff;
    }
    .toast-error {
      background: #ef4444;
      color: #fff;
    }
    .toast-warning {
      background: #f59e0b;
      color: #fff;
    }
    .toast-icon {
      width: 20px;
      height: 20px;
      flex-shrink: 0;
    }
    .toast-message {
      flex: 1;
    }
    .toast-close {
      background: none;
      border: none;
      color: inherit;
      cursor: pointer;
      font-size: 1.2rem;
      line-height: 1;
      opacity: 0.8;
      padding: 0;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .toast-close:hover {
      opacity: 1;
    }

    @media (max-width: 768px) {
      .row {
        grid-template-columns: 1fr;
      }
      .products-grid {
        grid-template-columns: 1fr;
      }
      .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }
      .toast-container {
        bottom: 16px;
        right: 16px;
        left: 16px;
      }
      .toast {
        min-width: auto;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="container nav">
      <div class="brand">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M5 12c0-4.418 3.582-8 8-8a6 6 0 110 12H5z" fill="currentColor"/>
          <path d="M5 12c0 3.866 3.134 7 7 7 2.761 0 5-2.239 5-5H5z" fill="#1f8a58"/>
        </svg>
        FreshMart Admin
      </div>
      <div class="user-info">
        <span id="adminName">Admin</span>
        <button class="logout" onclick="logout()">Logout</button>
      </div>
    </div>
  </header>

  <main class="container">
    <!-- Admin Navigation -->
    <div style="margin-bottom: 2rem; padding: 1rem; background: white; border-radius: 12px; border: 1px solid var(--border);">
      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <button id="tabProducts" class="btn" style="background: var(--primary); color: #fff;" onclick="showSection('products')">Products</button>
        <a id="tabContact" class="btn" href="admin_contact.php" style="background: var(--border); color: var(--text); text-decoration: none;">Contact Submissions</a>
        <button id="tabCategories" class="btn" style="background: var(--border); color: var(--text);" onclick="showSection('categories')">Categories</button>
        <button id="tabUsers" class="btn" style="background: var(--border); color: var(--text);" onclick="showSection('users')">Users</button>
        <button id="tabOrders" class="btn" style="background: var(--border); color: var(--text);" onclick="showSection('orders')">Orders</button>
      </div>
    </div>

    <!-- Toast notifications will appear here - placed outside sections so it's always visible -->
    <div id="toastContainer" class="toast-container"></div>

    <div id="section-products" class="section">
    <div class="dashboard-header">
      <div>
        <h1 style="margin: 0 0 0.25rem;">Product Management</h1>
        <p style="color: var(--muted); margin: 0;">Manage your grocery inventory</p>
      </div>
      <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Add Product
      </button>
    </div>

    <div class="stats">
      <div class="stat-card">
        <h3>Total Products</h3>
        <div class="value" id="totalProducts">0</div>
      </div>
      <div class="stat-card">
        <h3>Low Stock Items</h3>
        <div class="value" id="lowStock">0</div>
      </div>
      <div class="stat-card">
        <h3>Categories</h3>
        <div class="value" id="categories">0</div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 style="margin: 0;">Products</h2>
        <div style="display: flex; gap: 0.5rem;">
          <select id="categoryFilter" onchange="filterProducts()">
            <option value="">All Categories</option>
          </select>
        </div>
      </div>
      <div class="card-body">
        <div class="products-grid" id="productsGrid">
          <!-- Products will be loaded here -->
        </div>
      </div>
    </div>
    </div>
    
    <!-- Categories Management -->
    <div id="section-categories" class="section" style="display:none;">
    <div class="card" style="margin-top: 0;">
      <div class="card-header">
        <h2 style="margin:0;">Categories</h2>
        <button class="btn btn-primary" onclick="openCategoryModal()">Add Category</button>
      </div>
      <div class="card-body">
        <div id="categoriesGrid" class="products-grid">
          <!-- Categories list -->
        </div>
      </div>
    </div>
    </div>

    <!-- Users Listing -->
    <div id="section-users" class="section" style="display:none;">
    <div class="card" style="margin-top: 0;">
      <div class="card-header">
        <h2 style="margin:0;">Users</h2>
      </div>
      <div class="card-body">
        <div id="usersGrid" class="products-grid">
          <!-- Users list -->
        </div>
      </div>
    </div>
    </div>

    <!-- Orders Listing -->
    <div id="section-orders" class="section" style="display:none;">
    <div class="card" style="margin-top: 0;">
      <div class="card-header">
        <h2 style="margin:0;">Orders</h2>
      </div>
      <div class="card-body">
        <div id="ordersGrid" class="products-grid">
          <!-- Orders list -->
        </div>
      </div>
    </div>
    </div>
  </main>

  <!-- Add/Edit Product Modal -->
  <div id="productModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalTitle">Add Product</h2>
        <button class="close" onclick="closeModal()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="productForm">
          <input type="hidden" id="productId" name="id">
          <div class="row">
            <div>
              <label for="productName">Product Name</label>
              <input type="text" id="productName" name="name" placeholder="e.g., Red Apples" required>
            </div>
            <div>
              <label for="productCategory">Category</label>
              <select id="productCategory" name="category" required>
                <option value="">Select Category</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div>
              <label for="productPrice">Price (₹)</label>
              <input type="number" id="productPrice" name="price" step="0.01" placeholder="3.99" required>
            </div>
            <div>
              <label for="productStock">Stock Quantity</label>
              <input type="number" id="productStock" name="stock" placeholder="100" required>
            </div>
          </div>
          <div>
            <label for="productImage">Image URL</label>
            <input type="url" id="productImage" name="image" placeholder="https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?w=300&h=200&fit=crop" required onchange="previewImage(this.value)">
            <small style="color: var(--muted); font-size: 0.8rem;">Use a valid image URL (e.g., Unsplash, Imgur, etc.)</small>
            <div id="imagePreview" style="margin-top: 0.5rem; display: none;">
              <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid var(--border);">
            </div>
          </div>
          <div>
            <label for="productDescription">Description</label>
            <textarea id="productDescription" name="description" placeholder="Product description..." rows="3"></textarea>
          </div>
          <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
            <button type="button" class="btn" onclick="closeModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Product</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Order Details Modal (separate from product modal) -->
  <div id="orderModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="orderModalTitle">Order</h2>
        <button class="close" onclick="closeOrderModal()">&times;</button>
      </div>
      <div class="modal-body" id="orderModalBody"></div>
    </div>
  </div>

  <!-- Category Modal -->
  <div id="categoryModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="categoryModalTitle">Add Category</h2>
        <button class="close" onclick="closeCategoryModal()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="categoryForm">
          <input type="hidden" id="categoryId" name="id">
          <div>
            <label for="categoryName">Name</label>
            <input type="text" id="categoryName" name="name" placeholder="e.g., Fruits" required>
          </div>
          <div>
            <label for="categoryImage">Image URL</label>
            <input type="url" id="categoryImage" name="image_url" placeholder="https://...">
          </div>
          <div>
            <label for="categoryDescription">Description</label>
            <textarea id="categoryDescription" name="description" placeholder="Category description" rows="3"></textarea>
          </div>
          <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
            <button type="button" class="btn" onclick="closeCategoryModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Category</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Logout
    function logout() {
      fetch('api/logout.php', { method: 'POST' }).finally(() => {
        window.location.href = 'login.php';
      });
    }

    // Load products from DB
    async function loadProducts() {
      try {
        const res = await fetch('api/products.php?action=fetch');
        const products = await res.json();
        renderProducts(products);
        updateStats(products);
        // categories are loaded separately
      } catch (error) {
        console.error('Error loading products:', error);
        showAlert('Error loading products: ' + error.message, 'error');
      }
    }

    function renderProducts(products) {
      const grid = document.getElementById('productsGrid');
      const categoryFilter = document.getElementById('categoryFilter').value;
      const filtered = categoryFilter ? products.filter(p => p.category === categoryFilter) : products;

      grid.innerHTML = filtered.map(product => `
        <div class="product-item">
          <img src="${product.image_url || product.image || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xNTAgMTAwQzE1MCAxMDAgMTUwIDEwMCAxNTAgMTAwWiIgZmlsbD0iIzlDQTNBRiIvPgo8dGV4dCB4PSIxNTAiIHk9IjExMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSIjNkI3MjgwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5ObyBJbWFnZTwvdGV4dD4KPC9zdmc+'}" alt="${product.name}" class="product-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xNTAgMTAwQzE1MCAxMDAgMTUwIDEwMCAxNTAgMTAwWiIgZmlsbD0iIzlDQTNBRiIvPgo8dGV4dCB4PSIxNTAiIHk9IjExMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSIjNkI3MjgwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5ObyBJbWFnZTwvdGV4dD4KPC9zdmc+'">
          <div class="product-content">
            <h3>${product.name}</h3>
            <div>Category: ${product.category}</div>
            <div>Price: ₹${product.price}</div>
            <div>Stock: ${product.stock_quantity || product.stock} units</div>
            <div>Status: ${parseInt(product.is_active) === 1 ? 'Active' : 'Inactive'}</div>
            ${(product.stock_quantity || product.stock) < 10 ? '<div style="color:red;font-weight:bold;">Low Stock!</div>' : ''}
            <div class="product-actions">
              <button onclick="editProduct(${product.id})">Edit</button>
              ${parseInt(product.is_active) === 1
                ? `<button class=\"btn-warning\" onclick=\"toggleProductActive(${product.id}, 0)\">Set Inactive</button>`
                : `<button class=\"btn-primary\" onclick=\"toggleProductActive(${product.id}, 1)\">Set Active</button>`}
            </div>
          </div>
        </div>
      `).join('');
    }

    function updateStats(products) {
      const categories = new Set(products.map(p => p.category)).size;
      const lowStock = products.filter(p => (p.stock_quantity || p.stock) < 10).length;
      document.getElementById('totalProducts').textContent = products.length;
      document.getElementById('lowStock').textContent = lowStock;
      document.getElementById('categories').textContent = categories;
    }

    async function loadCategoriesIntoUI() {
      try {
        const res = await fetch('api/categories.php');
        const categories = await res.json();

        // Populate filter
        const filter = document.getElementById('categoryFilter');
        filter.innerHTML = `<option value="">All Categories</option>` + (Array.isArray(categories) ? categories : []).map(c => `<option value="${c.name}">${c.name}</option>`).join('');

        // Populate product form select
        const formSelect = document.getElementById('productCategory');
        formSelect.innerHTML = `<option value="">Select Category</option>` + (Array.isArray(categories) ? categories : []).map(c => `<option value="${c.name}">${c.name}</option>`).join('');

        // Render categories list
        const categoriesGrid = document.getElementById('categoriesGrid');
        categoriesGrid.innerHTML = (Array.isArray(categories) ? categories : []).map(c => `
          <div class="product-item">
            <img src="${c.image_url || ''}" alt="${c.name}" class="product-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xNTAgMTAwQzE1MCAxMDAgMTUwIDEwMCAxNTAgMTAwWiIgZmlsbD0iIzlDQTNBRiIvPgo8dGV4dCB4PSIxNTAiIHk9IjExMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSIjNkI3MjgwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5ObyBJbWFnZTwvdGV4dD4KPC9zdmc+'">
            <div class="product-content">
              <h3>${c.name}</h3>
              <div>${c.description || ''}</div>
              <div>Status: ${parseInt(c.is_active) === 1 ? 'Active' : 'Inactive'}</div>
              <div class="product-actions">
                <button onclick="editCategory(${c.id})">Edit</button>
                ${parseInt(c.is_active) === 1
                  ? `<button class=\"btn-warning\" onclick=\"toggleCategoryActive(${c.id}, 0)\">Set Inactive</button>`
                  : `<button class=\"btn-primary\" onclick=\"toggleCategoryActive(${c.id}, 1)\">Set Active</button>`}
              </div>
            </div>
          </div>
        `).join('');
      } catch (e) {
        console.error('Error loading categories', e);
      }
    }

    function filterProducts() {
      loadProducts();
    }

    // Categories modal handlers
    function openCategoryModal() {
      document.getElementById('categoryModalTitle').textContent = 'Add Category';
      document.getElementById('categoryForm').reset();
      document.getElementById('categoryId').value = '';
      document.getElementById('categoryModal').classList.add('active');
    }

    async function editCategory(id) {
      try {
        const res = await fetch(`api/categories.php?action=get&id=${id}`);
        const cat = await res.json();
        document.getElementById('categoryModalTitle').textContent = 'Edit Category';
        document.getElementById('categoryId').value = cat.id;
        document.getElementById('categoryName').value = cat.name || '';
        document.getElementById('categoryImage').value = cat.image_url || '';
        document.getElementById('categoryDescription').value = cat.description || '';
        document.getElementById('categoryModal').classList.add('active');
      } catch (e) {
        showAlert('Error loading category', 'error');
      }
    }

    function closeCategoryModal() {
      document.getElementById('categoryModal').classList.remove('active');
    }

    async function toggleCategoryActive(id, isActive) {
      try {
        const res = await fetch('api/categories.php?action=toggle_status', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, is_active: isActive })
        });
        const result = await res.json();
        if (result.success) {
          await loadCategoriesIntoUI();
          showAlert('Category status updated', 'success');
        } else {
          showAlert(result.message || 'Failed to update status', 'error');
        }
      } catch (e) {
        showAlert('Error updating category status', 'error');
      }
    }

    // Modal management
    function openAddModal() {
      document.getElementById('modalTitle').textContent = 'Add Product';
      document.getElementById('productForm').reset();
      document.getElementById('productId').value = '';
      document.getElementById('imagePreview').style.display = 'none';
      document.getElementById('productModal').classList.add('active');
    }

    async function editProduct(id) {
      try {
        const res = await fetch(`api/products.php?action=get&id=${id}`);
        const product = await res.json();

        document.getElementById('modalTitle').textContent = 'Edit Product';
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productCategory').value = product.category;
        document.getElementById('productPrice').value = product.price;
        document.getElementById('productStock').value = product.stock;
        document.getElementById('productImage').value = product.image_url || product.image;
        document.getElementById('productDescription').value = product.description || '';
        document.getElementById('productModal').classList.add('active');
        
        // Show image preview if available
        if (product.image_url || product.image) {
          previewImage(product.image_url || product.image);
        }
      } catch (error) {
        console.error('Error loading product:', error);
        showAlert('Error loading product: ' + error.message, 'error');
      }
    }

    function closeModal() {
      document.getElementById('productModal').classList.remove('active');
      document.getElementById('imagePreview').style.display = 'none';
    }

    async function toggleProductActive(id, isActive) {
      try {
        const response = await fetch('api/products.php?action=toggle_status', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, is_active: isActive })
        });
        const result = await response.json();
        if (result.success) {
          await loadProducts();
          showAlert('Product status updated', 'success');
        } else {
          showAlert(result.message || 'Failed to update status', 'error');
        }
      } catch (e) {
        showAlert('Error updating product status', 'error');
      }
    }

    // Save Product
    document.getElementById('productForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = Object.fromEntries(new FormData(e.target).entries());
      
      try {
        const response = await fetch('api/products.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
          loadProducts();
          closeModal();
          showAlert('Product saved!', 'success');
        } else {
          // Check if it's a duplicate entry
          if (result.already_exists) {
            showAlert('This product already exists. Please use a different name.', 'error');
          } else {
            showAlert('Error: ' + result.message, 'error');
          }
        }
      } catch (error) {
        showAlert('Error saving product: ' + error.message, 'error');
      }
    });

    // Save Category
    document.getElementById('categoryForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = Object.fromEntries(new FormData(e.target).entries());
      try {
        const res = await fetch('api/categories.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(formData)
        });
        
        // Parse JSON response regardless of status code
        let result;
        try {
          result = await res.json();
        } catch (parseError) {
          console.error('JSON parse error:', parseError);
          showAlert('Error: Invalid response from server', 'error');
          return;
        }
        
        console.log('Category save response:', result); // Debug log
        console.log('Response status:', res.status); // Debug log
        
        if (result.success) {
          await loadCategoriesIntoUI();
          closeCategoryModal();
          // Small delay to ensure modal is closed before showing toast
          setTimeout(() => {
            showAlert('Category saved!', 'success');
          }, 100);
        } else {
          // Check if it's a duplicate entry
          console.log('Category save failed, already_exists:', result.already_exists); // Debug log
          console.log('Result object:', JSON.stringify(result)); // Debug log
          
          // Show toast immediately - don't wait
          if (result.already_exists === true || result.already_exists === 'true') {
            console.log('Showing duplicate category toast'); // Debug log
            showAlert('This category already exists. Please use a different name.', 'error');
          } else {
            showAlert(result.message || 'Failed to save category', 'error');
          }
        }
      } catch (e) {
        console.error('Category save error:', e); // Debug log
        showAlert('Error saving category: ' + e.message, 'error');
      }
    });

    function showAlert(msg, type) {
      console.log('showAlert called:', msg, type); // Debug log
      const container = document.getElementById('toastContainer');
      if (!container) {
        // Fallback if container doesn't exist - try to create it
        console.error('Toast container not found!');
        alert(`${type.toUpperCase()}: ${msg}`); // Fallback to alert
        return;
      }

      console.log('Toast container found, creating toast'); // Debug log

      // Create toast element
      const toast = document.createElement('div');
      toast.className = `toast toast-${type}`;
      
      // Icons for different types
      const icons = {
        success: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        error: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        warning: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
      };

      toast.innerHTML = `
        <span class="toast-icon">${icons[type] || icons.success}</span>
        <span class="toast-message">${msg}</span>
        <button class="toast-close" onclick="this.parentElement.remove()" aria-label="Close">&times;</button>
      `;

      // Add to container
      container.appendChild(toast);
      console.log('Toast added to container, element:', toast); // Debug log
      console.log('Toast classes:', toast.className); // Debug log
      console.log('Toast styles:', window.getComputedStyle(toast)); // Debug log

      // Force a reflow to ensure the element is in the DOM
      void toast.offsetHeight;

      // Apply styles directly to ensure visibility
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(400px)';
      
      // Trigger animation with a small delay to ensure DOM is ready
      setTimeout(() => {
        toast.classList.add('show');
        // Force style update as backup
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
        console.log('Toast show class added and styles applied'); // Debug log
        console.log('Toast computed opacity:', window.getComputedStyle(toast).opacity); // Debug log
        console.log('Toast computed transform:', window.getComputedStyle(toast).transform); // Debug log
      }, 100);

      // Auto remove after 4 seconds
      setTimeout(() => {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => {
          if (toast.parentElement) {
            toast.remove();
          }
        }, 300);
      }, 4000);
    }

    // Image preview function
    function previewImage(url) {
      const preview = document.getElementById('imagePreview');
      const previewImg = document.getElementById('previewImg');
      
      if (url && url.trim() !== '') {
        previewImg.src = url;
        preview.style.display = 'block';
        
        // Handle image load error
        previewImg.onerror = function() {
          previewImg.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjE1MCIgdmlld0JveD0iMCAwIDIwMCAxNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMTUwIiBmaWxsPSIjRjNGNEY2Ii8+Cjx0ZXh0IHg9IjEwMCIgeT0iNzUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzZCNzI4MCIgdGV4dC1hbmNob3I9Im1pZGRsZSI+SW52YWxpZCBJbWFnZTwvdGV4dD4KPC9zdmc+';
        };
      } else {
        preview.style.display = 'none';
      }
    }

    // Close modal on background click
    document.getElementById('productModal').addEventListener('click', (e) => {
      if (e.target.id === 'productModal') closeModal();
    });

    document.getElementById('categoryModal').addEventListener('click', (e) => {
      if (e.target.id === 'categoryModal') closeCategoryModal();
    });

    // Users
    async function loadUsers() {
      try {
        const res = await fetch('api/users.php?action=list');
        const users = await res.json();
        const grid = document.getElementById('usersGrid');
        grid.innerHTML = users.map(u => `
          <div class="product-item">
            <div class="product-content">
              <h3>${u.name}</h3>
              <div class="product-meta">${u.email}</div>
              <div>Phone: ${u.phone || '-'}</div>
              <div>Address: ${u.address || '-'}</div>
              <div>Joined: ${u.created_at}</div>
            </div>
          </div>
        `).join('');
      } catch (e) {
        console.error('Error loading users', e);
      }
    }

    // Initial load
    loadProducts();
    loadCategoriesIntoUI();
    loadUsers();
    // default section is products
    function showSection(section) {
      const sections = ['products', 'categories', 'users', 'orders'];
      sections.forEach(s => {
        const el = document.getElementById(`section-${s}`);
        if (el) el.style.display = s === section ? '' : 'none';
        const tab = document.getElementById(`tab${s.charAt(0).toUpperCase() + s.slice(1)}`);
        if (tab) {
          if (s === section) {
            tab.style.background = 'var(--primary)';
            tab.style.color = '#fff';
          } else {
            tab.style.background = 'var(--border)';
            tab.style.color = 'var(--text)';
          }
        }
      });
      if (section === 'orders') loadOrders();
      if (section === 'users') loadUsers();
      if (section === 'categories') loadCategoriesIntoUI();
      if (section === 'products') loadProducts();
    }
    showSection('products');

    // Orders
    async function loadOrders() {
      try {
        const res = await fetch('api/orders.php?action=list');
        const orders = await res.json();
        const grid = document.getElementById('ordersGrid');
        grid.innerHTML = orders.map(o => `
          <div class=\"product-item\">
            <div class=\"product-content\">
              <h3>Order #${o.id}</h3>
              <div class=\"product-meta\">${o.user_name || 'Guest'} (${o.user_email || '-'})</div>
              <div>Total: ₹${o.total_amount}</div>
              <div>Status: ${o.status}</div>
              <div>Date: ${o.order_date}</div>
              <div>Items: ${o.items ?? '-'}</div>
              <div class=\"product-actions\">
                <button onclick=\"viewOrder(${o.id})\">View</button>
              </div>
            </div>
          </div>
        `).join('');
      } catch (e) {
        console.error('Error loading orders', e);
      }
    }

    async function viewOrder(id) {
      try {
        const res = await fetch(`api/orders.php?action=get&id=${id}`);
        const data = await res.json();
        if (!data || !data.order) {
          showAlert('Order not found', 'error');
          return;
        }
        const o = data.order; const items = data.items || [];

        // Populate dedicated order modal
        const orderModal = document.getElementById('orderModal');
        document.getElementById('orderModalTitle').textContent = `Order #${o.id}`;
        const bodyEl = document.getElementById('orderModalBody');
        bodyEl.innerHTML = `
          <div class=\"alert\" style=\"margin:0 0 1rem\"><b>User:</b> ${o.user_name || 'Guest'} (${o.user_email || '-'})</div>
          <div class=\"row\">
            <div>
              <label>Status</label>
              <input type=\"text\" value=\"${o.status}\" disabled>
            </div>
            <div>
              <label>Total Amount</label>
              <input type=\"text\" value=\"₹${o.total_amount}\" disabled>
            </div>
          </div>
          <div class=\"row\">
            <div>
              <label>Order Date</label>
              <input type=\"text\" value=\"${o.order_date}\" disabled>
            </div>
            <div>
              <label>Delivery Address</label>
              <input type=\"text\" value=\"${o.delivery_address || ''}\" disabled>
            </div>
          </div>
          <div style=\"margin: 1rem 0;\"><b>Items</b></div>
          <div>
            ${items.map(it => `
              <div style=\"border:1px solid var(--border);border-radius:10px;padding:0.75rem;margin-bottom:0.5rem;\">
                <div style=\"display:flex;gap:0.75rem;align-items:center;\">
                  <img src=\"${it.image_url || ''}\" alt=\"${it.name}\" style=\"width:64px;height:64px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;\" onerror=\"this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIGZpbGw9IiNGM0Y0RjYiLz48dGV4dCB4PSIzMiIgeT0iMzYiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM2QjcyODAiIHRleHQtYW5jaG9yPSJtaWRkbGUiPkl0ZW08L3RleHQ+PC9zdmc+'\"> 
                  <div>
                    <div><b>${it.name}</b></div>
                    <div>Qty: ${it.quantity} × ₹${it.price} = ₹${(parseFloat(it.quantity) * parseFloat(it.price)).toFixed(2)}</div>
                  </div>
                </div>
              </div>
            `).join('')}
          </div>
          <div style=\"display:flex;justify-content:flex-end;gap:0.5rem;margin-top:1rem;\">
            <button class=\"btn\" onclick=\"closeOrderModal()\">Close</button>
          </div>
        `;
        orderModal.classList.add('active');
      } catch (e) {
        showAlert('Error loading order', 'error');
      }
    }

    function closeOrderModal() {
      document.getElementById('orderModal').classList.remove('active');
    }

    document.getElementById('orderModal').addEventListener('click', (e) => {
      if (e.target.id === 'orderModal') closeOrderModal();
    });

    // Load orders on demand via showSection
  </script>
</body>
</html>

<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isUserLoggedIn()) {
    redirect('login.php');
}

$user_id = getCurrentUserId();
$pdo = getDBConnection();
$cart_items = [];
$user_address = '';

if ($pdo) {
    try {
        // Get cart items
        $stmt = $pdo->prepare("
            SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image_url, p.stock_quantity
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll();
        
        // Get user address for pre-filling
        $user_stmt = $pdo->prepare("SELECT address FROM users WHERE id = ?");
        $user_stmt->execute([$user_id]);
        $user_data = $user_stmt->fetch();
        $user_address = $user_data['address'] ?? '';
    } catch (Exception $e) {
        $cart_items = [];
    }
}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - FreshMart</title>
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
        }
        
        .container {
            width: min(1200px, 100% - 2rem);
            margin: 0 auto;
        }
        
        header {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
        }
        
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary);
        }
        
        .back-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        
        main {
            padding: 2rem 0;
        }
        
        .cart-header {
            margin-bottom: 2rem;
        }
        
        .cart-header h1 {
            margin: 0 0 0.5rem;
            font-size: 2rem;
        }
        
        .cart-empty {
            text-align: center;
            padding: 3rem;
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        
        .cart-items {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: auto 1fr auto auto;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            align-items: center;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .item-details h3 {
            margin: 0 0 0.25rem;
            font-size: 1.1rem;
        }
        
        .item-price {
            color: var(--muted);
            font-weight: 600;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quantity-btn {
            background: var(--border);
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .quantity-btn:hover {
            background: var(--muted);
            color: #fff;
        }
        
        .quantity-display {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
        }
        
        .remove-btn {
            background: var(--danger);
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .remove-btn:hover {
            background: #dc2626;
        }
        
        .cart-summary {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 1.5rem;
            margin-top: 1rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .summary-row.total {
            border-top: 1px solid var(--border);
            padding-top: 1rem;
            margin-top: 1rem;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .checkout-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 1rem;
        }
        
        .checkout-btn:hover {
            background: var(--primary-dark);
        }
        
        .checkout-btn:disabled {
            background: var(--muted);
            cursor: not-allowed;
        }
        
        /* Modal Styles */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: #fff;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        
        .modal-header h2 {
            margin: 0;
            color: var(--text);
        }
        
        .close {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--muted);
        }
        
        .close:hover {
            color: var(--text);
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text);
        }
        
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
        }
        
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(47, 179, 109, 0.1);
        }
        
        .order-summary {
            background: var(--bg);
            padding: 1rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }
        
        .order-summary h3 {
            margin: 0 0 1rem;
            color: var(--text);
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .btn-secondary {
            background: var(--muted);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            flex: 1;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .btn-primary {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            flex: 1;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-primary:disabled {
            background: var(--muted);
            cursor: not-allowed;
        }
        
        @media (max-width: 768px) {
            .cart-item {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .quantity-controls {
                justify-content: center;
            }
            
            .modal-content {
                width: 95%;
                margin: 1rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
    }
  </style>
</head>
<body>
  <header>
    <div class="container nav">
            <a href="homepage.php" class="brand">FreshMart</a>
            <button class="back-btn" onclick="window.location.href='homepage.php'">← Back to Shop</button>
    </div>
  </header>

  <main class="container">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
            <p>Review your items and proceed to checkout</p>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="cart-empty">
                <h2>Your cart is empty</h2>
                <p>Add some products to your cart to get started!</p>
                <button class="checkout-btn" onclick="window.location.href='homepage.php'">Continue Shopping</button>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                                         <div class="cart-item" data-cart-id="<?php echo $item['cart_id']; ?>">
                         <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik00MCA0MEM0MCA0MCA0MCA0MCA0MCA0MFoiIGZpbGw9IiM5Q0EzQUYiLz4KPHRleHQgeD0iNDAiIHk9IjQ1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM2QjcyODAiIHRleHQtYW5jaG9yPSJtaWRkbGUiPk5vIEltYWdlPC90ZXh0Pgo8L3N2Zz4='">
                        
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <div class="item-price">₹<?php echo htmlspecialchars($item['price']); ?></div>
                        </div>
                        
                        <div class="quantity-controls">
                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, -1)">-</button>
                            <span class="quantity-display"><?php echo $item['quantity']; ?></span>
                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 1)">+</button>
                        </div>
                        
                        <button class="remove-btn" onclick="removeItem(<?php echo $item['cart_id']; ?>)">Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>₹<?php echo number_format($total, 2); ?></span>
        </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>Free</span>
        </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>₹<?php echo number_format($total, 2); ?></span>
                </div>
                
                <button class="checkout-btn" onclick="showCheckoutForm()">Proceed to Checkout</button>
    </div>
        <?php endif; ?>
        
        <!-- Checkout Modal -->
        <div id="checkoutModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Checkout</h2>
                    <span class="close" onclick="closeCheckoutModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="checkoutForm">
                        <div class="form-group">
                            <label for="deliveryAddress">Delivery Address *</label>
                            <textarea id="deliveryAddress" name="delivery_address" rows="4" placeholder="Enter your complete delivery address" required><?php echo htmlspecialchars($user_address); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="paymentMethod">Payment Method *</label>
                            <select id="paymentMethod" name="payment_method" required>
                                <option value="">Select payment method</option>
                                <option value="cash_on_delivery">Cash on Delivery</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="upi">UPI</option>
                                <option value="net_banking">Net Banking</option>
                            </select>
                        </div>
                        
                        <div class="order-summary">
                            <h3>Order Summary</h3>
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>₹<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span>₹<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="closeCheckoutModal()">Cancel</button>
                            <button type="submit" class="btn-primary">Place Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
  </main>

  <script>
        const userId = <?php echo $user_id ?? 'null'; ?>;
        
        async function updateQuantity(cartId, change) {
            try {
                const response = await fetch('api/cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        cart_id: cartId,
                        change: change
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error updating quantity: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating quantity');
            }
        }
        
        async function removeItem(cartId) {
            if (!confirm('Remove this item from cart?')) return;
            
            try {
                const response = await fetch('api/cart.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        cart_id: cartId
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error removing item: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error removing item');
            }
        }
        
        function showCheckoutForm() {
            document.getElementById('checkoutModal').style.display = 'flex';
        }
        
        function closeCheckoutModal() {
            document.getElementById('checkoutModal').style.display = 'none';
        }
        
        // Handle checkout form submission
        document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const deliveryAddress = formData.get('delivery_address').trim();
            const paymentMethod = formData.get('payment_method');
            
            if (!deliveryAddress) {
                alert('Please enter delivery address');
                return;
            }
            
            if (!paymentMethod) {
                alert('Please select payment method');
                return;
            }
            
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
            
            try {
                const response = await fetch('api/checkout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        delivery_address: deliveryAddress,
                        payment_method: paymentMethod
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    closeCheckoutModal();
                    // Redirect to order confirmation page
                    window.location.href = `order_confirmation.php?order_id=${result.order_id}`;
                } else {
                    alert('Error placing order: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error placing order. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Place Order';
            }
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            const modal = document.getElementById('checkoutModal');
            if (e.target === modal) {
                closeCheckoutModal();
            }
        });
  </script>
</body>
</html> 
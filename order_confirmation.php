<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isUserLoggedIn()) {
    redirect('login.php');
}

$user_id = getCurrentUserId();
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    redirect('homepage.php');
}

$pdo = getDBConnection();
$order = null;
$order_items = [];

if ($pdo) {
    try {
        // Get order details
        $order_stmt = $pdo->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email 
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ? AND o.user_id = ?
        ");
        $order_stmt->execute([$order_id, $user_id]);
        $order = $order_stmt->fetch();
        
        if ($order) {
            // Get order items
            $items_stmt = $pdo->prepare("
                SELECT oi.*, p.name as product_name, p.image_url
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $items_stmt->execute([$order_id]);
            $order_items = $items_stmt->fetchAll();
        }
    } catch (Exception $e) {
        // Handle error
    }
}

if (!$order) {
    redirect('homepage.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - FreshMart</title>
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
            width: min(800px, 100% - 2rem);
            margin: 0 auto;
            padding: 2rem 0;
        }
        
        .success-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
        }
        
        .order-details {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .order-items {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .item-price {
            color: var(--muted);
        }
        
        .item-quantity {
            font-weight: 600;
        }
        
        .actions {
            text-align: center;
        }
        
        .btn {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 0 0.5rem;
        }
        
        .btn:hover {
            background: var(--primary-dark);
        }
        
        .btn-secondary {
            background: var(--muted);
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .detail-row.total {
            border-top: 1px solid var(--border);
            padding-top: 1rem;
            margin-top: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-card">
            <div class="success-icon">✓</div>
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for your order. We'll process it and deliver it to you soon.</p>
            <p><strong>Order ID: #<?php echo $order['id']; ?></strong></p>
        </div>
        
        <div class="order-details">
            <h2>Order Details</h2>
            <div class="detail-row">
                <span>Order Date:</span>
                <span><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></span>
            </div>
            <div class="detail-row">
                <span>Status:</span>
                <span style="text-transform: capitalize; color: var(--primary);"><?php echo $order['status']; ?></span>
            </div>
            <div class="detail-row">
                <span>Payment Method:</span>
                <span style="text-transform: capitalize;"><?php echo str_replace('_', ' ', $order['payment_method']); ?></span>
            </div>
            <div class="detail-row">
                <span>Delivery Address:</span>
                <span><?php echo htmlspecialchars($order['delivery_address']); ?></span>
            </div>
            <div class="detail-row total">
                <span>Total Amount:</span>
                <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>
        
        <div class="order-items">
            <h2>Order Items</h2>
            <?php foreach ($order_items as $item): ?>
                <div class="order-item">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="item-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0zMCAzMEMzMCAzMCAzMCAzMCAzMCAzMFoiIGZpbGw9IiM5Q0EzQUYiLz4KPHRleHQgeD0iMzAiIHk9IjM1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTAiIGZpbGw9IiM2QjcyODAiIHRleHQtYW5jaG9yPSJtaWRkbGUiPk5vIEltYWdlPC90ZXh0Pgo8L3N2Zz4='">
                    <div class="item-details">
                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        <div class="item-price">₹<?php echo number_format($item['price'], 2); ?> each</div>
                    </div>
                    <div class="item-quantity">Qty: <?php echo $item['quantity']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="actions">
            <a href="homepage.php" class="btn">Continue Shopping</a>
            <a href="cart.php" class="btn btn-secondary">View Cart</a>
        </div>
    </div>
</body>
</html>

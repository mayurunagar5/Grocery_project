<?php
// api/checkout.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once "../config/database.php";

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

// Check if user is logged in
if (!isUserLoggedIn()) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid JSON data"]);
    exit;
}

// Validate required fields
$delivery_address = sanitizeInput($input['delivery_address'] ?? '');
$payment_method = sanitizeInput($input['payment_method'] ?? '');

if (empty($delivery_address)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Delivery address is required"]);
    exit;
}

if (empty($payment_method)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Payment method is required"]);
    exit;
}

$user_id = getCurrentUserId();
$pdo = getDBConnection();

if (!$pdo) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Get cart items for the user
    $cart_stmt = $pdo->prepare("
        SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.stock_quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $cart_stmt->execute([$user_id]);
    $cart_items = $cart_stmt->fetchAll();
    
    if (empty($cart_items)) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Cart is empty"]);
        exit;
    }
    
    // Calculate total amount
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // Check stock availability
    foreach ($cart_items as $item) {
        if ($item['stock_quantity'] < $item['quantity']) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode([
                "success" => false, 
                "message" => "Insufficient stock for product: " . $item['name'] . ". Available: " . $item['stock_quantity']
            ]);
            exit;
        }
    }
    
    // Create order
    $order_stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, status, delivery_address, payment_method) 
        VALUES (?, ?, 'pending', ?, ?)
    ");
    $order_stmt->execute([$user_id, $total_amount, $delivery_address, $payment_method]);
    $order_id = $pdo->lastInsertId();
    
    // Create order items and update stock
    foreach ($cart_items as $item) {
        // Insert order item
        $order_item_stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ");
        $order_item_stmt->execute([
            $order_id, 
            $item['product_id'], 
            $item['quantity'], 
            $item['price']
        ]);
        
        // Update product stock
        $stock_stmt = $pdo->prepare("
            UPDATE products 
            SET stock_quantity = stock_quantity - ? 
            WHERE id = ?
        ");
        $stock_stmt->execute([$item['quantity'], $item['product_id']]);
    }
    
    // Clear cart
    $clear_cart_stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear_cart_stmt->execute([$user_id]);
    
    // Commit transaction
    $pdo->commit();
    
    // Log the order
    logActivity($user_id, 'order_created', "Order #$order_id created with total amount: ₹$total_amount");
    
    // Return success response
    echo json_encode([
        "success" => true,
        "message" => "Order placed successfully!",
        "order_id" => $order_id,
        "total_amount" => $total_amount,
        "order_date" => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Order processing failed: " . $e->getMessage()
    ]);
}
?>



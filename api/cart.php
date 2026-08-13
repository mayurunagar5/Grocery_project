khklb
CjxwYXRoIGQ9Ik00MCA0MEM0MCA0MCA0MCA0MCA0MCA0MFoiIGZpbGw9IiM5Q0EzQUYiLz4KPHRleHQgeD0iNDAiIHk9IjQ1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM2QjcyODAiIHRleHQtYW5jaG9yPSJtaWRkbGUiPk5vIEltYWdlPC90ZXh0Pgo8L3N2Zz4

mb[<?php
// api/cart.php
header("Content-Type: application/json");
require_once "../db.php";

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        echo json_encode(["success" => false, "message" => "Invalid data"]);
        exit;
    }
    
    // Handle quantity update for existing cart items
    if (isset($data['cart_id']) && isset($data['change'])) {
        $cart_id = intval($data['cart_id']);
        $change = intval($data['change']);
        
        try {
            // Get current quantity
            $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE id = ?");
            $stmt->execute([$cart_id]);
            $current = $stmt->fetch();
            
            if (!$current) {
                echo json_encode(["success" => false, "message" => "Cart item not found"]);
                exit;
            }
            
            $new_quantity = max(1, $current['quantity'] + $change);
            
            // Update quantity
            $update_stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $update_stmt->execute([$new_quantity, $cart_id]);
            
            echo json_encode(["success" => true, "message" => "Quantity updated", "quantity" => $new_quantity]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
        exit;
    }
    
    // Handle adding new items to cart
    $user_id = $data['user_id'] ?? null;
    $product_id = $data['product_id'] ?? null;
    $quantity = $data['quantity'] ?? 1;
    
    if (!$user_id || !$product_id) {
        echo json_encode(["success" => false, "message" => "User ID and Product ID are required"]);
        exit;
    }
    
    try {
        // Check if item already exists in cart
        $check_stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $check_stmt->execute([$user_id, $product_id]);
        $existing_item = $check_stmt->fetch();
        
        if ($existing_item) {
            // Item already exists - return message instead of updating
            echo json_encode([
                "success" => false, 
                "message" => "This item is already in your cart",
                "already_added" => true,
                "quantity" => $existing_item['quantity']
            ]);
        } else {
            // Add new item
            $insert_stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert_stmt->execute([$user_id, $product_id, $quantity]);
            echo json_encode(["success" => true, "message" => "Item added to cart"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    
} elseif ($method === "GET") {
    if ($action === "count") {
        $user_id = $_GET['user_id'] ?? null;
        
        if (!$user_id) {
            echo json_encode(["count" => 0]);
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            echo json_encode(["count" => intval($result['total'] ?? 0)]);
        } catch (Exception $e) {
            echo json_encode(["count" => 0, "error" => $e->getMessage()]);
        }
    } elseif ($action === "items") {
        $user_id = $_GET['user_id'] ?? null;
        
        if (!$user_id) {
            echo json_encode([]);
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("
                SELECT c.id, c.quantity, p.name, p.price, p.image_url, p.stock_quantity
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$user_id]);
            $items = $stmt->fetchAll();
            echo json_encode($items);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    
} elseif ($method === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);
    $cart_id = $data['cart_id'] ?? null;
    
    if (!$cart_id) {
        echo json_encode(["success" => false, "message" => "Cart ID is required"]);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->execute([$cart_id]);
        echo json_encode(["success" => true, "message" => "Item removed from cart"]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>

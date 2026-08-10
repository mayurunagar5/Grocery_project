<?php
// api/products.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output

require_once "../db.php";

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Ensure we always return JSON
function returnJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

if ($method === "POST") {
    // Handle different POST actions
    if ($action === "delete") {
        // Delete product
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!$data || !isset($data["id"])) {
            returnJson(["success" => false, "message" => "Product ID is required"], 400);
        }
        
        $id = intval($data["id"]);
        
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                returnJson(["success" => true, "message" => "Product deleted successfully"]);
            } else {
                returnJson(["success" => false, "message" => "Product not found"], 404);
            }
        } catch (Exception $e) {
            returnJson(["success" => false, "message" => "Error deleting product: " . $e->getMessage()], 500);
        }
    } elseif ($action === "toggle_status") {
        // Toggle product active status
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data["id"])) {
            returnJson(["success" => false, "message" => "Product ID is required"], 400);
        }
        $id = intval($data["id"]);
        $is_active = isset($data['is_active']) ? (intval($data['is_active']) ? 1 : 0) : null;
        if ($is_active === null) {
            returnJson(["success" => false, "message" => "is_active is required"], 400);
        }
        try {
            $stmt = $pdo->prepare("UPDATE products SET is_active = ? WHERE id = ?");
            $stmt->execute([$is_active, $id]);
            returnJson(["success" => true, "message" => "Status updated"]);
        } catch (Exception $e) {
            returnJson(["success" => false, "message" => $e->getMessage()], 500);
        }
    } else {
        // Add or update product
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            returnJson(["success" => false, "message" => "Invalid data"], 400);
        }

        $id = isset($data["id"]) && $data["id"] !== "" ? intval($data["id"]) : null;
        $name = $data["name"];
        $description = $data["description"] ?? '';
        $price = $data["price"];
        $stock = $data["stock"];
        $category = $data["category"]; // category name
        $image_url = $data["image"];

        try {
            // Get category_id from category name
            $cat_stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
            $cat_stmt->execute([$category]);
            $category_result = $cat_stmt->fetch();
            
            if (!$category_result) {
                // Create category if it doesn't exist
                $cat_insert = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                $cat_insert->execute([$category, "Category for " . $category]);
                $category_id = $pdo->lastInsertId();
            } else {
                $category_id = $category_result['id'];
            }

            if ($id) {
                // Update - check if name already exists for another product
                $check_stmt = $pdo->prepare("SELECT id FROM products WHERE name = ? AND id != ?");
                $check_stmt->execute([$name, $id]);
                $existing = $check_stmt->fetch();
                
                if ($existing) {
                    returnJson(["success" => false, "message" => "A product with this name already exists", "already_exists" => true], 400);
                }
                
                // Update
                $stmt = $pdo->prepare("UPDATE products 
                    SET name=?, description=?, price=?, stock_quantity=?, category_id=?, image_url=? 
                    WHERE id=?");
                $stmt->execute([$name, $description, $price, $stock, $category_id, $image_url, $id]);
                returnJson(["success" => true, "message" => "Product updated"]);
            } else {
                // Insert - check if product with same name already exists
                $check_stmt = $pdo->prepare("SELECT id FROM products WHERE name = ?");
                $check_stmt->execute([$name]);
                $existing = $check_stmt->fetch();
                
                if ($existing) {
                    returnJson(["success" => false, "message" => "A product with this name already exists", "already_exists" => true], 400);
                }
                
                // Insert
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock_quantity, category_id, image_url, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$name, $description, $price, $stock, $category_id, $image_url]);
                returnJson(["success" => true, "message" => "Product added"]);
            }
        } catch (Exception $e) {
            // Check if error is due to duplicate entry
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || strpos($e->getMessage(), 'UNIQUE constraint') !== false) {
                returnJson(["success" => false, "message" => "A product with this name already exists", "already_exists" => true], 400);
            }
            returnJson(["success" => false, "message" => $e->getMessage()], 500);
        }
    }

} elseif ($method === "GET") {
    if ($action === "fetch") {
        // Get all products with category names
        $stmt = $pdo->query("
            SELECT p.*, c.name as category 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.created_at DESC
        ");
        returnJson($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($action === "get") {
        // Get single product
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("
            SELECT p.*, c.name as category 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        returnJson($product);
    } else {
        // Get all products (default)
        $stmt = $pdo->query("
            SELECT p.*, c.name as category 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.created_at DESC
        ");
        returnJson($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    
} elseif ($method === "DELETE") {
    // Handle DELETE method (alternative way to delete)
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data || !isset($data["id"])) {
        returnJson(["success" => false, "message" => "Product ID is required"], 400);
    }
    
    $id = intval($data["id"]);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            returnJson(["success" => true, "message" => "Product deleted successfully"]);
        } else {
            returnJson(["success" => false, "message" => "Product not found"], 404);
        }
    } catch (Exception $e) {
        returnJson(["success" => false, "message" => "Error deleting product: " . $e->getMessage()], 500);
    }
}
?>

<?php
// api/categories.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../db.php"; // provides $pdo

$ensureSql = "ALTER TABLE categories ADD COLUMN is_active TINYINT(1) DEFAULT 1";
try {
    // Best-effort migration: add is_active if it doesn't exist
    $pdo->exec($ensureSql);
} catch (Throwable $e) {
    // ignore if already exists or insufficient privileges
}

// Backfill: ensure existing rows default to active where null
try {
    $pdo->exec("UPDATE categories SET is_active = 1 WHERE is_active IS NULL");
} catch (Throwable $e) {
    // ignore
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

function json($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

if ($method === 'GET') {
    if ($action === 'get') {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) json(['success' => false, 'message' => 'Invalid id'], 400);
        $stmt = $pdo->prepare("SELECT id, name, description, image_url, is_active, created_at FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch(PDO::FETCH_ASSOC);
        json($cat ?: []);
    }

    // default: fetch all
    $stmt = $pdo->query("SELECT id, name, description, image_url, is_active, created_at FROM categories ORDER BY name");
    json($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($method === 'POST') {
    if ($action === 'toggle_status') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id'])) {
            json(['success' => false, 'message' => 'Category ID is required'], 400);
        }
        $id = intval($data['id']);
        $is_active = isset($data['is_active']) ? (intval($data['is_active']) ? 1 : 0) : null;
        if ($is_active === null) {
            json(['success' => false, 'message' => 'is_active is required'], 400);
        }
        try {
            $stmt = $pdo->prepare("UPDATE categories SET is_active = ? WHERE id = ?");
            $stmt->execute([$is_active, $id]);
            json(['success' => true, 'message' => 'Status updated']);
        } catch (Throwable $e) {
            json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    if ($action === 'delete') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id'])) {
            json(['success' => false, 'message' => 'Category ID is required'], 400);
        }
        $id = intval($data['id']);
        try {
            // Set products in this category to NULL before deleting (ON DELETE SET NULL should handle, but be explicit)
            $stmt = $pdo->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
            $stmt->execute([$id]);

            $del = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $del->execute([$id]);
            if ($del->rowCount() > 0) {
                json(['success' => true, 'message' => 'Category deleted']);
            }
            json(['success' => false, 'message' => 'Category not found'], 404);
        } catch (Throwable $e) {
            json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // create or update
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) json(['success' => false, 'message' => 'Invalid data'], 400);

    $id = isset($data['id']) && $data['id'] !== '' ? intval($data['id']) : null;
    $name = trim($data['name'] ?? '');
    $description = trim($data['description'] ?? '');
    $image_url = trim($data['image_url'] ?? '');
    if ($name === '') json(['success' => false, 'message' => 'Name is required'], 400);

    try {
        // Normalize name for comparison (lowercase, trimmed)
        $normalized_name = strtolower(trim($name));
        
        if ($id) {
            // Update - check if name already exists for another category (case-insensitive)
            $check_stmt = $pdo->prepare("SELECT id, name FROM categories WHERE id != ?");
            $check_stmt->execute([$id]);
            $all_categories = $check_stmt->fetchAll();
            
            foreach ($all_categories as $cat) {
                if (strtolower(trim($cat['name'])) === $normalized_name) {
                    json(['success' => false, 'message' => 'A category with this name already exists', 'already_exists' => true], 400);
                }
            }
            
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$name, $description, $image_url, $id]);
            json(['success' => true, 'message' => 'Category updated']);
        } else {
            // Insert - check if category with same name already exists (case-insensitive)
            $check_stmt = $pdo->prepare("SELECT id, name FROM categories");
            $check_stmt->execute();
            $all_categories = $check_stmt->fetchAll();
            
            foreach ($all_categories as $cat) {
                if (strtolower(trim($cat['name'])) === $normalized_name) {
                    json(['success' => false, 'message' => 'A category with this name already exists', 'already_exists' => true], 400);
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO categories (name, description, image_url, is_active) VALUES (?, ?, ?, 1)");
            $stmt->execute([$name, $description, $image_url]);
            json(['success' => true, 'message' => 'Category added', 'id' => $pdo->lastInsertId()]);
        }
    } catch (Throwable $e) {
        // Check if error is due to duplicate entry
        if (strpos($e->getMessage(), 'Duplicate entry') !== false || strpos($e->getMessage(), 'UNIQUE constraint') !== false) {
            json(['success' => false, 'message' => 'A category with this name already exists', 'already_exists' => true], 400);
        }
        json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['id'])) json(['success' => false, 'message' => 'Category ID is required'], 400);
    $id = intval($data['id']);
    try {
        $stmt = $pdo->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
        $stmt->execute([$id]);
        $del = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $del->execute([$id]);
        if ($del->rowCount() > 0) json(['success' => true, 'message' => 'Category deleted']);
        json(['success' => false, 'message' => 'Category not found'], 404);
    } catch (Throwable $e) {
        json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
    }
}

json(['success' => false, 'message' => 'Method not allowed'], 405);
?>





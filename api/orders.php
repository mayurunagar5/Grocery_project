<?php
// api/orders.php - list orders and fetch order details
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../db.php"; // $pdo

function json($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'GET') {
    json(['success' => false, 'message' => 'Method not allowed'], 405);
}

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    // Basic order list with user info and items count
    $stmt = $pdo->query("SELECT o.id, o.user_id, u.name AS user_name, u.email AS user_email, o.total_amount, o.status, o.order_date
                         FROM orders o
                         LEFT JOIN users u ON u.id = o.user_id
                         ORDER BY o.order_date DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Optionally include item counts
    try {
        $ids = array_map(fn($o) => intval($o['id']), $orders);
        if ($ids) {
            $in  = implode(',', array_fill(0, count($ids), '?'));
            $q   = $pdo->prepare("SELECT order_id, COUNT(*) AS item_count FROM order_items WHERE order_id IN ($in) GROUP BY order_id");
            $q->execute($ids);
            $counts = [];
            foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $counts[intval($row['order_id'])] = intval($row['item_count']);
            }
            foreach ($orders as &$o) {
                $o['items'] = $counts[intval($o['id'])] ?? 0;
            }
        }
    } catch (Throwable $e) {
        // ignore counts if fails
    }

    json($orders);
}

if ($action === 'get') {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) json(['success' => false, 'message' => 'Invalid order id'], 400);

    // Order core + user
    $stmt = $pdo->prepare("SELECT o.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone, u.address AS user_address
                           FROM orders o
                           LEFT JOIN users u ON u.id = o.user_id
                           WHERE o.id = ? LIMIT 1");
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) json(['success' => false, 'message' => 'Order not found'], 404);

    // Items
    $itemsStmt = $pdo->prepare("SELECT oi.id, oi.product_id, p.name, p.image_url, oi.quantity, oi.price
                                FROM order_items oi
                                LEFT JOIN products p ON p.id = oi.product_id
                                WHERE oi.order_id = ?");
    $itemsStmt->execute([$id]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    json(['order' => $order, 'items' => $items]);
}

json(['success' => false, 'message' => 'Invalid action'], 400);
?>




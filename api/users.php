<?php
// api/users.php - list users for admin
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../db.php"; // $pdo

$method = $_SERVER['REQUEST_METHOD'];

function json($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

if ($method !== 'GET') {
    json(['success' => false, 'message' => 'Method not allowed'], 405);
}

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $stmt = $pdo->query("SELECT id, name, email, phone, address, created_at FROM users ORDER BY created_at DESC");
    json($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($action === 'logins') {
    // recent login activities if activity_logs table exists
    try {
        $stmt = $pdo->query("SELECT l.id, l.user_id, u.name, u.email, l.action, l.details, l.ip_address, l.user_agent, l.created_at
                              FROM activity_logs l
                              LEFT JOIN users u ON u.id = l.user_id
                              WHERE l.action IN ('user_login', 'user_logout')
                              ORDER BY l.created_at DESC");
        json($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (Throwable $e) {
        json([]);
    }
}

if ($action === 'get') {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) json(['success' => false, 'message' => 'Invalid id'], 400);
    $stmt = $pdo->prepare("SELECT id, name, email, phone, address, created_at FROM users WHERE id = ?");
    $stmt->execute([$id]);
    json($stmt->fetch(PDO::FETCH_ASSOC) ?: []);
}

json([]);
?>





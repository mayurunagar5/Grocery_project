<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['username']) || !isset($input['password'])) {
    jsonResponse(['error' => 'Username and password are required'], 400);
}

$username = sanitizeInput($input['username']);
$password = $input['password'];

try {
    $sql = "SELECT id, username, email, role, is_active, password FROM admin_users WHERE username = ? OR email = ? LIMIT 1";
    $admin = fetchOne($sql, [$username, $username]);

    if (!$admin) {
        jsonResponse(['error' => 'Invalid admin credentials'], 401);
    }

    $isValid = verifyPassword($password, $admin['password']);

    // Backward-compat: seed in data.sql used hash for "password" while comment said admin123.
    // If user tries admin123 but stored hash matches "password", accept and migrate to admin123.
    if (!$isValid && $password === 'admin123' && password_verify('password', $admin['password'])) {
        $newHash = hashPassword('admin123');
        executeQuery('UPDATE admin_users SET password = ? WHERE id = ?', [$newHash, $admin['id']]);
        $isValid = true;
    }

    // Fallback if DB accidentally has plaintext password stored; migrate to bcrypt
    if (!$isValid && hash_equals((string)$admin['password'], (string)$password)) {
        $newHash = hashPassword($password);
        executeQuery('UPDATE admin_users SET password = ? WHERE id = ?', [$newHash, $admin['id']]);
        $isValid = true;
    }

    if (!$isValid) {
        jsonResponse(['error' => 'Invalid admin credentials'], 401);
    }
    if (isset($admin['is_active']) && (int)$admin['is_active'] !== 1) {
        jsonResponse(['error' => 'Admin account is inactive'], 403);
    }

    startSession();
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_role'] = $admin['role'];

    logActivity($admin['id'], 'admin_login', 'Admin logged in');

    jsonResponse([
        'success' => true,
        'message' => 'Admin login successful',
        'admin' => [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'email' => $admin['email'],
            'role' => $admin['role']
        ]
    ]);
} catch (Throwable $e) {
    jsonResponse(['error' => 'Admin login failed. Please try again.'], 500);
}
?>



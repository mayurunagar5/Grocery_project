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
if (!isset($input['email']) || !isset($input['password'])) {
    jsonResponse(['error' => 'Email and password are required'], 400);
}

$email = sanitizeInput($input['email']);
$password = $input['password'];

try {
    $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
    $user = fetchOne($sql, [$email]);

    if (!$user || !verifyPassword($password, $user['password'])) {
        jsonResponse(['error' => 'Invalid email or password'], 401);
    }

    startSession();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];

    logActivity($user['id'], 'user_login', 'User logged in');

    jsonResponse([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ]
    ]);
} catch (Throwable $e) {
    jsonResponse(['error' => 'Login failed. Please try again.'], 500);
}
?>




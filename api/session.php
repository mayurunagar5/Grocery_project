<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../config/database.php';

startSession();

$response = [ 'authenticated' => false ];

try {
    if (isset($_SESSION['admin_id'])) {
        $admin = fetchOne(
            'SELECT id, username, email, role FROM admin_users WHERE id = ? LIMIT 1',
            [$_SESSION['admin_id']]
        );
        if ($admin) {
            $response['authenticated'] = true;
            $response['type'] = 'admin';
            $response['admin'] = $admin;
            echo json_encode($response);
            exit;
        }
    }

    if (isset($_SESSION['user_id'])) {
        $user = fetchOne(
            'SELECT id, name, email FROM users WHERE id = ? LIMIT 1',
            [$_SESSION['user_id']]
        );
        if ($user) {
            $response['authenticated'] = true;
            $response['type'] = 'user';
            $response['user'] = $user;
        }
    }

    echo json_encode($response);
} catch (Throwable $e) {
    echo json_encode(['authenticated' => false]);
}
?>




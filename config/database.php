<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'data');
define('DB_USER', 'root'); // Change if needed
define('DB_PASS', '');     // Change if needed

// Create database connection
function getDBConnection()
{
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );

        return $pdo; // return connection object if successful

    } catch (PDOException $e) {
        return false; // return false if failed
    }
}

// Test connection
if (defined('DEBUG') && DEBUG) {
    $conn = getDBConnection();
    if ($conn) {
        echo "✅ Connection successful!";
    } else {
        echo "❌ Connection failed!";
    }
}

// ================= Helper Functions =================

// Helper function to execute queries
function executeQuery($sql, $params = [])
{
    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        throw $e; // Let caller handle
    }
}

// Fetch single row
function fetchOne($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

// Fetch all rows
function fetchAll($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

// Get last inserted ID
function getLastInsertId()
{
    $pdo = getDBConnection();
    return $pdo->lastInsertId();
}

// Check if email exists
function emailExists($email)
{
    $sql    = "SELECT COUNT(*) as count FROM users WHERE email = ?";
    $result = fetchOne($sql, [$email]);
    return $result['count'] > 0;
}

// Check if username exists
function usernameExists($username)
{
    $sql    = "SELECT COUNT(*) as count FROM admin_users WHERE username = ?";
    $result = fetchOne($sql, [$username]);
    return $result['count'] > 0;
}

// Generate order number
function generateOrderNumber()
{
    return 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
}

// Sanitize input
function sanitizeInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// Validate email
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Hash password
function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

// Start session
function startSession()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Check if user logged in
function isUserLoggedIn()
{
    startSession();
    return isset($_SESSION['user_id']);
}

// Check if admin logged in
function isAdminLoggedIn()
{
    startSession();
    return isset($_SESSION['admin_id']);
}

// Get current user ID
function getCurrentUserId()
{
    startSession();
    return $_SESSION['user_id'] ?? null;
}

// Get current admin ID
function getCurrentAdminId()
{
    startSession();
    return $_SESSION['admin_id'] ?? null;
}

// Logout user
function logoutUser()
{
    startSession();
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']);
    session_destroy();
}

// Logout admin
function logoutAdmin()
{
    startSession();
    unset($_SESSION['admin_id'], $_SESSION['admin_username'], $_SESSION['admin_role']);
    session_destroy();
}

// Redirect
function redirect($url)
{
    header("Location: $url");
    exit();
}

// JSON response
function jsonResponse($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Log activity
function logActivity($user_id, $action, $details = '')
{
    try {
        $sql = "INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)";
        executeQuery($sql, [
            $user_id,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
    } catch (Throwable $e) {
        // ignore errors if table doesn’t exist
    }
}

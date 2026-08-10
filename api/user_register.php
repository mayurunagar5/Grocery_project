<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['name']) || !isset($input['email']) || !isset($input['password'])) {
    jsonResponse(['error' => 'Name, email, and password are required'], 400);
}

// Sanitize input
$name = sanitizeInput($input['name']);
$email = sanitizeInput($input['email']);
$password = $input['password'];
$phone = isset($input['phone']) ? sanitizeInput($input['phone']) : null;
$address = isset($input['address']) ? sanitizeInput($input['address']) : null;

// Validate email
if (!validateEmail($email)) {
    jsonResponse(['error' => 'Invalid email format'], 400);
}

// Check if email already exists
if (emailExists($email)) {
    jsonResponse(['error' => 'User with this email already exists'], 409);
}

// Validate password length
if (strlen($password) < 6) {
    jsonResponse(['error' => 'Password must be at least 6 characters long'], 400);
}

// Hash password
$hashedPassword = hashPassword($password);

try {
    // Insert user into database
    $sql = "INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)";
    executeQuery($sql, [$name, $email, $hashedPassword, $phone, $address]);

    $userId = getLastInsertId();

    // Start session and set user data
    startSession();
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    // Log the registration
    logActivity($userId, 'user_registration', 'New user registered');

    // Return success response
    jsonResponse([
        'success' => true,
        'message' => 'Account created successfully!',
        'user' => [
            'id' => $userId,
            'name' => $name,
            'email' => $email
        ]
    ]);
} catch (Throwable $e) {
    jsonResponse(['error' => 'Registration failed. Please try again.', 'detail' => $e->getMessage()], 500);
}
?> 
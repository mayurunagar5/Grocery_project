<?php
require_once '../config/database.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    // Get form data
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $topic = sanitizeInput($_POST['topic'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    // Validate required fields
    if (empty($name)) {
        jsonResponse(['error' => 'Name is required'], 400);
    }

    if (empty($email)) {
        jsonResponse(['error' => 'Email is required'], 400);
    }

    if (!validateEmail($email)) {
        jsonResponse(['error' => 'Please enter a valid email address'], 400);
    }

    if (empty($topic)) {
        jsonResponse(['error' => 'Topic is required'], 400);
    }

    if (empty($message)) {
        jsonResponse(['error' => 'Message is required'], 400);
    }

    // Insert into database
    $sql = "INSERT INTO contact_submissions (name, email, phone, topic, message) VALUES (?, ?, ?, ?, ?)";
    executeQuery($sql, [$name, $email, $phone, $topic, $message]);

    // Return success response
    jsonResponse([
        'success' => true,
        'message' => 'Thank you for your message! We will get back to you soon.'
    ]);

} catch (PDOException $e) {
    // Log error for debugging (in production, you might want to log to a file)
    error_log("Contact form error: " . $e->getMessage());
    jsonResponse(['error' => 'Sorry, there was an error submitting your message. Please try again.'], 500);
} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    jsonResponse(['error' => 'An unexpected error occurred. Please try again.'], 500);
}
?>

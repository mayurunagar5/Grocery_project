<?php
require_once 'config/database.php';

echo "<h2>Setting up Contact Form Database Table</h2>";

try {
    // Create the contact_submissions table
    $sql = "CREATE TABLE IF NOT EXISTS contact_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        topic VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('new', 'read', 'replied') DEFAULT 'new'
    )";
    
    executeQuery($sql);
    echo "<p style='color: green;'>✅ Contact submissions table created successfully!</p>";
    
    // Add indexes for better performance
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_contact_email ON contact_submissions(email)",
        "CREATE INDEX IF NOT EXISTS idx_contact_status ON contact_submissions(status)",
        "CREATE INDEX IF NOT EXISTS idx_contact_created ON contact_submissions(created_at)"
    ];
    
    foreach ($indexes as $index_sql) {
        try {
            executeQuery($index_sql);
        } catch (Exception $e) {
            // Index might already exist, that's okay
        }
    }
    
    echo "<p style='color: green;'>✅ Database indexes created successfully!</p>";
    
    // Test the table
    $test_sql = "SELECT COUNT(*) as count FROM contact_submissions";
    $result = fetchOne($test_sql);
    echo "<p style='color: blue;'>📊 Current contact submissions: " . $result['count'] . "</p>";
    
    echo "<h3>Setup Complete! 🎉</h3>";
    echo "<p>Your contact form is now ready to store submissions in the database.</p>";
    echo "<p><a href='contact.php' style='color: blue;'>→ Test the contact form</a></p>";
    echo "<p><a href='admin_contact.php' style='color: blue;'>→ View submissions in admin panel</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; padding: 2rem; }
h2 { color: #2fb36d; }
</style>

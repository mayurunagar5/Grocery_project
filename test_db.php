<?php
// Test database connection and tables
require_once "config/database.php";

echo "<h2>Database Connection Test</h2>";

// Test connection
$pdo = getDBConnection();
if ($pdo) {
    echo "✅ Database connection successful!<br><br>";
    
    // Check if tables exist
    $tables = ['users', 'categories', 'products', 'orders', 'order_items', 'cart', 'admin_users', 'contact_submissions'];
    
    echo "<h3>Table Status:</h3>";
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Table '$table' exists<br>";
                
                // Count records
                $count_stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $count_stmt->fetch()['count'];
                echo "   - Records: $count<br>";
            } else {
                echo "❌ Table '$table' does not exist<br>";
            }
        } catch (Exception $e) {
            echo "❌ Error checking table '$table': " . $e->getMessage() . "<br>";
        }
    }
    
    // Test products query
    echo "<br><h3>Products Test:</h3>";
    try {
        $stmt = $pdo->query("
            SELECT p.*, c.name as category 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id
            LIMIT 5
        ");
        $products = $stmt->fetchAll();
        
        if (count($products) > 0) {
            echo "✅ Products query successful! Found " . count($products) . " products<br>";
            foreach ($products as $product) {
                echo "- {$product['name']} (Category: {$product['category']})<br>";
            }
        } else {
            echo "⚠️ No products found in database<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error querying products: " . $e->getMessage() . "<br>";
    }
    
} else {
    echo "❌ Database connection failed!<br>";
    echo "Please check your database configuration in config/database.php<br>";
}
?>

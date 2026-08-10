<?php
require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();
if ($conn) {
    echo "DB OK\n";
    exit(0);
}

echo "DB CONNECTION FAILED\n";
exit(1);

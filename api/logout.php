<?php
require_once '../config/database.php';

startSession();

if (isset($_SESSION['admin_id'])) {
    logoutAdmin();
}
if (isset($_SESSION['user_id'])) {
    logoutUser();
}

// Redirect to login page
redirect('login.php');
?>




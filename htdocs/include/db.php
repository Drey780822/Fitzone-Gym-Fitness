<?php
$host = 'sql305.infinityfree.com';
$user = 'if0_38973257';
$pass = 'T780822030102';
$db_name = 'if0_38973257_mygymdb';

// Establish database connection
$con = mysqli_connect($host, $user, $pass, $db_name);

// Check connection
if (!$con) {
    // Log error to file instead of displaying to users
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Connection failed. Please try again later.");
}

// Set charset to UTF-8 for proper encoding
mysqli_set_charset($con, 'utf8mb4');
?>
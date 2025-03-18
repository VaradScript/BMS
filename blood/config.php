<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'blood_management');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
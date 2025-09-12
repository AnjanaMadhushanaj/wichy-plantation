<?php
// config.php - place this at project root (same folder as init_db.sql)
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'coconut_shop_simple';


$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
die('DB connection error: ' . $conn->connect_error);
}

session_start();

// Start session only if not already started and before any output
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
// For this simple demo, auto-login test user ID 1. Replace with real login later.
if (!isset($_SESSION['user_id'])) {
	$_SESSION['user_id'] = 1;
}


function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
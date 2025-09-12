<?php
$host = "localhost";
$user = "root";
$pass = "";                // set if you have a MySQL password
$dbname = "coconut_shop_simple"; // your database

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

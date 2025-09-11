<?php
// You can add session/authentication here later (for security)
// require_once __DIR__ . '/../config.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../public/style.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f9;
    margin: 0;
    padding: 0;
}
.header {
    background: #333;
    color: white;
    padding: 20px;
    text-align: center;
}
.container {
    max-width: 1100px;
    margin: 30px auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    padding: 20px;
}
.card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    transition: transform 0.2s;
}
.card:hover {
    transform: scale(1.05);
}
.card a {
    text-decoration: none;
    display: block;
    color: #333;
    font-weight: bold;
    margin-top: 10px;
}
</style>
</head>
<body>
<div class="header">
    <h1>Admin Dashboard</h1>
    <p>Manage your store easily from here</p>
</div>
<div class="container">
    <div class="card">
        <h2>Products</h2>
        <p>Add, Edit, Delete Products</p>
        <a href="products.php">Manage Products</a>
    </div>
    <div class="card">
        <h2>Orders</h2>
        <p>View Customer Orders</p>
        <a href="orders.php">View Orders</a>
    </div>
    <div class="card">
        <h2>Employees</h2>
        <p>Manage Employee Details</p>
        <a href="employee.php">Manage Employees</a>
    </div>
    <div class="card">
        <h2>Comments</h2>
        <p>See Customer Feedback</p>
        <a href="comments.php">View Comments</a>
    </div>
</div>
</body>
</html>

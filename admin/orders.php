<?php
require_once __DIR__ . '/../config.php';

$res = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin - Orders</title>
<link rel="stylesheet" href="../public/style.css">
<style>
table { width:100%; border-collapse:collapse; margin-top:24px; }
th, td { border:1px solid #ccc; padding:8px; text-align:left; }
th { background:#f5f5f5; }
tr:nth-child(even) { background:#fafafa; }
.container { max-width:1100px; margin:auto; }
.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
</style>
</head>
<body>
<div class="container">
<div class="header">
<h1>Order Details</h1>
<a href="add_product.php" class="btn btn-primary">Add Product</a>
<a href="products.php" class="btn btn-light">Manage Products</a>
</div>
<table>
<tr>
<th>Order ID</th><th>User Email</th><th>Product Name</th><th>Qty</th><th>Address</th><th>Payment</th><th>Status</th><th>Date</th>
</tr>
<?php foreach($orders as $o): ?>
<tr>
<td><?= $o['order_id'] ?></td>
<td><?= htmlspecialchars($o['user_email']) ?></td>
<td><?= htmlspecialchars($o['product_name']) ?></td>
<td><?= $o['qty'] ?></td>
<td><?= htmlspecialchars($o['address']) ?></td>
<td><?= $o['payment_method'] ?></td>
<td><?= $o['status'] ?></td>
<td><?= $o['created_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>

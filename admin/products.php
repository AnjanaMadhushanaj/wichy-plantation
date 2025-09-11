<?php
require_once __DIR__ . '/../config.php';

// Handle delete
if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    $conn->query("DELETE FROM product WHERE product_id=$pid");
    // Also remove from cart and orders for data integrity
    $conn->query("DELETE FROM cart WHERE product_id=$pid");
    $conn->query("DELETE FROM orders WHERE product_id=$pid");
    header('Location: products.php');
    exit;
}

// Handle update
$edit = null;
if (isset($_GET['edit'])) {
    $pid = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM product WHERE product_id=$pid");
    $edit = $res->fetch_assoc();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $pid = (int)$_POST['pid'];
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $img = trim($_POST['img']);
    $desc = trim($_POST['desc']);
    $stmt = $conn->prepare('UPDATE product SET product_name=?, product_price=?, product_image=?, description=? WHERE product_id=?');
    $stmt->bind_param('sdssi', $name, $price, $img, $desc, $pid);
    $stmt->execute();
    header('Location: products.php');
    exit;
}

$res = $conn->query("SELECT * FROM product ORDER BY product_id DESC");
$products = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Manage Products</title>
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
<h1>Manage Products</h1>
<a href="add_product.php" class="btn btn-primary">Add Product</a>
<a href="orders.php" class="btn btn-light">View Orders</a>
</div>
<table>
<tr>
<th>ID</th><th>Name</th><th>Price</th><th>Image</th><th>Description</th><th>Actions</th>
</tr>
<?php foreach($products as $p): ?>
<tr>
<td><?= $p['product_id'] ?></td>
<td><?= htmlspecialchars($p['product_name']) ?></td>
<td>Rs <?= number_format($p['product_price'],2) ?></td>
<td><img src="<?= htmlspecialchars($p['product_image']) ?>" width="64" height="64"></td>
<td><?= htmlspecialchars($p['description']) ?></td>
<td>
    <a href="products.php?edit=<?= $p['product_id'] ?>" class="btn btn-light">Edit</a>
    <a href="products.php?delete=<?= $p['product_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php if ($edit): ?>
<hr>
<h3>Edit Product</h3>
<form method="post">
    <input type="hidden" name="pid" value="<?= $edit['product_id'] ?>">
    <label>Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($edit['product_name']) ?>" required>
    <label>Price</label>
    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($edit['product_price']) ?>" required>
    <label>Image Link (PNG URL)</label>
    <input type="url" name="img" value="<?= htmlspecialchars($edit['product_image']) ?>" required>
    <label>Description</label>
    <textarea name="desc" rows="3"><?= htmlspecialchars($edit['description']) ?></textarea>
    <button class="btn btn-primary" type="submit" name="update">Update</button>
    <a href="products.php" class="btn btn-light">Cancel</a>
</form>
<?php endif; ?>
</div>
</body>
</html>

<?php
require_once __DIR__ . '/../config.php';

$name = $price = $desc = $img = '';
$upload_dir = __DIR__ . '/../public/images/';
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $desc = trim($_POST['desc'] ?? '');
    $img = trim($_POST['img'] ?? '');
    $file_uploaded = false;
    // Handle file upload if present
    if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['img_file']['tmp_name'];
        $file_name = basename($_FILES['img_file']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['png', 'jpg', 'jpeg', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            $new_name = uniqid('img_', true) . '.' . $file_ext;
            $target_path = $upload_dir . $new_name;
            if (move_uploaded_file($file_tmp, $target_path)) {
                $img = 'images/' . $new_name;
                $file_uploaded = true;
            } else {
                $error = 'Failed to upload file.';
            }
        } else {
            $error = 'Invalid file type. Only PNG, JPG, JPEG, GIF allowed.';
        }
    }
    if ($name && $price && $img) {
        $stmt = $conn->prepare('INSERT INTO product (product_name, product_price, product_image, description) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('sdss', $name, $price, $img, $desc);
        if ($stmt->execute()) {
            header('Location: add_product.php?success=1');
            exit;
        } else {
            $error = 'Error: ' . $conn->error;
        }
    } else if (!$error) {
        $error = 'Name, price, and image (link or file) are required.';
    }
}

if (isset($_GET['success'])) {
    $success = 'Product added!';
    $name = $price = $desc = $img = '';
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add Product</title>
<link rel="stylesheet" href="../public/style.css">
<style>
.container { max-width:600px; margin:auto; }
label { font-weight:bold; }
input, textarea { width:100%; padding:8px; margin-bottom:12px; }
.btn { margin-right:8px; }
</style>
</head>
<body>
<div class="container">
<h2>Add Product</h2>
<?php if ($success): ?><div style="color:green;"> <?= htmlspecialchars($success) ?> </div><?php endif; ?>
<?php if ($error): ?><div style="color:red;"> <?= htmlspecialchars($error) ?> </div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <label>Product Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
    <label>Price</label>
    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>" required>
    <label>Image Link (PNG/JPG/GIF URL)</label>
    <input type="url" name="img" value="<?= htmlspecialchars($img) ?>">
    <label>Or Upload Image File</label>
    <input type="file" name="img_file" accept="image/png, image/jpeg, image/jpg, image/gif">
    <label>Description</label>
    <textarea name="desc" rows="3"><?= htmlspecialchars($desc) ?></textarea>
    <button class="btn btn-primary" type="submit">Add Product</button>
    <a href="products.php" class="btn btn-light">Manage Products</a>
</form>
</div>
</body>
</html>

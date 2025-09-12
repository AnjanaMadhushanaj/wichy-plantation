<?php
require_once __DIR__ . '/../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

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
<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"></noscript>
<style>
/* ---- OPTIMIZED STYLES ---- */
:root {
    --green: #4CAF50;
    --dark-green: #2E7D32;
    --light-green: #E8F5E9;
    --gray: #666;
    --light-gray: #f5f5f5;
    --bg: #c7efcb;
}

body {
    font-family: 'Inter', Arial, sans-serif;
    background: linear-gradient(135deg, var(--bg) 0%, #b8e6c1 100%);
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ---- MODERN BUTTON SYSTEM ---- */
.btn {
    padding: 14px 28px;
    border-radius: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-weight: 600;
    font-size: 14px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    margin: 8px;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, var(--green) 0%, var(--dark-green) 100%);
    color: white;
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(76, 175, 80, 0.4);
}

.btn-light {
    background: rgba(255, 255, 255, 0.9);
    color: var(--dark-green);
    border-color: var(--green);
    backdrop-filter: blur(10px);
}

.btn-light:hover {
    background: var(--light-green);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(165, 214, 167, 0.3);
}

/* ---- MODERN CONTAINER & FORM ---- */
.container {
    max-width: 520px;
    width: 90%;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.container h2 {
    color: var(--dark-green);
    font-size: 2rem;
    font-weight: 700;
    text-align: center;
    margin: 0 0 30px 0;
    position: relative;
}

.container h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--green), var(--dark-green));
    border-radius: 2px;
}

/* ---- PREMIUM FORM INPUTS ---- */
.form-group {
    margin-bottom: 24px;
}

label {
    display: block;
    font-weight: 600;
    color: var(--dark-green);
    margin-bottom: 8px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

input[type="text"],
input[type="number"],
input[type="url"],
input[type="file"],
textarea {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e1e5e9;
    border-radius: 12px;
    font-size: 16px;
    font-family: 'Inter', sans-serif;
    background: rgba(255, 255, 255, 0.8);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-sizing: border-box;
}

input:focus,
textarea:focus {
    outline: none;
    border-color: var(--green);
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.15);
}

textarea {
    resize: vertical;
    min-height: 120px;
}

input[type="file"] {
    padding: 12px;
    background: var(--light-green);
    border: 2px dashed var(--green);
    cursor: pointer;
}

input[type="file"]:hover {
    background: rgba(165, 214, 167, 0.3);
}

/* ---- ALERT MESSAGES ---- */
.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border: 1px solid #34d399;
}

.alert-success::before {
    content: '✅';
    font-size: 18px;
}

.alert-error {
    background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%);
    color: #991b1b;
    border: 1px solid #f87171;
}

.alert-error::before {
    content: '❌';
    font-size: 18px;
}

/* ---- ACTION BUTTONS ---- */
.form-actions {
    display: flex;
    gap: 16px;
    justify-content: center;
    margin-top: 32px;
    flex-wrap: wrap;
}

/* ---- RESPONSIVE DESIGN ---- */
@media (max-width: 768px) {
    .container {
        padding: 24px;
        margin: 20px;
    }
    
    .container h2 {
        font-size: 1.6rem;
    }
    
    .btn {
        padding: 12px 20px;
        font-size: 13px;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>
</head>
<body>
<div class="container">
    <!-- Header with Back Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0;">🛍️ Add New Product</h2>
        <a href="../admin.php" class="btn btn-light" style="margin: 0;">
            ⬅️ Back to Dashboard
        </a>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">📦 Product Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required placeholder="Enter product name">
        </div>
        
        <div class="form-group">
            <label for="price">💰 Price (Rs.)</label>
            <input type="number" id="price" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>" required placeholder="0.00">
        </div>
        
        <div class="form-group">
            <label for="img">🔗 Image URL</label>
            <input type="url" id="img" name="img" value="<?= htmlspecialchars($img) ?>" placeholder="https://example.com/image.jpg">
        </div>
        
        <div class="form-group">
            <label for="img_file">📸 Or Upload Image</label>
            <input type="file" id="img_file" name="img_file" accept="image/png, image/jpeg, image/jpg, image/gif">
        </div>
        
        <div class="form-group">
            <label for="desc">📝 Description</label>
            <textarea id="desc" name="desc" rows="4" placeholder="Describe your product..."><?= htmlspecialchars($desc) ?></textarea>
        </div>
        
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">
                ✨ Add Product
            </button>
            <a href="products.php" class="btn btn-light">
                📋 Manage Products
            </a>
        </div>
    </form>
</div>
</body>
</html>

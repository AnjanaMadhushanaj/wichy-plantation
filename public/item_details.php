<?php
require_once __DIR__ . '/../config.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ensure user is logged in (same guard as items.php)
if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
    header('Location: ../login.php');
    exit;
}
$stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$p = $res->fetch_assoc();
if (!$p) { echo 'Product not found'; exit; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?=h($p['product_name'])?> - Details</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ---- GLOBAL STYLES & VARIABLES ----*/
:root {
    --color-bg: #c7efcb;
    --color-bg-light-card: #F1F8E9;
    --color-main-green: #A5D6A7;
    --color-dark-green-text: #2E7D32;
    --color-medium-green-text: #4CAF50;
    --color-dark-btn: #333;
    --color-gray-800: #1f2937;
    --color-gray-700: #374151;
    --color-gray-600: #4b5563;
    --primary-green: #4CAF50;
} 

body {
    font-family: 'Inter', sans-serif;
    background-color: var(--color-bg);
    color: var(--color-gray-800);
}

.container {
    max-width: 1280px;
    margin-left: auto;
    margin-right: auto;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.hidden {
    display: none;
}

/* ---- BACKGROUND BARS ---- */
.background-bars-container {
    display: flex;
    justify-content: center;
    gap: 48px;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3300px;
    z-index: -1;
    overflow: hidden;
}

.bar {
    width: 70px;
    height: 3300px;
    background-color: #d8b63069;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(216,182,48,0.15);
    border: 2px solid #e6d97a33;
}

/* ---- HEADER ---- */
.header {
    background-color: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    padding: 1rem 0;
}

.header-container {
    max-height: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    margin-top: -4px;
}

.logo-container {
    display: flex;
    align-items: center;
}

.logo {
    height: 3rem;
    width: 3rem;
    margin-right: 0.75rem;
}

.main-nav {
    display: flex;
    align-items: center;
}

@media (max-width: 768px) {
    .main-nav {
        display: none;
    }
    .mobile-menu-trigger {
        display: block;
    }
    .mobile-menu {
        display: block;
    }
}

.main-nav a {
    margin: 0 0.75rem;
    text-decoration: none;
    color: var(--color-gray-800);
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    transition: background 0.3s, color 0.3s, box-shadow 0.3s;
    font-size: 14px;
}

.main-nav a:hover {
    color: var(--color-dark-green-text);
    background: linear-gradient(90deg, #c7efcb 0%, #A5D6A7 100%);
    box-shadow: 0 2px 8px rgba(165,214,167,0.15);
    transform: translateY(-2px) scale(1.05);
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.btn {
    padding: 0.375rem 1.25rem;
    border-radius: 9999px;
    text-decoration: none;
    transition: background-color 0.3s, color 0.3s;
    display: inline-block;
    text-align: center;
    border: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
}

.btn-outline {
    border: 1px solid var(--color-gray-700);
    color: var(--color-gray-800);
}

.btn-outline:hover {
    background-color: var(--color-gray-700);
    color: white;
}

.btn-solid {
    background-color: var(--color-gray-800);
    color: white;
}

.btn-solid:hover {
    background-color: var(--color-gray-700);
}

.btn-primary {
    background-color: var(--color-dark-green-text);
    color: white;
}

.btn-primary:hover {
    background-color: var(--color-medium-green-text);
}

.btn-light {
    background-color: var(--color-bg-light-card);
    color: var(--color-dark-green-text);
    border: 1px solid var(--color-main-green);
}

.btn-light:hover {
    background-color: var(--color-main-green);
    color: var(--color-dark-green-text);
}

.btn-large {
    padding: 0.75rem 2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* ---- USER PROFILE ---- */
.user-profile {
    margin-left: 15px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 14px;
    background: rgba(76, 175, 80, 0.1);
    border-radius: 20px;
    border: 1px solid rgba(76, 175, 80, 0.2);
    backdrop-filter: blur(10px);
}

.profile-pic {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--primary-green);
}

.profile-pic-placeholder {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--primary-green);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
    border: 2px solid var(--primary-green);
}

.username {
    color: var(--color-dark-green-text);
    font-weight: 500;
    font-size: 14px;
}

@media (max-width: 768px) {
    .user-info {
        padding: 6px 12px;
    }
    
    .profile-pic,
    .profile-pic-placeholder {
        width: 28px;
        height: 28px;
    }
    
    .username {
        font-size: 12px;
    }
}

.mobile-menu-trigger {
    display: none;
}

.mobile-menu-trigger .icon {
    width: 1.5rem;
    height: 1.5rem;
}

.mobile-menu {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
    padding-bottom: 1rem;
}

.mobile-menu a {
    display: block;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    color: var(--color-gray-800);
    text-decoration: none;
}

.mobile-menu a:hover {
    color: var(--color-dark-green-text);
}

/* ---- MAIN CONTENT & SECTIONS ---- */
main {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.section-title {
    font-size: 2.25rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 3rem;
}

.footer {
    width: 100%;
    margin-top: auto;
}

/* ---- PRODUCT DETAIL LAYOUT ---- */
.page-wrap {
    padding-top: 96px; /* breathing room */
    padding-bottom: 48px;
}

.back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: var(--color-dark-green-text);
    background: var(--color-bg-light-card);
    border: 1px solid var(--color-main-green);
    padding: 8px 14px;
    border-radius: 9999px;
    font-weight: 500;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.back:hover { 
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(76, 175, 80, 0.15);
    background: #E8F5E9;
}

.detail {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 32px;
    align-items: start;
    margin-top: 16px;
}

@media (max-width: 992px) {
    .detail {
        grid-template-columns: 1fr;
    }
}

.card {
    background: white;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 10px 24px rgba(0,0,0,0.06);
}

.card-padding { padding: 18px; }
.card-pad-lg { padding: 22px; }

.big {
    width: 100%;
    aspect-ratio: 1/1;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    background: #fafafa;
}

.thumbs {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 12px;
}

.thumb {
    width: 100%;
    aspect-ratio: 1/1;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.thumb:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(0,0,0,0.08); }

h2 {
    margin: 0 0 8px 0;
    font-size: 1.75rem;
    color: var(--color-gray-800);
}

.price {
    color: var(--color-dark-green-text);
    font-weight: 700;
    font-size: 1.4rem;
    margin-bottom: 12px;
}

.desc { color: var(--color-gray-700); line-height: 1.7; }

.actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.actions .btn { border-radius: 10px; padding: 10px 16px; }
.actions .btn-primary { box-shadow: 0 8px 18px rgba(46, 125, 50, 0.25); }
.actions .btn-primary:hover { box-shadow: 0 10px 22px rgba(46, 125, 50, 0.35); }
</style>
<script>
function swap(src){ document.getElementById('big').src = src; }
</script>
</head>
<body>
<div class="container page-wrap">
  <a class="back" href="items.php" aria-label="Back to items">&#8592; Back to items</a>

  <div class="detail">
    <!-- Left: Gallery -->
    <div class="card card-padding">
      <img id="big" class="big" src="<?=h($p['product_image'])?>" alt="<?=h($p['product_name'])?>" loading="eager">
      <div class="thumbs">
        <img class="thumb" src="<?=h($p['product_image'])?>" alt="Thumbnail 1" onclick="swap(this.src)" loading="lazy">
        <!-- demo thumbnails reuse same image -->
        <img class="thumb" src="<?=h($p['product_image'])?>" alt="Thumbnail 2" onclick="swap(this.src)" loading="lazy">
        <img class="thumb" src="<?=h($p['product_image'])?>" alt="Thumbnail 3" onclick="swap(this.src)" loading="lazy">
        <img class="thumb" src="<?=h($p['product_image'])?>" alt="Thumbnail 4" onclick="swap(this.src)" loading="lazy">
      </div>
    </div>

    <!-- Right: Details -->
    <div class="card card-pad-lg">
      <h2><?=h($p['product_name'])?></h2>
      <div class="price">Rs <?=number_format($p['product_price'],2)?></div>
      <p class="desc"><?= nl2br(h($p['description'])) ?></p>
      <div class="actions" style="margin-top:16px">
        <a class="btn btn-light" href="items.php">Back</a>
        <a class="btn btn-primary" href="cart.php?add=<?= $p['product_id'] ?>">Add to Cart</a>
        <a class="btn btn-primary" href="cart.php?buy=<?= $p['product_id'] ?>">Buy Now</a>
      </div>
    </div>
  </div>
 </div>
</body>
</html>
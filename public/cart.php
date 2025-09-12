<?php
// Load DB + session
require_once __DIR__ . '/../config.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// User login check
if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
    header('Location: ../login.php');
    exit;
}

$uid = $_SESSION['user_id'];

// -------------------------
// Add to cart (GET)
// -------------------------
if (isset($_GET['add'])) {
    $pid = (int)$_GET['add'];

    // Check if already in cart
    $c = $conn->prepare("SELECT id, qty FROM cart WHERE user_id=? AND product_id=?");
    $c->bind_param('ii', $uid, $pid);
    $c->execute();
    $cres = $c->get_result();

    if ($row = $cres->fetch_assoc()) {
        // Already in cart → increment qty (max 10)
        $newQty = min(10, $row['qty'] + 1);
        $u = $conn->prepare("UPDATE cart SET qty=? WHERE id=?");
        $u->bind_param('ii', $newQty, $row['id']);
        $u->execute();
    } else {
        // Not in cart → insert new
        $i = $conn->prepare("INSERT INTO cart (user_id, product_id, qty) VALUES (?, ?, 1)");
        $i->bind_param('ii', $uid, $pid);
        $i->execute();
    }

    header('Location: cart.php');
    exit;
}

// -------------------------
// Update quantities (POST)
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['qty'] as $cartId => $qty) {
        $qty = (int)$qty;
        if ($qty < 1) $qty = 1;
        if ($qty > 10) $qty = 10;

        $u = $conn->prepare("UPDATE cart SET qty=? WHERE id=? AND user_id=?");
        $u->bind_param('iii', $qty, $cartId, $uid);
        $u->execute();
    }
    header('Location: cart.php');
    exit;
}

// -------------------------
// Remove item
// -------------------------
if (isset($_GET['remove'])) {
    $cid = (int)$_GET['remove'];
    $d = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    $d->bind_param('ii', $cid, $uid);
    $d->execute();
    header('Location: cart.php');
    exit;
}

// -------------------------
// Fetch cart items
// -------------------------
$sql = "SELECT c.id AS cart_id, c.qty, p.product_name, p.product_price, p.product_image
        FROM cart c 
        JOIN product p ON c.product_id = p.product_id
        WHERE c.user_id = ?";
$s = $conn->prepare($sql);
$s->bind_param('i', $uid);
$s->execute();
$res = $s->get_result();
$items = $res->fetch_all(MYSQLI_ASSOC);

// Calculate total
$total = 0;
foreach ($items as $it) {
    $total += $it['qty'] * $it['product_price'];
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Your Cart</title>
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
</style>
<style>
body {
    background: linear-gradient(120deg, #f8fafc 0%, #e0eafc 100%);
    font-family: 'Segoe UI', Arial, sans-serif;
    min-height: 100vh;
    margin: 0;
}
.main-center {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.container, .cart-container {
    max-width: 520px;
    width: 100%;
    margin: 48px auto;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 6px 32px rgba(60, 90, 130, 0.13), 0 1.5px 4px rgba(60, 90, 130, 0.08);
    padding: 36px 32px 28px 32px;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: stretch;
}
h2, h3 {
    text-align: center;
    color: #2d3a4a;
    margin-bottom: 28px;
    font-weight: 600;
    letter-spacing: 1px;
}
table.cart-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 24px;
}
table.cart-table th, table.cart-table td {
    padding: 12px 8px;
    border-bottom: 1px solid #e3e8ee;
    text-align: left;
    font-size: 1rem;
}
table.cart-table th {
    background: #f3f7fa;
    color: #3a4a5d;
    font-weight: 600;
}
table.cart-table tr:last-child td {
    border-bottom: none;
}
.cart-summary {
    background: #f7fafc;
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 18px;
    font-size: 1.08rem;
    color: #2d3a4a;
    width: 100%;
}
.btn, button, input[type="submit"] {
    background: linear-gradient(90deg, #5b9bd5 0%, #3a7bd5 100%);
    color: #fff;
    border: none;
    border-radius: 7px;
    padding: 10px 32px;
    font-size: 1.08rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(60, 90, 130, 0.08);
    margin: 10px 8px 0 0;
    transition: background 0.2s, box-shadow 0.2s;
    display: inline-block;
    text-decoration: none;
    letter-spacing: 0.5px;
}
.btn.secondary, .btn-secondary {
    background: #f3f7fa;
    color: #3a7bd5;
    border: 1.5px solid #b6c6d6;
    box-shadow: none;
}
.btn.secondary:hover, .btn-secondary:hover {
    background: #e0eafc;
    color: #2d3a4a;
}
.btn:hover, button:hover, input[type="submit"]:hover {
    background: linear-gradient(90deg, #3a7bd5 0%, #5b9bd5 100%);
    box-shadow: 0 4px 16px rgba(60, 90, 130, 0.13);
}
.empty-cart {
    text-align: center;
    color: #b0b8c1;
    font-size: 1.1rem;
    margin: 40px 0 20px 0;
}
.cart-actions {
    width: 100%;
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-top: 24px;
}
@media (max-width: 800px) {
    .container, .cart-container {
        padding: 18px 6vw 18px 6vw;
        margin: 18px 0;
    }
    table.cart-table th, table.cart-table td {
        font-size: 0.97rem;
        padding: 8px 4px;
    }
    .main-center {
        min-height: unset;
        align-items: flex-start;
    }
}
</style>
</head>
        <div><a href="items.php">Continue Shopping</a></div>
    </div>

    <form method="post">
        <table class="table">
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Line</th>
                <th></th>
            </tr>
            <?php foreach($items as $it): ?>
            <tr>
                <td style="display:flex;align-items:center;gap:12px">
                    <img src="<?= h($it['product_image']) ?>" width="64" height="64"> 
                    <?= h($it['product_name']) ?>
                </td>
                <td>Rs <?= number_format($it['product_price'], 2) ?></td>
                <td>
                    <input class="input" type="number" 
                           name="qty[<?= $it['cart_id'] ?>]" 
                           value="<?= $it['qty'] ?>" min="1" max="10" style="width:80px">
                </td>
                <td>Rs <?= number_format($it['qty'] * $it['product_price'], 2) ?></td>
                <td><a href="cart.php?remove=<?= $it['cart_id'] ?>">Remove</a></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div style="margin-top:12px">
            <button class="btn btn-light" type="submit" name="update">Update Quantities</button>
            <a class="btn btn-primary" href="address.php">Buy Now (Proceed)</a>
        </div>
    </form>

    <div style="margin-top:18px; text-align:right">
        <div class="total">Total: Rs <?= number_format($total, 2) ?></div>
    </div>
</div>
</body>
</html>

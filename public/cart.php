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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Cart</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    /* ---- Theme tokens ---- */
    :root {
        --color-bg: #c7efcb;
        --color-bg-light-card: #F1F8E9;
        --color-main-green: #A5D6A7;
        --color-dark-green-text: #2E7D32;
        --color-medium-green-text: #4CAF50;
        --color-gray-800: #1f2937;
        --color-gray-700: #374151;
        --color-gray-600: #4b5563;
    }
    body { margin:0; font-family:'Inter',sans-serif; background: var(--color-bg); color: var(--color-gray-800); }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
    .page-wrap { padding-top: 64px; padding-bottom: 48px; }

    .section-title { font-size: 1.75rem; font-weight: 700; text-align: center; margin: 16px 0 24px; }
    .back { display:inline-flex; align-items:center; gap:8px; text-decoration:none; color:var(--color-dark-green-text); background:var(--color-bg-light-card); border:1px solid var(--color-main-green); padding:8px 14px; border-radius:9999px; font-weight:500; transition:transform .2s, box-shadow .2s, background .2s; }
    .back:hover { transform: translateY(-1px); box-shadow: 0 8px 16px rgba(76,175,80,.15); background:#E8F5E9; }

    .card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 10px 24px rgba(0,0,0,.06); }
    .card-pad { padding:18px; }

    .cart-table { width:100%; border-collapse: collapse; }
    .cart-table th, .cart-table td { padding:12px 10px; border-bottom:1px solid #e5e7eb; text-align:left; }
    .cart-table th { background:#f8fafc; color:#334155; font-weight:600; }
    .cart-table tr:last-child td { border-bottom:none; }

    .prod-cell { display:flex; align-items:center; gap:12px; }
    .prod-img { width:80px; height:80px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb; background:#fafafa; }

    .qty-input { width:90px; padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; font-weight:600; }
    .qty-input:focus { outline:none; border-color: var(--color-main-green); box-shadow: 0 0 0 3px rgba(165,214,167,.35); }

    .remove-link { color:#dc2626; text-decoration:none; font-weight:600; }
    .remove-link:hover { text-decoration: underline; }

    .actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:12px; }
    .totals { text-align:right; margin-top:18px; font-size:1.15rem; font-weight:700; color: var(--color-dark-green-text); }

    .btn { padding:10px 16px; border-radius:10px; border:none; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; }
    .btn-light { background: var(--color-bg-light-card); color: var(--color-dark-green-text); border:1px solid var(--color-main-green); }
    .btn-light:hover { background: var(--color-main-green); }
    .btn-primary { background: var(--color-dark-green-text); color:#fff; }
    .btn-primary:hover { background: var(--color-medium-green-text); }

    .empty { text-align:center; padding: 28px; color: var(--color-gray-600); }

    @media (max-width: 768px) {
        .cart-table th, .cart-table td { padding:10px 8px; }
        .prod-img { width:64px; height:64px; }
    }
    </style>
</head>
<body>
    <div class="container page-wrap">
        <a class="back" href="items.php">&#8592; Continue Shopping</a>
        <h2 class="section-title">Your Cart</h2>

        <?php if (!$items): ?>
            <div class="card card-pad empty">
                Your cart is empty. Start exploring our items.
                <div style="margin-top:12px">
                    <a class="btn btn-primary" href="items.php">Browse Items</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card card-pad">
                <form method="post">
                    <table class="cart-table">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Line</th>
                            <th></th>
                        </tr>
                        <?php foreach ($items as $it): ?>
                        <tr>
                            <td class="prod-cell">
                                <img class="prod-img" src="<?= h($it['product_image']) ?>" alt="<?= h($it['product_name']) ?>" loading="lazy" decoding="async">
                                <span><?= h($it['product_name']) ?></span>
                            </td>
                            <td>Rs <?= number_format($it['product_price'], 2) ?></td>
                            <td>
                                <input class="qty-input" type="number" name="qty[<?= $it['cart_id'] ?>]" value="<?= $it['qty'] ?>" min="1" max="10">
                            </td>
                            <td>Rs <?= number_format($it['qty'] * $it['product_price'], 2) ?></td>
                            <td><a class="remove-link" href="cart.php?remove=<?= $it['cart_id'] ?>">Remove</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <div class="actions">
                        <button class="btn btn-light" type="submit" name="update">Update Quantities</button>
                        <a class="btn btn-primary" href="address.php">Buy Now (Proceed)</a>
                    </div>
                </form>
                <div class="totals">Total: Rs <?= number_format($total, 2) ?></div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
require_once __DIR__ . '/../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$res = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin - Orders</title>
<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap"></noscript>
<style>
/* ---- EXPERT DESIGN SYSTEM ---- */
:root {
    --green: #4CAF50;
    --dark-green: #2E7D32;
    --light-green: #E8F5E9;
    --gray: #666;
    --light-gray: #f5f5f5;
    --bg: #f8fffe;
    --accent: #00c853;
    --shadow: rgba(0, 0, 0, 0.1);
}

body {
    font-family: 'Inter', Arial, sans-serif;
    background: linear-gradient(135deg, var(--bg) 0%, #e8f5e9 100%);
    margin: 0;
    padding: 20px;
    color: #2c3e50;
    line-height: 1.6;
}

/* ---- MODERN CONTAINER ---- */
.container {
    max-width: 1400px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 
        0 20px 40px var(--shadow),
        0 0 0 1px rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* ---- PREMIUM HEADER ---- */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 3px solid transparent;
    background: linear-gradient(90deg, var(--green), var(--accent)) bottom/100% 3px no-repeat;
}

.header h1 {
    color: var(--dark-green);
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 15px;
}

.header h1::before {
    content: '📋';
    font-size: 2.2rem;
}

/* ---- EXPERT BUTTON SYSTEM ---- */
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
    margin: 0 8px;
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

/* ---- PREMIUM TABLE DESIGN ---- */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 30px var(--shadow);
    margin-top: 30px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

th {
    background: linear-gradient(135deg, var(--dark-green) 0%, var(--green) 100%);
    color: white;
    padding: 20px 16px;
    text-align: left;
    font-weight: 700;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    border: none;
}

th::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
}

td {
    padding: 18px 16px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
    color: #2c3e50;
    transition: all 0.3s ease;
}

tr:last-child td {
    border-bottom: none;
}

tr:hover td {
    background: linear-gradient(135deg, var(--light-green) 0%, rgba(232, 245, 233, 0.7) 100%);
    transform: scale(1.01);
}

/* ---- STATUS BADGES ---- */
.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: linear-gradient(135deg, #fef3c7 0%, #fbbf24 100%);
    color: #92400e;
}

.status-completed {
    background: linear-gradient(135deg, #d1fae5 0%, #34d399 100%);
    color: #065f46;
}

.status-cancelled {
    background: linear-gradient(135deg, #fee2e2 0%, #f87171 100%);
    color: #991b1b;
}

/* ---- STATS OVERVIEW ---- */
.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
    backdrop-filter: blur(10px);
    padding: 24px;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 25px var(--shadow);
    text-align: center;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark-green);
    margin-bottom: 8px;
}

.stat-label {
    font-size: 14px;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* ---- RESPONSIVE DESIGN ---- */
@media (max-width: 1200px) {
    .container {
        margin: 10px;
        padding: 30px;
    }
    
    table {
        font-size: 13px;
    }
    
    th, td {
        padding: 12px 10px;
    }
}

@media (max-width: 768px) {
    .header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .header h1 {
        font-size: 2rem;
    }
    
    .stats-overview {
        grid-template-columns: 1fr;
    }
    
    table {
        font-size: 12px;
    }
    
    th, td {
        padding: 8px 6px;
    }
    
    .btn {
        padding: 10px 16px;
        font-size: 12px;
    }
}
</style>
</head>
<body>
<div class="container">
    <!-- Header Section -->
    <div class="header">
        <h1>Order Management</h1>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="add_product.php" class="btn btn-primary">
                ➕ Add Product
            </a>
            <a href="products.php" class="btn btn-light">
                📦 Manage Products
            </a>
            <a href="../admin.php" class="btn btn-light">
                🏠 Dashboard
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-number"><?= count($orders) ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count(array_filter($orders, fn($o) => $o['status'] === 'pending')) ?></div>
            <div class="stat-label">Pending Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count(array_filter($orders, fn($o) => $o['status'] === 'completed')) ?></div>
            <div class="stat-label">Completed Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">Rs <?= number_format(array_sum(array_map(fn($o) => $o['qty'] * 100, $orders))) ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>

    <!-- Orders Table -->
    <?php if (!empty($orders)): ?>
    <table>
        <thead>
            <tr>
                <th>🆔 Order ID</th>
                <th>👤 Customer</th>
                <th>📦 Product</th>
                <th>📊 Quantity</th>
                <th>📍 Address</th>
                <th>💳 Payment</th>
                <th>📈 Status</th>
                <th>📅 Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $o): ?>
            <tr>
                <td><strong>#<?= $o['order_id'] ?></strong></td>
                <td><?= htmlspecialchars($o['user_email']) ?></td>
                <td><strong><?= htmlspecialchars($o['product_name']) ?></strong></td>
                <td><span style="background: var(--light-green); padding: 4px 8px; border-radius: 6px; font-weight: 600;"><?= $o['qty'] ?></span></td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($o['address']) ?>">
                    <?= htmlspecialchars(strlen($o['address']) > 30 ? substr($o['address'], 0, 30) . '...' : $o['address']) ?>
                </td>
                <td>
                    <span style="background: <?= $o['payment_method'] === 'card' ? '#e3f2fd' : '#fff3e0' ?>; color: <?= $o['payment_method'] === 'card' ? '#1565c0' : '#ef6c00' ?>; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                        <?= ucfirst($o['payment_method']) ?>
                    </span>
                </td>
                <td>
                    <span class="status-badge status-<?= $o['status'] ?>">
                        <?= ucfirst($o['status']) ?>
                    </span>
                </td>
                <td style="font-size: 13px; color: var(--gray);">
                    <?= date('M d, Y', strtotime($o['created_at'])) ?><br>
                    <small style="color: #999;"><?= date('H:i', strtotime($o['created_at'])) ?></small>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div style="text-align: center; padding: 60px 20px; background: rgba(255,255,255,0.7); border-radius: 16px; margin-top: 30px;">
        <div style="font-size: 4rem; margin-bottom: 20px;">📋</div>
        <h3 style="color: var(--dark-green); margin: 0 0 10px 0;">No Orders Found</h3>
        <p style="color: var(--gray); margin: 0;">Orders will appear here once customers start purchasing.</p>
    </div>
    <?php endif; ?>
</div>
</body>
</html>

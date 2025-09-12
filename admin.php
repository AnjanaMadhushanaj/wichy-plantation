<?php
require_once __DIR__ . '/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect non-admin users to login page
    header('Location: login.php');
    exit;
}

// Get admin user info
$admin_username = $_SESSION['username'] ?? 'Admin';
$admin_email = $_SESSION['email'] ?? '';

// Get some basic statistics for the admin dashboard
$total_users_query = "SELECT COUNT(*) as total FROM users";
$total_users_result = $conn->query($total_users_query);
$total_users = $total_users_result->fetch_assoc()['total'];

$total_products_query = "SELECT COUNT(*) as total FROM product";
$total_products_result = $conn->query($total_products_query);
$total_products = $total_products_result->fetch_assoc()['total'];

$total_orders_query = "SELECT COUNT(*) as total FROM orders";
$total_orders_result = $conn->query($total_orders_query);
$total_orders = $total_orders_result->fetch_assoc()['total'];

// Get recent users
$recent_users_query = "SELECT username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users_result = $conn->query($recent_users_query);
$recent_users = $recent_users_result->fetch_all(MYSQLI_ASSOC);

// Get recent orders
$recent_orders_query = "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_id DESC LIMIT 5";
$recent_orders_result = $conn->query($recent_orders_query);
$recent_orders = $recent_orders_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Coconut Shop</title>
    <link rel="stylesheet" href="public/style.css">
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 0%, #e0eafc 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
        }
        .admin-header {
            background: #2d3a4a;
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .admin-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .logout-btn {
            background: #5b9bd5;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .logout-btn:hover {
            background: #3a7bd5;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .welcome-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #5b9bd5;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-weight: 500;
        }
        .admin-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .admin-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3a4a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0eafc;
        }
        .admin-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .admin-link {
            background: #f7fafc;
            color: #2d3a4a;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
            border: 1px solid #e0eafc;
        }
        .admin-link:hover {
            background: #e0eafc;
            color: #2d3a4a;
        }
        .recent-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .recent-item {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }
        .recent-item:last-child {
            border-bottom: none;
        }
        .recent-item strong {
            color: #2d3a4a;
        }
        .role-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        .role-admin {
            background: #ffebee;
            color: #c62828;
        }
        .role-employee {
            background: #e8f5e8;
            color: #2e7d32;
        }
        .role-customer {
            background: #e3f2fd;
            color: #1565c0;
        }
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .admin-info {
                flex-direction: column;
                gap: 10px;
            }
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            .admin-sections {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="header-content">
            <div class="admin-title">🥥 Coconut Shop Admin Dashboard</div>
            <div class="admin-info">
                <span>Welcome, <?= htmlspecialchars($admin_username) ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <div class="welcome-section">
            <h2>Welcome to Admin Dashboard</h2>
            <p>Manage your coconut shop from this central dashboard. You have full access to all administrative functions.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $total_users ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_products ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_orders ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>

        <div class="admin-sections">
            <div class="admin-section">
                <h3 class="section-title">Quick Actions</h3>
                <div class="admin-links">
                    <a href="admin/add_product.php" class="admin-link">➕ Add New Product</a>
                    <a href="admin/products.php" class="admin-link">📦 Manage Products</a>
                    <a href="admin/orders.php" class="admin-link">📋 View Orders</a>
                    <a href="admin/employee.php" class="admin-link">👥 Manage Employees</a>
                    <a href="public/items.php" class="admin-link">🛍️ View Shop</a>
                </div>
            </div>

            <div class="admin-section">
                <h3 class="section-title">Recent Users</h3>
                <div class="recent-list">
                    <?php if (!empty($recent_users)): ?>
                        <?php foreach ($recent_users as $user): ?>
                            <div class="recent-item">
                                <strong><?= htmlspecialchars($user['username']) ?></strong>
                                <span class="role-badge role-<?= $user['role'] ?>"><?= $user['role'] ?></span>
                                <br>
                                <small><?= htmlspecialchars($user['email']) ?></small>
                                <br>
                                <small>Joined: <?= date('M d, Y', strtotime($user['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No users found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="admin-section">
                <h3 class="section-title">Recent Orders</h3>
                <div class="recent-list">
                    <?php if (!empty($recent_orders)): ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <div class="recent-item">
                                <strong>Order #<?= $order['order_id'] ?></strong>
                                <br>
                                Customer: <?= htmlspecialchars($order['username']) ?>
                                <br>
                                Product: <?= htmlspecialchars($order['product_name']) ?>
                                <br>
                                <small>Status: <?= htmlspecialchars($order['status']) ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No orders found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

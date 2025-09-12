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

.container-global {
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
.header-global {
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

<!-- Quick Actions Section -->
<div class="container" style="margin-bottom: 30px;">
    <div class="card" style="width:100%;max-width:1000px;margin:auto;">
        <h2 style="margin-bottom:18px;">Quick Actions</h2>
        <div style="display:flex;flex-wrap:wrap;gap:20px;justify-content:center;">
            <a class="btn btn-primary" href="products.php">Manage Products</a>
            <a class="btn btn-primary" href="orders.php">View Orders</a>
            <a class="btn btn-primary" href="employee.php">Manage Employees</a>
            <a class="btn btn-primary" href="comments.php">View All Comments</a>
            <a class="btn btn-primary" href="contact_messages.php">View Contact Messages</a>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:40px;justify-content:center;margin-top:30px;">
            <form method="POST" action="admin_add_comment.php" style="background:#fff;padding:18px 24px;border-radius:10px;box-shadow:0 2px 8px #0001;min-width:260px;">
                <h3 style="margin-top:0;color:var(--color-dark-green-text);">Add Comment</h3>
                <input type="text" name="user_name" placeholder="Name" required style="width:100%;margin-bottom:10px;padding:8px 10px;border-radius:6px;border:1px solid var(--color-main-green);">
                <textarea name="comment" placeholder="Comment" required style="width:100%;margin-bottom:10px;padding:8px 10px;border-radius:6px;border:1px solid var(--color-main-green);"></textarea>
                <button type="submit" class="btn btn-solid" style="width:100%;">Add Comment</button>
            </form>
            <form method="POST" action="admin_add_contact_message.php" style="background:#fff;padding:18px 24px;border-radius:10px;box-shadow:0 2px 8px #0001;min-width:260px;">
                <h3 style="margin-top:0;color:var(--color-dark-green-text);">Add Contact Message</h3>
                <input type="text" name="name" placeholder="Name" required style="width:100%;margin-bottom:10px;padding:8px 10px;border-radius:6px;border:1px solid var(--color-main-green);">
                <input type="email" name="email" placeholder="Email" required style="width:100%;margin-bottom:10px;padding:8px 10px;border-radius:6px;border:1px solid var(--color-main-green);">
                <input type="text" name="subject" placeholder="Subject" required style="width:100%;margin-bottom:10px;padding:8px 10px;border-radius:6px;border:1px solid var(--color-main-green);">
                <textarea name="message" placeholder="Message" required style="width:100%;margin-bottom:10px;padding:8px 10px;border-radius:6px;border:1px solid var(--color-main-green);"></textarea>
                <button type="submit" class="btn btn-solid" style="width:100%;">Add Message</button>
            </form>
        </div>
    </div>
</div>


<!-- Comments Section under Quick Actions -->
<?php
require_once '../config.php';
$sql = "SELECT id, user_name, comment, created_at FROM comments ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($sql);
?>
<div class="container" style="margin-top: 0;">
    <div class="card" style="width:100%;max-width:1000px;margin:auto;">
        <h2 style="margin-bottom:18px;">Latest Customer Comments</h2>
        <?php if ($result && $result->num_rows > 0): ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f0f4f8;">
                    <th style="padding:10px 6px;">ID</th>
                    <th style="padding:10px 6px;">Name</th>
                    <th style="padding:10px 6px;">Comment</th>
                    <th style="padding:10px 6px;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td style="padding:8px 6px; color:#888;"> <?= $row['id'] ?> </td>
                    <td style="padding:8px 6px; font-weight:500; color:#1a73e8;"> <?= htmlspecialchars($row['user_name']) ?> </td>
                    <td style="padding:8px 6px; color:#333;"> <?= nl2br(htmlspecialchars($row['comment'])) ?> </td>
                    <td style="padding:8px 6px; color:#888; font-size:0.97em;"> <?= $row['created_at'] ?> </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div style="text-align:center; color:#888; padding:30px 0;">No comments found.</div>
        <?php endif; ?>
    </div>
</div>
<?php
if ($result) $result->free();

// Show latest contact messages below comments
$sql2 = "SELECT id, name, email, subject, message, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 10";
$result2 = $conn->query($sql2);
?>
<div class="container" style="margin-top: 30px;">
    <div class="card" style="width:100%;max-width:1000px;margin:auto;">
        <h2 style="margin-bottom:18px;">Latest Contact Messages</h2>
        <?php if ($result2 && $result2->num_rows > 0): ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f0f4f8;">
                    <th style="padding:10px 6px;">ID</th>
                    <th style="padding:10px 6px;">Name</th>
                    <th style="padding:10px 6px;">Email</th>
                    <th style="padding:10px 6px;">Subject</th>
                    <th style="padding:10px 6px;">Message</th>
                    <th style="padding:10px 6px;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result2->fetch_assoc()): ?>
                <tr>
                    <td style="padding:8px 6px; color:#888;"> <?= $row['id'] ?> </td>
                    <td style="padding:8px 6px; font-weight:500; color:#1a73e8;"> <?= htmlspecialchars($row['name']) ?> </td>
                    <td style="padding:8px 6px; color:#388e3c;"> <?= htmlspecialchars($row['email']) ?> </td>
                    <td style="padding:8px 6px; color:#333; font-style:italic;"> <?= htmlspecialchars($row['subject']) ?> </td>
                    <td style="padding:8px 6px; color:#333;"> <?= nl2br(htmlspecialchars($row['message'])) ?> </td>
                    <td style="padding:8px 6px; color:#888; font-size:0.97em;"> <?= $row['created_at'] ?> </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div style="text-align:center; color:#888; padding:30px 0;">No contact messages found.</div>
        <?php endif; ?>
    </div>
</div>
<?php
if ($result2) $result2->free();
$conn->close();
?>

<!-- Main Cards Section (optional, can be removed if redundant) -->
<div class="container">

</div>
</body>
</html>

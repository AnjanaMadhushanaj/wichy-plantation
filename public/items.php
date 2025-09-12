<?php
require_once __DIR__ . '/../config.php';

// Prevent page caching for security
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
// User login check
if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
	header('Location: ../login.php');
	exit;
}

$res = $conn->query("SELECT * FROM product ORDER BY product_id DESC");
$products = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Items - Coconut Shop</title>
<link rel="stylesheet" href="style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
} 

body {
    font-family: 'Inter', sans-serif;
    background-color: var(--color-bg);
    color: var(--color-gray-800);
    margin: 0;
    min-height: 100vh;
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
    overflow: hidden; /* Prevents horizontal scrollbar */
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

/* Ensure logout button is always visible */
.header .btn-logout {
    background-color: #dc3545 !important;
    color: white !important;
    display: inline-flex !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.header-container {
    max-height: 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
    margin-top: -4px;
}

/* ---- LOGO STYLING ---- */
.logo-container h1 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--color-dark-green-text);
    text-shadow: 0 1px 2px rgba(46, 125, 50, 0.1);
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
    z-index: 100;
    position: relative;
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

/* Logout Button Specific Styles */
.btn-logout {
    background-color: #dc3545 !important;
    color: white !important;
    border: 1px solid #dc3545;
    padding: 0.5rem 1.2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex !important;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
    opacity: 1;
    visibility: visible;
    z-index: 10;
}

.btn-logout svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}

.btn-logout:hover {
    background-color: var(--color-medium-green-text) !important;
    color: white !important;
    border-color: var(--color-medium-green-text);
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(76, 175, 80, 0.4);
}

.btn-logout:active {
    transform: translateY(0);
    box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
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

/* ---- RESPONSIVE HEADER ---- */
@media (max-width: 768px) {
    .header-container {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
    
    .logo-container h1 {
        font-size: 1.25rem !important;
    }
    
    .header-actions {
        gap: 0.5rem;
    }
    
    .btn {
        padding: 0.25rem 0.75rem;
        font-size: 12px;
    }
    
    .user-info {
        padding: 4px 8px;
    }
    
    .profile-pic,
    .profile-pic-placeholder {
        width: 28px;
        height: 28px;
    }
    
    .username {
        font-size: 11px;
        display: none; /* Hide username text on very small screens */
    }
}

@media (max-width: 480px) {
    .logo-container h1 {
        font-size: 1rem !important;
    }
    
    .header-actions {
        gap: 0.25rem;
    }
    
    .btn {
        padding: 0.25rem 0.5rem;
        font-size: 11px;
    }
    
    .user-info .username {
        display: none;
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
    padding-top: 6rem;
    padding-bottom: 3rem;
}

.section-title {
    font-size: 2.25rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 3rem;
    color: var(--color-dark-green-text);
}

/* ---- PRODUCT GRID ---- */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
    margin-bottom: 3rem;
}

.card {
    background-color: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid var(--color-main-green);
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

.card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--color-dark-green-text);
}

.price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-medium-green-text);
    margin-bottom: 1rem;
}

.footer {
    width: 100%;
    margin-top: auto;
    text-align: center;
    padding: 2rem 0;
    background-color: var(--color-bg-light-card);
    color: var(--color-dark-green-text);
    border-top: 1px solid var(--color-main-green);
    font-style: italic;
    font-weight: 500;
}
</style>
</head>
<body>
<!-- Background bars -->
<div class="background-bars-container">
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
</div>

<div class="header">
    <div class="container">
        <div class="header-container">
            <!-- Left side: Coconut Shop Title -->
            <div class="logo-container">
                <h1 style="margin: 0; font-size: 1.75rem; font-weight: 700; color: var(--color-dark-green-text);">
                    🥥 Coconut Shop
                </h1>
            </div>
            
            <!-- Right side: View Cart, Logout, and User Profile -->
            <div class="header-actions">
                <a href="cart.php" class="btn btn-outline">View Cart</a>
                <a href="../logout.php" class="btn btn-logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                    Logout
                </a>
                
                <!-- User Profile Section -->
                <div class="user-profile">
                    <?php
                    // Get user profile data
                    $user_id = $_SESSION['user_id'];
                    $user_stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
                    $user_stmt->bind_param('i', $user_id);
                    $user_stmt->execute();
                    $user_result = $user_stmt->get_result();
                    $user_data = $user_result->fetch_assoc();
                    
                    $username = $user_data['username'] ?? 'User';
                    $profile_picture = $user_data['profile_picture'] ?? null;
                    ?>
                    
                    <div class="user-info">
                        <?php if ($profile_picture && file_exists('../' . $profile_picture)): ?>
                            <img src="../<?= htmlspecialchars($profile_picture) ?>" alt="Profile" class="profile-pic">
                        <?php else: ?>
                            <div class="profile-pic-placeholder">
                                <?= strtoupper(substr($username, 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <span class="username">Hello, <?= htmlspecialchars($username) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<main>
    <div class="container">
        <div class="grid">
<?php foreach($products as $p): ?>
<div class="card">
<img src="<?=h($p['product_image'])?>" alt="">
<h3><?=h($p['product_name'])?></h3>
<div class="price">Rs <?=number_format($p['product_price'],2)?></div>
<div style="margin-top:8px">
<a class="btn btn-light" href="item_details.php?id=<?= $p['product_id'] ?>">View</a>
<a class="btn btn-primary" href="cart.php?add=<?= $p['product_id'] ?>">Add to Cart</a>
</div>
</div>
<?php endforeach; ?>
        </div>
    </div>
</main>

<div class="footer">
    <p>Apen Gathtoth Pita Yan na, Pitin Gathtoth Api dan na</p>
</div>

<script>
// Logout confirmation and session security
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.querySelector('.btn-logout');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to logout? You will need to enter your credentials again to access this site.')) {
                // Clear any local storage or session storage if used
                if (typeof(Storage) !== "undefined") {
                    localStorage.clear();
                    sessionStorage.clear();
                }
                
                // Proceed with logout
                window.location.href = this.href;
            }
        });
    }
    
    // Prevent back button after logout (additional security)
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
    };
});
</script>

</body>
</html>
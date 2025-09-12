<?php
require_once __DIR__ . '/../config.php';

// Security headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['checkout_address'])) {
    header('Location: address.php');
    exit;
}

// Handle payment method selection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['method'] ?? '';
    // Debug: Log what we received
    error_log("Payment method selected: " . $method);
    error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'not set'));
    error_log("Session address: " . print_r($_SESSION['checkout_address'] ?? 'not set', true));
    
    if ($method === 'COD') {
        $_SESSION['payment_method'] = 'COD';
        
        // Save order to database for COD
        $uid = $_SESSION['user_id'] ?? 0;
        $address_data = $_SESSION['checkout_address'] ?? '';
        
        if ($uid && $address_data) {
            // Format address from array if needed
            if (is_array($address_data)) {
                $full_address = $address_data['receiver'] . ', ' . $address_data['address'] . ', ' . 
                               $address_data['district'] . ', ' . $address_data['province'] . ' - ' . 
                               $address_data['postal'] . ', Mobile: ' . $address_data['mobile'];
            } else {
                $full_address = $address_data;
            }
            
            // Fetch cart items and save order
            $sql = "SELECT c.product_id, c.qty, u.email, p.product_name FROM cart c JOIN users u ON c.user_id = u.id JOIN product p ON c.product_id = p.product_id WHERE c.user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $uid);
            $stmt->execute();
            $res = $stmt->get_result();
            $cart_items = $res->fetch_all(MYSQLI_ASSOC);
            
            foreach ($cart_items as $item) {
                $ins = $conn->prepare("INSERT INTO orders (user_id, product_id, qty, address, payment_method, status, user_email, product_name) VALUES (?, ?, ?, ?, 'COD', 'Placed', ?, ?)");
                $ins->bind_param('iiisss', $uid, $item['product_id'], $item['qty'], $full_address, $item['email'], $item['product_name']);
                if (!$ins->execute()) {
                    error_log("Order insert failed: " . $ins->error);
                }
            }
            
            // Clear cart after successful order
            $del = $conn->prepare("DELETE FROM cart WHERE user_id=?");
            $del->bind_param('i', $uid);
            $del->execute();
        }
        
        header('Location: order_success.php');
        exit;
    } elseif ($method === 'CARD') {
        $_SESSION['payment_method'] = 'CARD';
        header('Location: card_details.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Payment - Coconut Shop</title>
<link rel="stylesheet" href="../global.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* Payment Page Specific Styles - Using Global CSS Theme */
body {
    min-height: 100vh;
    margin: 0;
}
/* Payment Container - Using Global Theme */
.payment-container {
    max-width: 500px;
    margin: 120px auto 40px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 24px;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 3rem 2.5rem;
    position: relative;
}

.payment-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--color-medium-green-text), var(--color-main-green), var(--color-dark-green-text));
    border-radius: 24px 24px 0 0;
}

h2 {
    text-align: center;
    color: var(--color-dark-green-text);
    margin-bottom: 2.5rem;
    font-weight: 700;
    font-size: 2.25rem;
    position: relative;
}

h2::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, var(--color-medium-green-text), var(--color-main-green));
    border-radius: 2px;
}

.payment-methods {
    margin-bottom: 2rem;
}

.payment-radio {
    display: flex;
    align-items: center;
    background: rgba(76, 175, 80, 0.05);
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1rem;
    border: 2px solid rgba(165, 214, 167, 0.3);
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-radio:hover {
    background: rgba(76, 175, 80, 0.1);
    border-color: var(--color-main-green);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(76, 175, 80, 0.2);
}

.payment-radio input[type="radio"] {
    margin-right: 1rem;
    width: 22px;
    height: 22px;
    accent-color: var(--color-medium-green-text);
    cursor: pointer;
}

.payment-radio input[type="radio"]:checked + span {
    color: var(--color-dark-green-text);
    font-weight: 600;
}

.payment-radio span {
    font-size: 1.1rem;
    color: var(--color-gray-700);
    font-weight: 500;
    cursor: pointer;
}

.btn {
    background: linear-gradient(135deg, var(--color-medium-green-text) 0%, var(--color-dark-green-text) 100%);
    color: white;
    border: none;
    border-radius: 16px;
    padding: 1rem 2rem;
    width: 100%;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
    margin-top: 1.5rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.btn:hover::before {
    left: 100%;
}

.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(76, 175, 80, 0.4);
    background: linear-gradient(135deg, var(--color-dark-green-text) 0%, #1B5E20 100%);
}

.btn:active {
    transform: translateY(-1px);
}

.back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--color-medium-green-text);
    text-decoration: none;
    margin-bottom: 1.5rem;
    font-size: 1rem;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    background: rgba(76, 175, 80, 0.1);
    border: 1px solid rgba(76, 175, 80, 0.2);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.back:hover {
    background: var(--color-medium-green-text);
    color: white;
    transform: translateX(-4px);
    box-shadow: 0 8px 20px rgba(76, 175, 80, 0.3);
    font-weight: 500;
    transition: color 0.2s;
}
.back:hover {
    color: #2d3a4a;
}
/* Responsive Design for Global CSS Theme */
@media (max-width: 768px) {
    .payment-container {
        margin: 100px 1rem 20px;
        padding: 2rem 1.5rem;
        border-radius: 16px;
    }
    
    h2 {
        font-size: 1.75rem;
        margin-bottom: 2rem;
    }
    
    .payment-radio {
        padding: 1rem 1.25rem;
    }
    
    .btn {
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .payment-container {
        margin: 80px 0.75rem 15px;
        padding: 1.5rem 1rem;
    }
    
    h2 {
        font-size: 1.5rem;
    }
    
    .payment-radio span {
        font-size: 1rem;
    }
}

/* Loading Animation for Button */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.btn:disabled {
    animation: pulse 1.5s ease-in-out infinite;
    cursor: not-allowed;
}
</style>
</head>
<body>
<div class="payment-container">
    <a class="back" href="address.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.42-1.41L7.83 13H20v-2z"/>
        </svg>
        Back to Address
    </a>
    
    <h2>Select Payment Method</h2>
    
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div style="background: rgba(76, 175, 80, 0.1); padding: 1rem; margin-bottom: 1.5rem; border-radius: 12px; border-left: 4px solid var(--color-medium-green-text);">
            <strong style="color: var(--color-dark-green-text);">Debug Info:</strong><br>
            <span style="color: var(--color-gray-700);">
                Method: <?= $_POST['method'] ?? 'not received' ?><br>
                User ID: <?= $_SESSION['user_id'] ?? 'not set' ?><br>
                Address: <?= is_array($_SESSION['checkout_address'] ?? '') ? 'array' : ($_SESSION['checkout_address'] ?? 'not set') ?>
            </span>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <div class="payment-methods">
            <label class="payment-radio">
                <input type="radio" name="method" value="COD" required>
                <span>Cash on Delivery</span>
            </label>
            <label class="payment-radio">
                <input type="radio" name="method" value="CARD" required>
                <span>Pay by Credit/Debit Card</span>
            </label>
        </div>
        
        <button class="btn" type="submit">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
            </svg>
            Continue Payment
        </button>
    </form>
</div>
</body>
</html>
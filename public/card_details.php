<?php
require_once __DIR__ . '/../config.php';

// Security headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['checkout_address']) || ($_SESSION['payment_method'] ?? '') !== 'CARD') {
    header('Location: payment.php');
    exit;
}

// Use global helper h() from config.php

$errors = [];
$card_holder = '';
$card_number = '';
$exp_date = '';
$cvv = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_holder = trim($_POST['card_holder'] ?? '');
    $card_number = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
    $exp_date = trim($_POST['exp_date'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');

    // Enhanced validation
    if ($card_holder === '') {
        $errors[] = "Card holder name is required.";
    } elseif (strlen($card_holder) < 2) {
        $errors[] = "Card holder name must be at least 2 characters.";
    }
    
    if (!preg_match('/^\d{16}$/', $card_number)) {
        $errors[] = "Card number must be exactly 16 digits.";
    }
    
    if (!preg_match('/^\d{2}\/\d{2}$/', $exp_date)) {
        $errors[] = "Expiration date must be in MM/YY format.";
    } else {
        // Validate expiration date is not in the past
        $exp_parts = explode('/', $exp_date);
        $exp_month = intval($exp_parts[0]);
        $exp_year = intval('20' . $exp_parts[1]);
        $current_year = intval(date('Y'));
        $current_month = intval(date('m'));
        
        if ($exp_month < 1 || $exp_month > 12) {
            $errors[] = "Invalid expiration month.";
        } elseif ($exp_year < $current_year || ($exp_year == $current_year && $exp_month < $current_month)) {
            $errors[] = "Card has expired.";
        }
    }
    
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        $errors[] = "CVV must be 3 or 4 digits.";
    }

    if (!$errors) {
        // Save card holder for order confirmation
        $_SESSION['card_holder'] = $card_holder;
        
        // Save order to database for card payment
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
                $ins = $conn->prepare("INSERT INTO orders (user_id, product_id, qty, address, payment_method, status, user_email, product_name) VALUES (?, ?, ?, ?, 'CARD', 'Paid', ?, ?)");
                $ins->bind_param('iiisss', $uid, $item['product_id'], $item['qty'], $full_address, $item['email'], $item['product_name']);
                $ins->execute();
            }
            
            // Clear cart after successful order
            $del = $conn->prepare("DELETE FROM cart WHERE user_id=?");
            $del->bind_param('i', $uid);
            $del->execute();
        }
        
        header('Location: order_success.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Card Details - Coconut Shop</title>
<link rel="stylesheet" href="../global.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* Card Details Page Specific Styles - Using Global CSS Theme */
body {
    min-height: 100vh;
    margin: 0;
}

/* Professional Card Details Container */
.card-container {
    max-width: 600px;
    margin: 120px auto 40px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 24px;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 3rem 2.5rem;
    position: relative;
}

.card-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--color-medium-green-text), var(--color-main-green), var(--color-dark-green-text));
    border-radius: 24px 24px 0 0;
}

/* Back Button */
.back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--color-medium-green-text);
    text-decoration: none;
    margin-bottom: 2rem;
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
}

/* Page Title */
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

/* Error Messages */
.error {
    background: rgba(220, 38, 38, 0.1);
    border: 1px solid rgba(220, 38, 38, 0.3);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid #dc2626;
}

.error ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.error li {
    color: #dc2626;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.error li:last-child {
    margin-bottom: 0;
}

/* Form Styling */
form {
    display: grid;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

label {
    font-weight: 600;
    color: var(--color-dark-green-text);
    font-size: 1rem;
    margin-bottom: 0.5rem;
    display: block;
    position: relative;
    padding-left: 0.5rem;
}

label::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 16px;
    background: var(--color-medium-green-text);
    border-radius: 2px;
}

input[type="text"] {
    padding: 1rem 1.25rem;
    border: 2px solid rgba(165, 214, 167, 0.3);
    border-radius: 16px;
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    color: var(--color-gray-800);
}

input[type="text"]:focus {
    outline: none;
    border-color: var(--color-medium-green-text);
    box-shadow: 0 0 0 6px rgba(76, 175, 80, 0.1);
    background: white;
    transform: translateY(-2px);
}

input[type="text"]:hover {
    border-color: var(--color-main-green);
    background: rgba(255, 255, 255, 0.9);
}

/* Card Number Input Special Styling */
input[name="card_number"] {
    letter-spacing: 2px;
    font-size: 1.1rem;
    font-weight: 500;
}

input[name="cvv"] {
    text-align: center;
    font-weight: 600;
    letter-spacing: 1px;
}

/* Two-column layout for exp and cvv */
.form-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1rem;
}

/* Button Styling */
.btn-primary {
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

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(76, 175, 80, 0.4);
    background: linear-gradient(135deg, var(--color-dark-green-text) 0%, #1B5E20 100%);
}

.btn-primary:active {
    transform: translateY(-1px);
}

/* Card Preview */
.card-preview {
    background: linear-gradient(135deg, var(--color-dark-green-text), var(--color-medium-green-text));
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
}

.card-preview::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.card-chip {
    width: 40px;
    height: 30px;
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    border-radius: 4px;
    margin-bottom: 1rem;
}

.card-number-preview {
    font-size: 1.25rem;
    font-weight: 600;
    letter-spacing: 3px;
    margin-bottom: 1rem;
    font-family: 'Courier New', monospace;
}

.card-holder-preview {
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Security Icons */
.security-info {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-top: 1.5rem;
    padding: 1rem;
    background: rgba(76, 175, 80, 0.05);
    border-radius: 12px;
    border: 1px solid rgba(76, 175, 80, 0.1);
}

.security-info span {
    font-size: 0.875rem;
    color: var(--color-gray-600);
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card-container {
        margin: 100px 1rem 20px;
        padding: 2rem 1.5rem;
        border-radius: 16px;
    }
    
    h2 {
        font-size: 1.75rem;
        margin-bottom: 2rem;
    }
    
    input[type="text"] {
        padding: 0.875rem 1rem;
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .btn-primary {
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .card-container {
        margin: 80px 0.75rem 15px;
        padding: 1.5rem 1rem;
    }
    
    h2 {
        font-size: 1.5rem;
    }
}

/* Loading Animation for Button */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.btn-primary:disabled {
    animation: pulse 1.5s ease-in-out infinite;
    cursor: not-allowed;
}
</style>
</head>
<body>
<div class="card-container">
    <a class="back" href="payment.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.42-1.41L7.83 13H20v-2z"/>
        </svg>
        Back to Payment
    </a>
    
    <h2>Enter Card Details</h2>
    
    <!-- Card Preview -->
    <div class="card-preview">
        <div class="card-chip"></div>
        <div class="card-number-preview" id="cardPreview">
            **** **** **** ****
        </div>
        <div class="card-holder-preview" id="holderPreview">
            CARD HOLDER NAME
        </div>
    </div>
    
    <?php if ($errors): ?>
        <div class="error">
            <ul><?php foreach ($errors as $e) echo "<li>" . h($e) . "</li>"; ?></ul>
        </div>
    <?php endif; ?>
    
    <form method="post" autocomplete="off" id="cardForm">
        <div class="form-group">
            <label>Card Holder Name</label>
            <input type="text" name="card_holder" id="holderName" value="<?= h($card_holder) ?>" placeholder="Full name as shown on card" required>
        </div>
        
        <div class="form-group">
            <label>Card Number</label>
            <input type="text" name="card_number" maxlength="19" pattern="\d{4}\s\d{4}\s\d{4}\s\d{4}" value="<?= h($card_number) ?>" placeholder="1234 5678 9012 3456" id="cardNumber" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Expiration Date</label>
                <input type="text" name="exp_date" pattern="\d{2}/\d{2}" placeholder="MM/YY" value="<?= h($exp_date) ?>" maxlength="5" id="expDate" required>
            </div>
            <div class="form-group">
                <label>CVV</label>
                <input type="text" name="cvv" maxlength="4" minlength="3" pattern="\d{3,4}" value="<?= h($cvv) ?>" placeholder="123" id="cvv" required>
            </div>
        </div>
        
        <div class="security-info">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color: var(--color-medium-green-text);">
                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11H16.2V18H7.8V11H9.2V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.4,8.7 10.4,10V11H13.6V10C13.6,8.7 12.8,8.2 12,8.2Z"/>
            </svg>
            <span>Your payment information is encrypted and secure</span>
        </div>
        
        <button class="btn-primary" type="submit" id="payButton">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
            </svg>
            Pay Now
        </button>
    </form>
</div>

<script>
// Enhanced form interactions
document.addEventListener('DOMContentLoaded', function() {
    const cardNumberInput = document.getElementById('cardNumber');
    const expDateInput = document.getElementById('expDate');
    const cvvInput = document.getElementById('cvv');
    const form = document.getElementById('cardForm');
    const payButton = document.getElementById('payButton');

    // Format card number input and update preview
    const cardPreview = document.getElementById('cardPreview');
    const holderPreview = document.getElementById('holderPreview');
    
    cardNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
        if (formattedValue.length <= 19) {
            e.target.value = formattedValue;
        }
        
        // Update card preview
        if (formattedValue.trim()) {
            let previewValue = formattedValue.padEnd(19, '*');
            cardPreview.textContent = previewValue.replace(/(\d|\*{1})/g, (match, index) => {
                return index % 5 === 4 ? match + ' ' : match;
            }).trim();
        } else {
            cardPreview.textContent = '**** **** **** ****';
        }
        
        // Visual feedback for card type
        if (value.startsWith('4')) {
            e.target.style.borderColor = '#1a365d'; // Visa blue
        } else if (value.startsWith('5')) {
            e.target.style.borderColor = '#c53030'; // Mastercard red
        } else {
            e.target.style.borderColor = 'var(--color-medium-green-text)';
        }
    });

    // Format expiration date
    expDateInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });

    // Update cardholder name preview
    document.getElementById('holderName').addEventListener('input', function(e) {
        const name = e.target.value.toUpperCase();
        holderPreview.textContent = name || 'CARD HOLDER NAME';
    });

    // CVV input - numbers only
    cvvInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });

    // Real-time validation
    const inputs = form.querySelectorAll('input[type="text"]');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.style.borderColor = 'var(--color-medium-green-text)';
            } else {
                this.style.borderColor = '#dc2626';
            }
        });
    });

    // Form submission enhancement
    form.addEventListener('submit', function(e) {
        payButton.disabled = true;
        payButton.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="animation: spin 1s linear infinite;">
                <path d="M12 4V2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8z"/>
            </svg>
            Processing Payment...
        `;
        
        // Re-enable button after 5 seconds if form hasn't submitted
        setTimeout(() => {
            if (payButton.disabled) {
                payButton.disabled = false;
                payButton.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    Pay Now
                `;
            }
        }, 5000);
    });

    // Add CSS for spin animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
});
</script>
</body>
</html>
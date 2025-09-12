<?php
require_once __DIR__ . '/../config.php';

// Security headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ensure user is logged in
$uid = $_SESSION['user_id'] ?? 0;
if ($uid === 0) {
    header('Location: login.php'); 
    exit;
}

// Sri Lankan provinces and districts
$provinces = [
    'Central' => ['Kandy', 'Matale', 'Nuwara Eliya'],
    'Eastern' => ['Ampara', 'Batticaloa', 'Trincomalee'],
    'Northern' => ['Jaffna', 'Kilinochchi', 'Mannar', 'Mullaitivu', 'Vavuniya'],
    'North Central' => ['Anuradhapura', 'Polonnaruwa'],
    'North Western' => ['Kurunegala', 'Puttalam'],
    'Sabaragamuwa' => ['Kegalle', 'Ratnapura'],
    'Southern' => ['Galle', 'Hambantota', 'Matara'],
    'Uva' => ['Badulla', 'Monaragala'],
    'Western' => ['Colombo', 'Gampaha', 'Kalutara'],
];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver = trim($_POST['receiver'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $postal = trim($_POST['postal'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');

    // Validation
    if ($receiver === '') $errors['receiver'] = 'Receiver name required.';
    if ($address === '') $errors['address'] = 'Address required.';
    if ($province === '' || !isset($provinces[$province])) $errors['province'] = 'Select a valid province.';
    if ($district === '' || !in_array($district, $provinces[$province] ?? [])) $errors['district'] = 'Select a valid district.';
    if (!preg_match('/^\d{5}$/', $postal)) $errors['postal'] = 'Postal code must be 5 digits.';
    if (!preg_match('/^(0|94)?7\d{8}$/', $mobile)) $errors['mobile'] = 'Enter valid Sri Lankan mobile number.';

    if (!$errors) {
        $_SESSION['checkout_address'] = [
            'receiver' => $receiver,
            'address' => $address,
            'province' => $province,
            'district' => $district,
            'postal' => $postal,
            'mobile' => $mobile
        ];
        header('Location: payment.php');
        exit;
    }
} else {
    $receiver = isset($_SESSION['checkout_address']['receiver']) ? $_SESSION['checkout_address']['receiver'] : '';
    $address = isset($_SESSION['checkout_address']['address']) ? $_SESSION['checkout_address']['address'] : '';
    $province = isset($_SESSION['checkout_address']['province']) ? $_SESSION['checkout_address']['province'] : '';
    $district = isset($_SESSION['checkout_address']['district']) ? $_SESSION['checkout_address']['district'] : '';
    $postal = isset($_SESSION['checkout_address']['postal']) ? $_SESSION['checkout_address']['postal'] : '';
    $mobile = isset($_SESSION['checkout_address']['mobile']) ? $_SESSION['checkout_address']['mobile'] : '';
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Address</title>
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

/* ---- PROFESSIONAL ADDRESS FORM STYLES ---- */
body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.container {
    max-width: 800px;
    margin: 120px auto 40px;
    padding: 3rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 24px;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    position: relative;
}

.container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--color-medium-green-text), var(--color-main-green), var(--color-dark-green-text));
    border-radius: 24px 24px 0 0;
}

.back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--color-medium-green-text);
    text-decoration: none;
    font-weight: 500;
    margin-bottom: 2rem;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: rgba(76, 175, 80, 0.1);
    border: 1px solid rgba(76, 175, 80, 0.2);
    backdrop-filter: blur(10px);
}

.back:hover {
    background: var(--color-medium-green-text);
    color: white;
    transform: translateX(-4px);
    box-shadow: 0 8px 20px rgba(76, 175, 80, 0.3);
}

h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--color-dark-green-text);
    margin-bottom: 3rem;
    text-align: center;
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

#addressForm {
    display: grid;
    gap: 2rem;
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

.input {
    padding: 1rem 1.25rem;
    border: 2px solid rgba(165, 214, 167, 0.3);
    border-radius: 16px;
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    position: relative;
}

.input:focus {
    outline: none;
    border-color: var(--color-medium-green-text);
    box-shadow: 0 0 0 6px rgba(76, 175, 80, 0.1);
    background: white;
    transform: translateY(-2px);
}

.input:hover {
    border-color: var(--color-main-green);
    background: rgba(255, 255, 255, 0.9);
}

textarea.input {
    resize: vertical;
    min-height: 120px;
    font-family: 'Inter', sans-serif;
}

select.input {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%234CAF50' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 1rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 3rem;
}

.error-message {
    color: #dc2626;
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(220, 38, 38, 0.1);
    border-radius: 12px;
    border-left: 4px solid #dc2626;
    backdrop-filter: blur(10px);
}

.form-actions {
    margin-top: 3rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn {
    padding: 1rem 3rem;
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    border: none;
    cursor: pointer;
    font-weight: 600;
    font-size: 1.1rem;
    font-family: 'Inter', sans-serif;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
    min-width: 160px;
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

.btn-light {
    background: linear-gradient(135deg, var(--color-medium-green-text) 0%, var(--color-dark-green-text) 100%);
    color: white;
    border: 2px solid transparent;
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
}

.btn-light:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(76, 175, 80, 0.4);
    background: linear-gradient(135deg, var(--color-dark-green-text) 0%, #1B5E20 100%);
}

.btn-light:active {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(76, 175, 80, 0.4);
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        margin: 100px 1rem 20px;
        padding: 2rem 1.5rem;
        border-radius: 16px;
    }
    
    h2 {
        font-size: 2rem;
    }
    
    .input {
        padding: 0.875rem 1rem;
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    .btn {
        padding: 0.875rem 2rem;
        font-size: 1rem;
        min-width: 140px;
    }
}

/* Loading Animation */
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
<div class="container">
    <a class="back" href="cart.php">← Back to cart</a>
    <h2>Delivery Address</h2>

    <form method="post" id="addressForm" autocomplete="off">
        <div class="form-group">
            <label>Receiver's Name</label>
            <input type="text" name="receiver" class="input" value="<?= htmlspecialchars($receiver ?? '') ?>" placeholder="Enter full name of the recipient" required autocomplete="name">
            <?php if (!empty($errors['receiver'])): ?><div class="error-message"><?= htmlspecialchars($errors['receiver']) ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label>Delivery Address</label>
            <textarea name="address" class="input" rows="4" placeholder="Enter complete delivery address including street name, building number, and any landmarks" required autocomplete="address-line1"><?= htmlspecialchars($address ?? '') ?></textarea>
            <?php if (!empty($errors['address'])): ?><div class="error-message"><?= htmlspecialchars($errors['address']) ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label>Province</label>
            <select name="province" id="province" class="input" required autocomplete="address-level1">
                <option value="">Choose your province</option>
                <?php foreach ($provinces as $prov => $dists): ?>
                    <option value="<?= htmlspecialchars($prov) ?>" <?= ($province === $prov) ? 'selected' : '' ?>><?= htmlspecialchars($prov) ?> Province</option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['province'])): ?><div class="error-message"><?= htmlspecialchars($errors['province']) ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label>District</label>
            <select name="district" id="district" class="input" required autocomplete="address-level2">
                <option value="">Choose your district</option>
                <?php if ($province && isset($provinces[$province])): ?>
                    <?php foreach ($provinces[$province] as $dist): ?>
                        <option value="<?= htmlspecialchars($dist) ?>" <?= ($district === $dist) ? 'selected' : '' ?>><?= htmlspecialchars($dist) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <?php if (!empty($errors['district'])): ?><div class="error-message"><?= htmlspecialchars($errors['district']) ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label>Postal Code</label>
            <input type="text" name="postal" class="input" maxlength="5" pattern="\d{5}" value="<?= htmlspecialchars($postal ?? '') ?>" placeholder="Enter 5-digit postal code" required inputmode="numeric" autocomplete="postal-code">
            <?php if (!empty($errors['postal'])): ?><div class="error-message"><?= htmlspecialchars($errors['postal']) ?></div><?php endif; ?>
        </div>

        <div class="form-group">
            <label>Mobile Number</label>
            <input type="tel" name="mobile" class="input" maxlength="11" pattern="(0|94)?7\d{8}" value="<?= htmlspecialchars($mobile ?? '') ?>" placeholder="07XXXXXXXX or 947XXXXXXXX" required inputmode="tel" autocomplete="tel">
            <?php if (!empty($errors['mobile'])): ?><div class="error-message"><?= htmlspecialchars($errors['mobile']) ?></div><?php endif; ?>
        </div>

        <div class="form-actions">
            <button class="btn btn-light" type="submit">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
                Continue to Payment
            </button>
        </div>
    </form>
    <script>
    // Enhanced province-district dynamic select with animations
    const provinceDistricts = <?= json_encode($provinces) ?>;
    const provinceSel = document.getElementById('province');
    const districtSel = document.getElementById('district');
    const form = document.getElementById('addressForm');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Province change handler
    provinceSel.addEventListener('change', function() {
        const prov = this.value;
        
        // Add loading state
        districtSel.style.opacity = '0.5';
        districtSel.disabled = true;
        
        setTimeout(() => {
            districtSel.innerHTML = '<option value="">Choose your district</option>';
            
            if (provinceDistricts[prov]) {
                provinceDistricts[prov].forEach(function(dist) {
                    const opt = document.createElement('option');
                    opt.value = dist;
                    opt.textContent = dist;
                    districtSel.appendChild(opt);
                });
            }
            
            // Remove loading state
            districtSel.style.opacity = '1';
            districtSel.disabled = false;
        }, 200);
    });

    // Form validation and submission enhancement
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="animation: spin 1s linear infinite;">
                <path d="M12 4V2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8z"/>
            </svg>
            Processing...
        `;
        
        // Re-enable button after 3 seconds if form hasn't submitted
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    Continue to Payment
                `;
            }
        }, 3000);
    });

    // Real-time validation feedback
    const inputs = form.querySelectorAll('.input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.style.borderColor = 'var(--color-medium-green-text)';
            } else {
                this.style.borderColor = '#dc2626';
            }
        });
    });

    // Mobile number formatting
    const mobileInput = form.querySelector('input[name="mobile"]');
    mobileInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.startsWith('94')) {
            if (value.length > 11) value = value.substring(0, 11);
        } else if (value.startsWith('0')) {
            if (value.length > 10) value = value.substring(0, 10);
        }
        this.value = value;
    });

    // Postal code formatting
    const postalInput = form.querySelector('input[name="postal"]');
    postalInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 5);
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
    </script>
</div>
</body>
</html>

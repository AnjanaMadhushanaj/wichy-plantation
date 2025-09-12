<?php
require_once __DIR__ . '/config.php';

$errors = [];
$email = '';
$success = '';

// Check if redirected from signup
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success = 'Account created successfully! Please login with your credentials.';
}

// Check if redirected due to session expiration
if (isset($_GET['error']) && $_GET['error'] == 'session_expired') {
    $errors['general'] = 'Your session has expired. Please login again.';
}

// Check if user was logged out
if (isset($_GET['message']) && $_GET['message'] == 'logged_out') {
    $success = 'You have been successfully logged out. Please enter your credentials to login again.';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }
    
    // Check credentials if no validation errors
    if (!$errors) {
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Login successful - set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Role-based redirection
                if ($user['role'] === 'admin') {
                    header('Location: admin.php');
                } elseif ($user['role'] === 'employee') {
                    header('Location: employee/display_wichy_coconut.php');
                } else {
                    // Check if redirected from shop now button
                    if (isset($_GET['redirect']) && $_GET['redirect'] === 'shop') {
                        header('Location: public/items.php');
                    } else {
                        header('Location: public/items.php');
                    }
                }
                exit;
            } else {
                $errors['general'] = 'Invalid email or password.';
            }
        } else {
            $errors['general'] = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Coconut Shop</title>
    <link rel="stylesheet" href="public/style.css">
    <style>
        body {
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            margin: 48px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 32px rgba(60, 90, 130, 0.13), 0 1.5px 4px rgba(60, 90, 130, 0.08);
            padding: 36px 32px 28px 32px;
            position: relative;
        }
        h2 {
            text-align: center;
            color: #2d3a4a;
            margin-bottom: 28px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #3a4a5d;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .input, input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #b6c6d6;
            border-radius: 8px;
            font-size: 1rem;
            background: #f7fafc;
            transition: border 0.2s;
            box-sizing: border-box;
        }
        .input:focus, input:focus {
            border-color: #5b9bd5;
            outline: none;
            background: #fff;
        }
        .btn {
            background: linear-gradient(90deg, #5b9bd5 0%, #3a7bd5 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            width: 100%;
            font-size: 1.08rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(60, 90, 130, 0.08);
            margin-top: 18px;
            transition: background 0.2s;
        }
        .btn:hover {
            background: linear-gradient(90deg, #3a7bd5 0%, #5b9bd5 100%);
        }
        .error {
            color: #d32f2f;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .general-error {
            color: #d32f2f;
            font-size: 1rem;
            margin-bottom: 20px;
            text-align: center;
            background: #ffeaea;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ffcdd2;
        }
        .success-message {
            color: #2e7d32;
            font-size: 1rem;
            margin-bottom: 20px;
            text-align: center;
            background: #e8f5e8;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #c8e6c9;
        }
        .signup-link, .home-link {
            text-align: center;
            margin-top: 20px;
        }
        .signup-link a, .home-link a {
            color: #5b9bd5;
            text-decoration: none;
            font-weight: 500;
        }
        .signup-link a:hover, .home-link a:hover {
            color: #3a7bd5;
        }
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 0.95rem;
        }
        @media (max-width: 600px) {
            .login-container {
                padding: 24px 20px;
                margin: 20px 10px;
            }
            h2 {
                font-size: 1.4rem;
            }
            .links {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Welcome Back</h2>
        
        <?php if ($success): ?>
            <div class="success-message">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="general-error">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="input" 
                       value="<?= htmlspecialchars($email) ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="input" required>
                <?php if (!empty($errors['password'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['password']) ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="links">
            <div class="home-link">
                <a href="index.php">← Back to Home</a>
            </div>
            <div class="signup-link">
                <a href="signup.php">Create Account →</a>
            </div>
        </div>
    </div>
    
    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            if (!email) {
                e.preventDefault();
                alert('Please enter your email address.');
                return false;
            }
            
            if (!password) {
                e.preventDefault();
                alert('Please enter your password.');
                return false;
            }
            
            // Email format validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
        });
        
        // Auto-focus on email field
        document.getElementById('email').focus();
    </script>
</body>
</html>

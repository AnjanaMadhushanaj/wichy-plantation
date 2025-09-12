<?php
require_once __DIR__ . '/config.php';

$errors = [];
$success = '';
$username = $email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = 'customer'; // Default all new users as customer
    
    // Validation
    if (empty($username)) {
        $errors['username'] = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'Username can only contain letters, numbers, and underscores.';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }
    
    // Check if username or email already exists
    if (!$errors) {
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check_stmt->bind_param('ss', $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $existing_stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
            $existing_stmt->bind_param('i', $row['id']);
            $existing_stmt->execute();
            $existing = $existing_stmt->get_result()->fetch_assoc();
            
            if ($existing['username'] === $username) {
                $errors['username'] = 'Username already exists.';
            }
            if ($existing['email'] === $email) {
                $errors['email'] = 'Email already exists.';
            }
        }
    }
    
    // Handle profile picture upload
    $profile_picture = null;
    if (!$errors && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/public/images/profiles/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_size = $_FILES['profile_picture']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validate file
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file_ext, $allowed_ext)) {
            $errors['profile_picture'] = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
        } elseif ($file_size > $max_size) {
            $errors['profile_picture'] = 'File size must be less than 5MB.';
        } else {
            $new_name = uniqid('profile_', true) . '.' . $file_ext;
            $target_path = $upload_dir . $new_name;
            
            if (move_uploaded_file($file_tmp, $target_path)) {
                $profile_picture = 'public/images/profiles/' . $new_name;
            } else {
                $errors['profile_picture'] = 'Failed to upload profile picture.';
            }
        }
    }
    
    // Insert user if no errors
    if (!$errors) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_picture, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $username, $email, $hashed_password, $profile_picture, $role);
        
        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            header('Location: login.php?success=1');
            exit;
        } else {
            $errors['general'] = 'Error creating account: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Coconut Shop</title>
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
        .signup-container {
            max-width: 450px;
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
        .input, input[type="text"], input[type="email"], input[type="password"], input[type="file"], select {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #b6c6d6;
            border-radius: 8px;
            font-size: 1rem;
            background: #f7fafc;
            transition: border 0.2s;
            box-sizing: border-box;
        }
        .input:focus, input:focus, select:focus {
            border-color: #5b9bd5;
            outline: none;
            background: #fff;
        }
        select {
            cursor: pointer;
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: -9999px;
        }
        .file-input-button {
            background: #f7fafc;
            border: 1.5px solid #b6c6d6;
            border-radius: 8px;
            padding: 12px 14px;
            cursor: pointer;
            display: block;
            width: 100%;
            text-align: center;
            transition: background 0.2s;
            box-sizing: border-box;
        }
        .file-input-button:hover {
            background: #e0eafc;
        }
        .file-name {
            margin-top: 8px;
            font-size: 0.9rem;
            color: #666;
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
        .success {
            color: #2e7d32;
            font-size: 1rem;
            margin-bottom: 20px;
            text-align: center;
            background: #e8f5e8;
            padding: 12px;
            border-radius: 8px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #5b9bd5;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            color: #3a7bd5;
        }
        @media (max-width: 600px) {
            .signup-container {
                padding: 24px 20px;
                margin: 20px 10px;
            }
            h2 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Create Account</h2>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="error" style="text-align: center; margin-bottom: 20px;">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="input" 
                       value="<?= htmlspecialchars($username) ?>" required>
                <?php if (!empty($errors['username'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['username']) ?></div>
                <?php endif; ?>
            </div>
            
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
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="input" required>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <div class="file-input-wrapper">
                    <input type="file" id="profile_picture" name="profile_picture" 
                           accept="image/jpeg,image/jpg,image/png,image/gif">
                    <label for="profile_picture" class="file-input-button">
                        Choose Profile Picture
                    </label>
                </div>
                <div class="file-name" id="file-name"></div>
                <?php if (!empty($errors['profile_picture'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['profile_picture']) ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Create Account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
    
    <script>
        // File input display
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : '';
            document.getElementById('file-name').textContent = fileName ? 'Selected: ' + fileName : '';
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    </script>
</body>
</html>

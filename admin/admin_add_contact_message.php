<?php
// admin_add_contact_message.php: Handles admin-side contact message addition
require_once '../config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $errors = [];
    if ($name === '' || strlen($name) < 2 || strlen($name) > 100) {
        $errors[] = 'Name must be 2-100 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
        $errors[] = 'Enter a valid email address (max 150 chars).';
    }
    if ($subject === '' || strlen($subject) < 2 || strlen($subject) > 200) {
        $errors[] = 'Subject must be 2-200 characters.';
    }
    if ($message === '' || strlen($message) < 3 || strlen($message) > 1000) {
        $errors[] = 'Message must be 3-1000 characters.';
    }
    if (count($errors) === 0) {
        $stmt = $conn->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        if ($stmt->execute()) {
            header('Location: admin.php?success=contact');
            exit;
        } else {
            $errors[] = 'Error saving message.';
        }
        $stmt->close();
    }
    if (count($errors) > 0) {
        header('Location: admin.php?error=' . urlencode(implode(' ', $errors)));
        exit;
    }
} else {
    // Fallback: render a small back link if accessed directly
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Add Contact Message</title><style>body{font-family:Segoe UI,Arial,sans-serif;background:#f6f8fa;padding:32px;} .card{max-width:520px;margin:40px auto;background:#fff;border:1px solid #e1e4e8;border-radius:10px;box-shadow:0 2px 12px #0001;padding:24px;text-align:center;} .btn{display:inline-block;margin-top:8px;padding:10px 16px;border-radius:8px;background:#2E7D32;color:#fff;text-decoration:none;font-weight:600;} .btn.secondary{background:#f7fafc;color:#2d3a4b;border:1px solid #e0e6ed;} .btn.secondary:hover{background:#eef4fb;} </style></head><body><div class="card"><h2>Admin Add Contact Message</h2><p>This endpoint expects a POST submission from the dashboard form.</p><a href="admin.php" class="btn">Go to Dashboard</a><br><a href="#" class="btn secondary" onclick="if(history.length>1){history.back();}else{location.href=\'admin.php\';}return false;">Go Back</a></div></body></html>';
    exit;
}

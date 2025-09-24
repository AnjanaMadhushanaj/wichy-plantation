<?php
// admin_add_comment.php: Handles admin-side comment addition
require_once '../config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    $errors = [];
    if ($user_name === '' || strlen($user_name) < 2 || strlen($user_name) > 50) {
        $errors[] = 'Name must be 2-50 characters.';
    }
    if ($comment === '' || strlen($comment) < 3 || strlen($comment) > 500) {
        $errors[] = 'Comment must be 3-500 characters.';
    }
    if (count($errors) === 0) {
        $stmt = $conn->prepare('INSERT INTO comments (user_name, comment) VALUES (?, ?)');
        $stmt->bind_param('ss', $user_name, $comment);
        if ($stmt->execute()) {
            header('Location: admin.php?success=comment');
            exit;
        } else {
            $errors[] = 'Error saving comment.';
        }
        $stmt->close();
    }
    if (count($errors) > 0) {
        header('Location: admin.php?error=' . urlencode(implode(' ', $errors)));
        exit;
    }
} else {
    // Fallback: render a small back link if accessed directly
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Add Comment</title><style>body{font-family:Segoe UI,Arial,sans-serif;background:#f6f8fa;padding:32px;} .card{max-width:520px;margin:40px auto;background:#fff;border:1px solid #e1e4e8;border-radius:10px;box-shadow:0 2px 12px #0001;padding:24px;text-align:center;} .btn{display:inline-block;margin-top:8px;padding:10px 16px;border-radius:8px;background:#2E7D32;color:#fff;text-decoration:none;font-weight:600;} .btn.secondary{background:#f7fafc;color:#2d3a4b;border:1px solid #e0e6ed;} .btn.secondary:hover{background:#eef4fb;} </style></head><body><div class="card"><h2>Admin Add Comment</h2><p>This endpoint expects a POST submission from the dashboard form.</p><a href="admin.php" class="btn">Go to Dashboard</a><br><a href="#" class="btn secondary" onclick="if(history.length>1){history.back();}else{location.href=\'admin.php\';}return false;">Go Back</a></div></body></html>';
    exit;
}

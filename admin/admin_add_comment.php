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
    header('Location: admin.php');
    exit;
}

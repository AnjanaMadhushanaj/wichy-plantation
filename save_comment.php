<?php
// save_comment.php: Handles saving user comments to the database
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    // Validation rules
    $errors = [];
    if ($user_name === '' || strlen($user_name) < 2 || strlen($user_name) > 50) {
        $errors[] = 'Name must be between 2 and 50 characters.';
    }
    if (!preg_match('/^[a-zA-Z0-9_\s]+$/', $user_name)) {
        $errors[] = 'Name can only contain letters, numbers, spaces, and underscores.';
    }
    if ($comment === '' || strlen($comment) < 3 || strlen($comment) > 500) {
        $errors[] = 'Comment must be between 3 and 500 characters.';
    }

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    } else {
        $stmt = $conn->prepare('INSERT INTO comments (user_name, comment) VALUES (?, ?)');
        $stmt->bind_param('ss', $user_name, $comment);
        if ($stmt->execute()) {
            echo 'Comment saved successfully!';
        } else {
            echo 'Error saving comment.';
        }
        $stmt->close();
    }
} else {
    echo 'Invalid request.';
}
?>

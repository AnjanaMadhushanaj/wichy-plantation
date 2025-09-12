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
    header('Location: admin.php');
    exit;
}

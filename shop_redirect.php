<?php
require_once __DIR__ . '/config.php';

// Always redirect to login page when Shop Now is clicked
header('Location: login.php?redirect=shop');
exit;
?>

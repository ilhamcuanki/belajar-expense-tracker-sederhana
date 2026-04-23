<?php
// auth/check_auth.php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . APP_URL . "/auth/login.php");
    exit;
}
?>
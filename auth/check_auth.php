<?php
// auth/check_auth.php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    // Pastikan APP_URL terdefinisi sebelum redirect
    if (!defined('APP_URL')) {
        require_once __DIR__ . '/../config.php';
    }
    header("Location: " . APP_URL . "/auth/login.php");
    exit; // ✅ WAJIB: hentikan eksekusi setelah redirect
}
?>
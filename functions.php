<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function formatRupiah(float $angka): string {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $message];
}

function getFlashMessage(): string {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $icon = $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation';
        return "<div class='alert alert-{$flash['type']}'><i class='fa-solid {$icon}'></i> {$flash['msg']}</div>";
    }
    return '';
}

function uploadBukti($file, $folder) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'msg' => 'Upload tidak valid.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) return ['success' => false, 'msg' => 'Gagal upload file.'];
    if ($file['size'] > 2 * 1024 * 1024) return ['success' => false, 'msg' => 'Ukuran file maksimal 2MB.'];
    
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return ['success' => false, 'msg' => 'Format tidak didukung (jpg, png, webp).'];
    
    $dir = __DIR__ . '/uploads/' . $folder;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    
    $filename = uniqid('img_', true) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
        return ['success' => false, 'msg' => 'Gagal menyimpan file.'];
    }
    
    return ['success' => true, 'filename' => $folder . '/' . $filename];
}
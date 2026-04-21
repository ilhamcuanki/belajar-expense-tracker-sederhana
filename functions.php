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
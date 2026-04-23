<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . APP_URL . "/index.php"); exit;
}

$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // Cek email duplikat
    $stmt = getDB()->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "Email sudah terdaftar.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = getDB()->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$nama, $email, $hash])) {
            $success = "Registrasi berhasil. Silakan login.";
        } else {
            $error = "Gagal membuat akun. Coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Expense Tracker</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f8fafc; }
        .auth-card { background: white; padding: 2.5rem; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.08); width: 100%; max-width: 400px; }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-header i { font-size: 3rem; color: var(--primary); margin-bottom: 0.5rem; }
        .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--muted); }
        .auth-footer a { color: var(--primary); text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-header">
        <i class="fa-solid fa-user-plus"></i>
        <h2>Buat Akun Baru</h2>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= $success ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" required placeholder="John Doe">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="nama@email.com">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Minimal 6 karakter">
        </div>
        <button type="submit" class="btn" style="width:100%; justify-content:center; margin-top:0.5rem;">
            <i class="fa-solid fa-user-check"></i> Daftar
        </button>
    </form>
    <div class="auth-footer">
        Sudah punya akun? <a href="login.php">Masuk di sini</a>
    </div>
</div>
</body>
</html>
<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../functions.php';

// Jika sudah login, lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: " . APP_URL . "/index.php"); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $stmt = getDB()->prepare("SELECT id, nama, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        header("Location: " . APP_URL . "/index.php"); exit;
    } else {
        $error = "Email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Expense Tracker</title>
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
        <i class="fa-solid fa-wallet"></i>
        <h2>Masuk ke Akun</h2>
        <p style="color:var(--muted)">Kelola keuangan Anda dengan aman</p>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="nama@email.com">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn" style="width:100%; justify-content:center; margin-top:0.5rem;">
            <i class="fa-solid fa-right-to-bracket"></i> Masuk
        </button>
    </form>
    <div class="auth-footer">
        Belum punya akun? <a href="register.php">Daftar sekarang</a>
    </div>
</div>
</body>
</html>
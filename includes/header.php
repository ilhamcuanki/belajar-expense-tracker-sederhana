<?php
require_once __DIR__ . '/../functions.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Expense Tracker') ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <nav class="navbar">
    <a href="<?= APP_URL ?>"><i class="fa-solid fa-wallet"></i> Expense Tracker</a>
    <div class="nav-links">
        <a href="<?= APP_URL ?>/jurnal/daftar.php"><i class="fa-solid fa-book-open"></i> Jurnal</a>
        
        <!-- ✅ DROPDOWN BUDGET -->
        <div class="nav-dropdown">
            <button class="nav-dropdown-btn">
                <i class="fa-solid fa-bullseye"></i> Budget 
                <i class="fa-solid fa-chevron-down" style="font-size:0.7em; margin-left:4px;"></i>
            </button>
            <div class="dropdown-menu">
                <a href="<?= APP_URL ?>/budget/pantau.php"><i class="fa-solid fa-chart-column"></i> Budget Bulanan</a>
                <a href="<?= APP_URL ?>/budget/target/index.php"><i class="fa-solid fa-piggy-bank"></i> Target Tabungan</a>
            </div>
        </div>
        <!-- ✅ AKHIR DROPDOWN -->

        <a href="<?= APP_URL ?>/laporan/index.php"><i class="fa-solid fa-chart-pie"></i> Laporan</a>
        
        <!-- Info User & Logout -->
        <div style="border-left: 1px solid #e2e8f0; padding-left: 1rem; display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 0.9rem; font-weight: 500;">
                <i class="fa-regular fa-user"></i> <?= htmlspecialchars($_SESSION['user_nama'] ?? 'User') ?>
            </span>
            <a href="<?= APP_URL ?>/auth/logout.php" class="btn btn-sm" style="background: #ef4444; padding: 0.4rem 0.8rem;">
                <i class="fa-solid fa-power-off"></i> Keluar
            </a>
        </div>
    </div>
</nav>
    <main class="container">
        <?php echo getFlashMessage(); ?>
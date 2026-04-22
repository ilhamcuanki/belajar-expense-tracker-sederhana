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

            <div class="nav-dropdown">
                <button class="nav-dropdown-btn"><i class="fa-solid fa-bullseye"></i> Budget <i class="fa-solid fa-chevron-down" style="font-size:0.7em; margin-left:4px;"></i></button>
                <div class="dropdown-menu">
                    <a href="<?= APP_URL ?>/budget/pantau.php"><i class="fa-solid fa-chart-column"></i> Budget Bulanan</a>
                    <a href="<?= APP_URL ?>/budget/target/index.php"><i class="fa-solid fa-piggy-bank"></i> Target Tabungan</a>
                </div>
            </div>

            <a href="<?= APP_URL ?>/laporan/index.php"><i class="fa-solid fa-chart-pie"></i> Laporan</a>
        </div>
    </nav>
    <main class="container">
        <?php echo getFlashMessage(); ?>
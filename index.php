<?php
// 1. Load konfigurasi & fungsi global DULU agar APP_URL dan session tersedia
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// 2. Set judul halaman untuk header
$pageTitle = "Dashboard";

// 3. Muat template header (ini akan memuat HTML, CSS, dan Navbar)
require_once __DIR__ . '/includes/header.php';
?>

<!-- 4. Konten Utama Dashboard -->
<div class="card" style="text-align: center; padding: 3rem;">
    <h1><i class="fa-solid fa-house"></i> Selamat Datang di Expense Tracker</h1>
    <p style="color: var(--muted); margin-bottom: 2rem;">
        Pilih modul di bawah ini untuk mulai mengelola keuangan Anda.
    </p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; max-width: 800px; margin: 0 auto;">
        <!-- Kartu Jurnal -->
        <a href="<?= APP_URL ?>/jurnal/daftar.php" class="card" style="text-decoration: none; color: inherit; border: 1px solid #e2e8f0; transition: transform 0.2s;">
            <div style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"><i class="fa-solid fa-book-open"></i></div>
            <h3 style="margin: 0;">Jurnal Umum</h3>
            <p style="font-size: 0.9rem; color: var(--muted); margin: 0.5rem 0 0;">Catat transaksi harian</p>
        </a>

        <!-- Kartu Budget -->
        <a href="<?= APP_URL ?>/budget/pantau.php" class="card" style="text-decoration: none; color: inherit; border: 1px solid #e2e8f0; transition: transform 0.2s;">
            <div style="font-size: 2rem; color: var(--warning); margin-bottom: 0.5rem;"><i class="fa-solid fa-bullseye"></i></div>
            <h3 style="margin: 0;">Budget</h3>
            <p style="font-size: 0.9rem; color: var(--muted); margin: 0.5rem 0 0;">Pantau batas pengeluaran</p>
        </a>

        <!-- Kartu Laporan -->
        <a href="<?= APP_URL ?>/laporan/index.php" class="card" style="text-decoration: none; color: inherit; border: 1px solid #e2e8f0; transition: transform 0.2s;">
            <div style="font-size: 2rem; color: var(--success); margin-bottom: 0.5rem;"><i class="fa-solid fa-chart-pie"></i></div>
            <h3 style="margin: 0;">Laporan</h3>
            <p style="font-size: 0.9rem; color: var(--muted); margin: 0.5rem 0 0;">Analisa keuangan bulanan</p>
        </a>
    </div>
</div>

<!-- 5. Muat footer untuk menutup tag HTML </body></html> -->
<?php require_once __DIR__ . '/includes/footer.php'; ?>
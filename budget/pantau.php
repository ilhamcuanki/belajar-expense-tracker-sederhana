<?php
// budget/pantau.php
$pageTitle = "Pantau Budget";
require_once __DIR__ . '/../auth/check_auth.php';
require_once __DIR__ . '/../includes/header.php';
$pdo = getDB();

// Ambil parameter filter (default: bulan & tahun sekarang)
$bulan = (int)($_GET['bulan'] ?? date('n'));
$tahun = (int)($_GET['tahun'] ?? date('Y'));

// Ambil semua budget yang diset untuk periode ini
$stmt = $pdo->prepare("
    SELECT b.*, k.nama as kategori_nama 
    FROM budget b 
    JOIN kategori k ON b.kategori_id = k.id 
    WHERE b.bulan = ? AND b.tahun = ?
    ORDER BY k.nama
");
$stmt->execute([$bulan, $tahun]);
$budgets = $stmt->fetchAll();

$bulan_nama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
?>

<h1><i class="fa-solid fa-chart-column"></i> Pantau Budget</h1>

<!-- Filter Periode -->
<form method="GET" style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap;">
    <div class="form-group" style="margin-bottom: 0;">
        <label>Bulan</label>
        <select name="bulan">
            <?php foreach ($bulan_nama as $i => $nama): ?>
                <option value="<?= $i + 1 ?>" <?= ($i + 1 == $bulan) ? 'selected' : '' ?>><?= $nama ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group" style="margin-bottom: 0;">
        <label>Tahun</label>
        <input type="number" name="tahun" value="<?= $tahun ?>" min="2020" max="2030">
    </div>
    <button type="submit" class="btn btn-sm">
        <i class="fa-solid fa-filter"></i> Tampilkan
    </button>
    <a href="atur.php" class="btn btn-sm" style="background: var(--primary-dark);">
        <i class="fa-solid fa-plus"></i> Atur Budget
    </a>
</form>

<!-- Daftar Budget Cards -->
<div style="display: grid; gap: 1rem;">
    <?php if (empty($budgets)): ?>
        <div class="card" style="text-align: center; color: var(--muted);">
            <i class="fa-regular fa-circle-question" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
            <p>Belum ada budget yang diatur untuk periode <?= $bulan_nama[$bulan-1] ?> <?= $tahun ?>.</p>
            <a href="atur.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">Atur budget sekarang →</a>
        </div>
    <?php else: ?>
        <?php foreach ($budgets as $b): 
            // Hitung realisasi: SUM dari transaksi pengeluaran di kategori & periode ini
            $stmt_real = $pdo->prepare("
                SELECT COALESCE(SUM(jumlah), 0) as total 
                FROM transaksi 
                WHERE kategori_id = ? 
                AND tipe = 'pengeluaran' 
                AND MONTH(tanggal) = ? 
                AND YEAR(tanggal) = ?
            ");
            $stmt_real->execute([$b['kategori_id'], $bulan, $tahun]);
            $realisasi = (float)$stmt_real->fetchColumn();
            
            // Hitung persentase & tentukan status visual
            $persen = ($b['jumlah_batas'] > 0) ? min(100, ($realisasi / $b['jumlah_batas']) * 100) : 0;
            $sisa = $b['jumlah_batas'] - $realisasi;
            
            // Tentukan kelas CSS untuk progress bar
            $status_class = '';
            if ($persen >= 100) $status_class = 'over';
            elseif ($persen >= 75) $status_class = 'warn';
            // else default: success (hijau)
        ?>
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h3 style="margin: 0; font-size: 1.1rem;">
                    <i class="fa-solid fa-tag"></i> <?= htmlspecialchars($b['kategori_nama']) ?>
                </h3>
                <span style="font-size: 0.9rem; color: var(--muted);">
                    Batas: <?= formatRupiah($b['jumlah_batas']) ?>
                </span>
            </div>
            
            <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 0.25rem;">
                <span>Terpakai: <strong><?= formatRupiah($realisasi) ?></strong></span>
                <span>Sisa: <strong style="color: <?= $sisa < 0 ? 'var(--danger)' : 'var(--success)' ?>">
                    <?= formatRupiah(max(0, $sisa)) ?>
                </strong></span>
            </div>
            
            <!-- Progress Bar -->
            <div class="progress-bar">
                <div class="progress-fill <?= $status_class ?>" style="width: <?= $persen ?>%"></div>
            </div>
            
            <!-- Indikator Teks -->
            <div style="font-size: 0.85rem; color: var(--muted); margin-top: 0.25rem;">
                <?= number_format($persen, 1) ?>% terpakai
                <?php if ($sisa < 0): ?>
                    <span style="color: var(--danger); font-weight: 500; margin-left: 0.5rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Melebihi batas <?= formatRupiah(abs($sisa)) ?>
                    </span>
                <?php elseif ($persen >= 90): ?>
                    <span style="color: var(--warning); font-weight: 500; margin-left: 0.5rem;">
                        <i class="fa-solid fa-circle-exclamation"></i> Hampir habis!
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
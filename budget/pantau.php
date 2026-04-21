<?php
$pageTitle = "Pantau Budget";
require_once __DIR__ . '/../includes/header.php';
$pdo = getDB();

$bulan = (int)($_GET['bulan'] ?? date('n'));
$tahun = (int)($_GET['tahun'] ?? date('Y'));

// Ambil budget yang diset
$stmt = $pdo->prepare("SELECT b.*, k.nama as kategori_nama FROM budget b JOIN kategori k ON b.kategori_id = k.id WHERE b.bulan = ? AND b.tahun = ?");
$stmt->execute([$bulan, $tahun]);
$budgets = $stmt->fetchAll();

$bulan_nama = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
?>

<h1><i class="fa-solid fa-chart-column"></i> Pantau Budget</h1>
<form method="GET" style="margin-bottom:1rem; display:flex; gap:0.5rem; align-items:end;">
    <div class="form-group" style="margin-bottom:0"><label>Bulan</label><select name="bulan"><?php foreach($bulan_nama as $i=>$b): ?><option value="<?= $i+1 ?>" <?= $i+1==$bulan?'selected':'' ?>><?= $b ?></option><?php endforeach; ?></select></div>
    <div class="form-group" style="margin-bottom:0"><label>Tahun</label><input type="number" name="tahun" value="<?= $tahun ?>"></div>
    <button type="submit" class="btn btn-sm"><i class="fa-solid fa-filter"></i> Filter</button>
</form>

<div style="display:grid; gap:1rem;">
<?php foreach ($budgets as $b): 
    // Hitung realisasi
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(jumlah),0) as realisasi FROM transaksi WHERE kategori_id=? AND tipe='pengeluaran' AND MONTH(tanggal)=? AND YEAR(tanggal)=?");
    $stmt->execute([$b['kategori_id'], $bulan, $tahun]);
    $realisasi = (float)$stmt->fetchColumn();
    
    $persen = ($b['jumlah_batas'] > 0) ? min(100, ($realisasi / $b['jumlah_batas']) * 100) : 0;
    $status = $persen >= 100 ? 'over' : ($persen >= 75 ? 'warn' : '');
    $sisa = $b['jumlah_batas'] - $realisasi;
?>
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
            <h2 style="margin:0; font-size:1.1rem;"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($b['kategori_nama']) ?></h2>
            <span style="font-size:0.9rem; color:var(--muted)">Batas: <?= formatRupiah($b['jumlah_batas']) ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:0.9rem; margin-bottom:0.25rem;">
            <span>Terpakai: <?= formatRupiah($realisasi) ?></span>
            <span>Sisa: <?= formatRupiah(max(0, $sisa)) ?></span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill <?= $status ?>" style="width: <?= $persen ?>%"></div>
        </div>
        <?php if ($sisa < 0): ?>
            <div class="alert alert-danger" style="margin-top:0.5rem"><i class="fa-solid fa-triangle-exclamation"></i> Melebihi batas <?= formatRupiah(abs($sisa)) ?></div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<?php if (empty($budgets)): ?>
    <div class="card" style="text-align:center; color:var(--muted)">Belum ada budget yang diatur untuk periode ini. <a href="atur.php">Atur sekarang</a></div>
<?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
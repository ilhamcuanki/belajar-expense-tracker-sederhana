<?php
$pageTitle = "Laporan Keuangan";
require_once __DIR__ . '/../includes/header.php';
$pdo = getDB();

$mulai = sanitize($_GET['mulai'] ?? date('Y-m-01'));
$akhir = sanitize($_GET['akhir'] ?? date('Y-m-t'));

// Agregasi per kategori
$stmt = $pdo->prepare("SELECT k.nama, t.tipe, SUM(t.jumlah) as total FROM transaksi t JOIN kategori k ON t.kategori_id = k.id WHERE t.tanggal BETWEEN ? AND ? GROUP BY k.nama, t.tipe ORDER BY k.nama");
$stmt->execute([$mulai, $akhir]);
$ringkasan = $stmt->fetchAll();

$total_masuk = 0;
$total_keluar = 0;
foreach ($ringkasan as $r) {
    if ($r['tipe'] === 'pemasukan') $total_masuk += $r['total'];
    else $total_keluar += $r['total'];
}
$saldo = $total_masuk - $total_keluar;
?>

<h1><i class="fa-solid fa-chart-pie"></i> Laporan Keuangan</h1>
<form method="GET" style="display:flex; gap:0.5rem; align-items:end; margin-bottom:1.5rem;">
    <div class="form-group" style="margin-bottom:0"><label>Dari Tanggal</label><input type="date" name="mulai" value="<?= htmlspecialchars($mulai) ?>"></div>
    <div class="form-group" style="margin-bottom:0"><label>Sampai Tanggal</label><input type="date" name="akhir" value="<?= htmlspecialchars($akhir) ?>"></div>
    <button type="submit" class="btn btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Generate</button>
</form>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
    <div class="card" style="text-align:center"><h3 style="color:var(--success)"><i class="fa-solid fa-arrow-trend-up"></i> Pemasukan</h3><p style="font-size:1.25rem; font-weight:700"><?= formatRupiah($total_masuk) ?></p></div>
    <div class="card" style="text-align:center"><h3 style="color:var(--danger)"><i class="fa-solid fa-arrow-trend-down"></i> Pengeluaran</h3><p style="font-size:1.25rem; font-weight:700"><?= formatRupiah($total_keluar) ?></p></div>
    <div class="card" style="text-align:center"><h3 style="color:var(--primary-dark)"><i class="fa-solid fa-scale-balanced"></i> Saldo Bersih</h3><p style="font-size:1.25rem; font-weight:700"><?= formatRupiah($saldo) ?></p></div>
</div>

<div class="card">
    <h2><i class="fa-solid fa-list-check"></i> Rincian per Kategori</h2>
    <?php if (empty($ringkasan)): ?>
        <p style="color:var(--muted)">Tidak ada data transaksi pada rentang tanggal ini.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Kategori</th><th>Tipe</th><th style="text-align:right">Total</th></tr></thead>
            <tbody>
                <?php foreach ($ringkasan as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['nama']) ?></td>
                        <td><span style="color:<?= $r['tipe']=='pemasukan'?'var(--success)':'var(--danger)' ?>"><?= ucfirst($r['tipe']) ?></span></td>
                        <td style="text-align:right; font-weight:600"><?= formatRupiah($r['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
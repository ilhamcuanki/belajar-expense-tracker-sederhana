<?php
// laporan/index.php
$pageTitle = "Laporan Keuangan";
require_once __DIR__ . '/../includes/header.php';
$pdo = getDB();

// 1. LOGIKA FILTER & PENENTUAN RENTANG TANGGAL
$filter = $_GET['filter'] ?? 'bulanan';
$mulai = sanitize($_GET['mulai'] ?? date('Y-m-d'));
$akhir = sanitize($_GET['akhir'] ?? date('Y-m-d'));

if ($filter === 'harian') {
    $mulai = date('Y-m-d');
    $akhir = date('Y-m-d');
} elseif ($filter === 'mingguan') {
    $mulai = date('Y-m-d', strtotime('monday this week'));
    $akhir = date('Y-m-d', strtotime('sunday this week'));
} elseif ($filter === 'bulanan') {
    $mulai = date('Y-m-01');
    $akhir = date('Y-m-t');
}
// filter 'custom' menggunakan input manual dari user

// 2. QUERY AGREGASI DATA
$stmt = $pdo->prepare("
    SELECT k.nama as kategori_nama, t.tipe, SUM(t.jumlah) as total
    FROM transaksi t
    JOIN kategori k ON t.kategori_id = k.id
    WHERE t.tanggal BETWEEN ? AND ?
    GROUP BY k.nama, t.tipe
    ORDER BY k.nama, t.tipe
");
$stmt->execute([$mulai, $akhir]);
$detail = $stmt->fetchAll();

// 3. KALKULASI RINGKASAN DI PHP
$ringkasan = ['pemasukan' => 0, 'pengeluaran' => 0];
foreach ($detail as $d) {
    $ringkasan[$d['tipe']] += $d['total'];
}
$saldo = $ringkasan['pemasukan'] - $ringkasan['pengeluaran'];

// Format tanggal untuk tampilan
$format_tgl = date('d M Y', strtotime($mulai)) . ' - ' . date('d M Y', strtotime($akhir));
?>

<h1><i class="fa-solid fa-chart-pie"></i> Laporan Keuangan</h1>

<!-- FORM FILTER -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" id="filterForm" style="display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Periode</label>
            <select name="filter" id="filterSelect">
                <option value="harian" <?= $filter == 'harian' ? 'selected' : '' ?>>Harian</option>
                <option value="mingguan" <?= $filter == 'mingguan' ? 'selected' : '' ?>>Mingguan</option>
                <option value="bulanan" <?= $filter == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                <option value="custom" <?= $filter == 'custom' ? 'selected' : '' ?>>Rentang Kustom</option>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label>Dari</label>
            <input type="date" name="mulai" id="dateMulai" value="<?= htmlspecialchars($mulai) ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Sampai</label>
            <input type="date" name="akhir" id="dateAkhir" value="<?= htmlspecialchars($akhir) ?>">
        </div>
        
        <button type="submit" class="btn btn-sm">
            <i class="fa-solid fa-filter"></i> Generate
        </button>
    </form>
</div>

<!-- KARTU RINGKASAN -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="text-align: center; border-left: 4px solid var(--success);">
        <h3 style="color: var(--muted); font-size: 0.9rem; margin-bottom: 0.5rem;"><i class="fa-solid fa-arrow-trend-up"></i> Total Pemasukan</h3>
        <p style="font-size: 1.5rem; font-weight: 700; color: var(--success);"><?= formatRupiah($ringkasan['pemasukan']) ?></p>
        <small style="color: var(--muted);"><?= $format_tgl ?></small>
    </div>
    
    <div class="card" style="text-align: center; border-left: 4px solid var(--danger);">
        <h3 style="color: var(--muted); font-size: 0.9rem; margin-bottom: 0.5rem;"><i class="fa-solid fa-arrow-trend-down"></i> Total Pengeluaran</h3>
        <p style="font-size: 1.5rem; font-weight: 700; color: var(--danger);"><?= formatRupiah($ringkasan['pengeluaran']) ?></p>
        <small style="color: var(--muted);"><?= $format_tgl ?></small>
    </div>
    
    <div class="card" style="text-align: center; border-left: 4px solid var(--primary);">
        <h3 style="color: var(--muted); font-size: 0.9rem; margin-bottom: 0.5rem;"><i class="fa-solid fa-scale-balanced"></i> Saldo Bersih</h3>
        <p style="font-size: 1.5rem; font-weight: 700; color: <?= $saldo >= 0 ? 'var(--primary)' : 'var(--danger)' ?>;">
            <?= formatRupiah($saldo) ?>
        </p>
        <small style="color: var(--muted);"><?= $format_tgl ?></small>
    </div>
</div>

<!-- TABEL RINCIAN -->
<div class="card">
    <h2><i class="fa-solid fa-list-check"></i> Rincian per Kategori</h2>
    <?php if (empty($detail)): ?>
        <div style="text-align: center; padding: 2rem; color: var(--muted);">
            <i class="fa-regular fa-folder-open" style="font-size: 2.5rem; margin-bottom: 0.5rem; display: block;"></i>
            Tidak ada transaksi pada rentang tanggal <?= $format_tgl ?>.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Tipe</th>
                    <th style="text-align: right;">Jumlah Transaksi</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detail as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['kategori_nama']) ?></td>
                        <td>
                            <span style="color: <?= $row['tipe'] == 'pemasukan' ? 'var(--success)' : 'var(--danger)' ?>">
                                <i class="fa-solid fa-<?= $row['tipe'] == 'pemasukan' ? 'arrow-up' : 'arrow-down' ?>"></i>
                                <?= ucfirst($row['tipe']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">-</td> <!-- Bisa dikembangkan dengan COUNT() -->
                        <td style="text-align: right; font-weight: 600;"><?= formatRupiah($row['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- SCRIPT UX: AUTO-UPDATE TANGGAL BERDASARKAN FILTER -->
<script>
document.getElementById('filterSelect').addEventListener('change', function() {
    const filter = this.value;
    const dateMulai = document.getElementById('dateMulai');
    const dateAkhir = document.getElementById('dateAkhir');
    
    let start = '', end = '';
    const today = new Date();
    const fmt = d => d.toISOString().split('T')[0];
    
    if (filter === 'harian') {
        start = end = fmt(today);
    } else if (filter === 'mingguan') {
        const day = today.getDay() || 7; // 1=Senin, 7=Minggu
        const mon = new Date(today);
        mon.setDate(today.getDate() - day + 1);
        const sun = new Date(today);
        sun.setDate(today.getDate() + (7 - day));
        start = fmt(mon); end = fmt(sun);
    } else if (filter === 'bulanan') {
        start = fmt(new Date(today.getFullYear(), today.getMonth(), 1));
        end = fmt(new Date(today.getFullYear(), today.getMonth() + 1, 0));
    }
    
    if (start && end) {
        dateMulai.value = start;
        dateAkhir.value = end;
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
$pageTitle = "Jurnal Umum";
require_once __DIR__ . '/../includes/header.php';
$pdo = getDB();

// Hapus transaksi jika ada request POST
if (isset($_POST['hapus_id'])) {
    $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id = ?");
    $stmt->execute([(int)$_POST['hapus_id']]);
    setFlashMessage('success', 'Transaksi dihapus.');
    header("Location: daftar.php");
    exit;
}

// Filter pencarian
$cari = sanitize($_GET['cari'] ?? '');
$stmt = $pdo->prepare("SELECT t.*, k.nama as kategori_nama FROM transaksi t JOIN kategori k ON t.kategori_id = k.id WHERE t.deskripsi LIKE ? OR k.nama LIKE ? ORDER BY t.tanggal DESC");
$stmt->execute(["%$cari%", "%$cari%"]);
$transaksi = $stmt->fetchAll();
?>

<h1><i class="fa-solid fa-book-open"></i> Jurnal Umum</h1>
<div class="card" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
    <form method="GET" style="flex:1; display:flex; gap:0.5rem;">
        <input type="text" name="cari" placeholder="Cari deskripsi/kategori..." value="<?= htmlspecialchars($cari) ?>">
        <button type="submit" class="btn btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
    </form>
    <a href="tambah.php" class="btn btn-sm"><i class="fa-solid fa-plus"></i> Tambah Baru</a>
</div>

<div class="card">
    <?php if (empty($transaksi)): ?>
        <p style="color:var(--muted)">Belum ada transaksi.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th style="width:80px; text-align: center;">Bukti</th>
                    <th style="text-align:right">Jumlah</th>
                    <th style="width: 80px; text-align: center;">Aksi</th>
                </tr>
            </thead>
<tbody>
    <?php foreach ($transaksi as $t): ?>
        <tr>
            <!-- Kolom Tanggal -->
            <td style="vertical-align: middle; padding: 0.75rem;">
                <?= date('d M Y', strtotime($t['tanggal'])) ?>
            </td>

            <!-- Kolom Kategori -->
            <td style="vertical-align: middle; padding: 0.75rem;">
                <span class="badge" style="color:<?= $t['tipe'] == 'pemasukan' ? 'var(--success)' : 'var(--danger)' ?>">
                    <?= htmlspecialchars($t['kategori_nama']) ?>
                </span>
            </td>

            <!-- Kolom Deskripsi -->
            <td style="vertical-align: middle; padding: 0.75rem;">
                <?= htmlspecialchars($t['deskripsi']) ?>
            </td>

            <!-- Kolom Bukti -->
            <td style="vertical-align: middle; padding: 0.75rem; text-align: center;">
                <?php if ($t['bukti_foto']): ?>
                    <a href="<?= APP_URL ?>/uploads/<?= htmlspecialchars($t['bukti_foto']) ?>" target="_blank">
                        <img src="<?= APP_URL ?>/uploads/<?= htmlspecialchars($t['bukti_foto']) ?>" alt="Bukti" style="width:40px; height:40px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0;">
                    </a>
                <?php else: ?>
                    <span style="color:var(--muted); font-size:0.8rem">-</span>
                <?php endif; ?>
            </td>

            <!-- Kolom Jumlah -->
            <td style="text-align: right; font-weight: 600; vertical-align: middle; padding: 0.75rem;">
                <?= formatRupiah($t['jumlah']) ?>
            </td>

            <!-- ✅ KOLOM AKSI (SUDAH DIPERBAIKI) -->
            <td style="vertical-align: middle; text-align: center; width: 80px; padding: 4px;">
                <form method="POST" style="display: inline-flex; margin: 0; padding: 0;" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                    <input type="hidden" name="hapus_id" value="<?= $t['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" style="width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-trash" style="margin: 0;"></i>
                    </button>
                </form>
            </td>
            <!-- ✅ AKHIR KOLOM AKSI -->
        </tr>
    <?php endforeach; ?>
</tbody>
        </table>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
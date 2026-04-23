<?php
$pageTitle = "Kelola Target";
require_once __DIR__ . '/../../auth/check_auth.php';
require_once __DIR__ . '/../../includes/header.php';
$pdo = getDB();

$id = (int)($_GET['id'] ?? 0);
$aksi = $_GET['aksi'] ?? 'setor';
$stmt = $pdo->prepare("SELECT * FROM target_budget WHERE id = ? AND status='aktif'");
$stmt->execute([$id]);
$target = $stmt->fetch();

if (!$target) { setFlashMessage('danger', 'Target tidak ditemukan.'); header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah = filter_var($_POST['jumlah'], FILTER_VALIDATE_FLOAT);
    $ket = sanitize($_POST['keterangan'] ?? '');
    $tgl = date('Y-m-d');

    if ($jumlah > 0) {
        $pdo->beginTransaction();
        try {
            if ($aksi === 'tarik') {
                if ($jumlah > $target['terkumpul_jumlah']) {
                    throw new Exception("Dana tidak mencukupi. Saldo: " . formatRupiah($target['terkumpul_jumlah']));
                }
                $stmtUpd = $pdo->prepare("UPDATE target_budget SET terkumpul_jumlah = terkumpul_jumlah - ? WHERE id = ?");
            } else {
                $stmtUpd = $pdo->prepare("UPDATE target_budget SET terkumpul_jumlah = terkumpul_jumlah + ? WHERE id = ?");
            }
            $stmtUpd->execute([$jumlah, $id]);
            
            $pdo->prepare("INSERT INTO target_riwayat (target_id, tipe, jumlah, keterangan, tanggal) VALUES (?, ?, ?, ?, ?)")
                 ->execute([$id, $aksi, $jumlah, $ket, $tgl]);
            
            $pdo->commit();
            setFlashMessage('success', ucfirst($aksi) . " berhasil.");
            header("Location: index.php"); exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            setFlashMessage('danger', $e->getMessage());
        }
    } else {
        setFlashMessage('danger', 'Jumlah harus lebih dari 0.');
    }
}
?>
<h1><i class="fa-solid fa-<?= $aksi=='tarik'?'arrow-up':'arrow-down' ?>"></i> <?= ucfirst($aksi) ?> Dana: <?= htmlspecialchars($target['nama_target']) ?></h1>
<div class="card" style="max-width:500px; margin:0 auto;">
    <div style="display:flex; justify-content:space-between; margin-bottom:1rem; padding-bottom:0.5rem; border-bottom:1px solid #e2e8f0;">
        <span>Target:</span> <strong><?= formatRupiah($target['target_jumlah']) ?></strong>
    </div>
    <div style="display:flex; justify-content:space-between; margin-bottom:1.5rem;">
        <span>Saldo Saat Ini:</span> <strong style="color:var(--primary)"><?= formatRupiah($target['terkumpul_jumlah']) ?></strong>
    </div>

    <form method="POST">
        <div class="form-group">
            <label>Jumlah (Rp)</label>
            <input type="number" name="jumlah" min="1000" step="1000" required placeholder="0">
        </div>
        <div class="form-group">
            <label>Keterangan</label>
            <input type="text" name="keterangan" placeholder="Contoh: Gaji bulan April, Dana darurat sewa kontrakan">
        </div>
        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn" style="background: <?= $aksi=='tarik' ? 'var(--warning)' : 'var(--success)' ?>">
                <i class="fa-solid fa-<?= $aksi=='tarik'?'arrow-up':'check' ?>"></i> Konfirmasi <?= ucfirst($aksi) ?>
            </button>
            <a href="index.php" class="btn" style="background:#cbd5e1; color:var(--text)">Batal</a>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
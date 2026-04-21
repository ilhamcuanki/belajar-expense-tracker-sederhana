<?php
$pageTitle = "Atur Budget";
require_once __DIR__ . '/../includes/header.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_id = (int)$_POST['kategori_id'];
    $batas = filter_var($_POST['jumlah_batas'], FILTER_VALIDATE_FLOAT);
    $bulan = (int)$_POST['bulan'];
    $tahun = (int)$_POST['tahun'];

    if ($batas > 0 && $bulan && $tahun) {
        $stmt = $pdo->prepare("INSERT INTO budget (kategori_id, jumlah_batas, bulan, tahun) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE jumlah_batas = VALUES(jumlah_batas)");
        $stmt->execute([$kategori_id, $batas, $bulan, $tahun]);
        setFlashMessage('success', 'Budget berhasil diperbarui.');
        header("Location: pantau.php"); exit;
    } else {
        setFlashMessage('danger', 'Input budget tidak valid.');
    }
}

$bulan_list = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
$kategori_pengeluaran = $pdo->query("SELECT id, nama FROM kategori WHERE tipe='pengeluaran' ORDER BY nama")->fetchAll();
?>

<h1><i class="fa-solid fa-sliders"></i> Atur Budget</h1>
<div class="card">
    <form method="POST" class="validate-on-submit">
        <div class="form-group">
            <label>Kategori Pengeluaran</label>
            <select name="kategori_id" required>
                <?php foreach ($kategori_pengeluaran as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
            <div><label>Bulan</label><select name="bulan" required><?php foreach($bulan_list as $i=>$b): ?><option value="<?= $i+1 ?>"><?= $b ?></option><?php endforeach; ?></select></div>
            <div><label>Tahun</label><input type="number" name="tahun" value="<?= date('Y') ?>" required min="2020" max="2030"></div>
        </div>
        <div class="form-group">
            <label>Batas Pengeluaran (Rp)</label>
            <input type="number" name="jumlah_batas" min="1000" step="1000" required>
        </div>
        <button type="submit" class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan Budget</button>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
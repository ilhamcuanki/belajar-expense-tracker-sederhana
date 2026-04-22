<?php
$pageTitle = "Buat Target Tabungan";
require_once __DIR__ . '/../../includes/header.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama_target']);
    $target = filter_var($_POST['target_jumlah'], FILTER_VALIDATE_FLOAT);
    $sumber = sanitize($_POST['sumber_dana']);
    
    $gambar_path = null;
    if (!empty($_FILES['gambar']['name'])) {
        $up = uploadBukti($_FILES['gambar'], 'target');
        if (!$up['success']) { setFlashMessage('danger', $up['msg']); }
        else $gambar_path = $up['filename'];
    }

    if ($nama && $target > 0 && $sumber) {
        $stmt = $pdo->prepare("INSERT INTO target_budget (nama_target, gambar, target_jumlah, sumber_dana) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nama, $gambar_path, $target, $sumber])) {
            setFlashMessage('success', 'Target tabungan berhasil dibuat.');
            header("Location: index.php"); exit;
        }
    } else {
        setFlashMessage('danger', 'Pastikan semua field wajib terisi.');
    }
}
?>
<h1><i class="fa-solid fa-bullseye"></i> Buat Target Tabungan</h1>
<div class="card">
    <form method="POST" enctype="multipart/form-data" class="validate-on-submit">
        <div class="form-group">
            <label><i class="fa-solid fa-tag"></i> Nama Target</label>
            <input type="text" name="nama_target" placeholder="Contoh: Laptop Baru, DP Motor, Liburan" required>
        </div>
        <div class="form-group" style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
            <div>
                <label><i class="fa-solid fa-coins"></i> Target Jumlah (Rp)</label>
                <input type="number" name="target_jumlah" min="100000" step="50000" required>
            </div>
            <div>
                <label><i class="fa-solid fa-building-columns"></i> Sumber Dana</label>
                <input type="text" name="sumber_dana" placeholder="Contoh: Bank BCA, Ewallet GoPay, Tunai" required>
            </div>
        </div>
        <div class="form-group">
            <label><i class="fa-solid fa-image"></i> Gambar Produk (Opsional)</label>
            <input type="file" name="gambar" accept="image/*" class="file-input">
        </div>
        <button type="submit" class="btn"><i class="fa-solid fa-plus"></i> Buat Target</button>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
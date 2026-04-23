<?php
// budget/atur.php
$pageTitle = "Atur Budget";
require_once __DIR__ . '/../auth/check_auth.php';
require_once __DIR__ . '/../includes/header.php';
$pdo = getDB();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori_id = (int)$_POST['kategori_id'];
    $jumlah_batas = filter_var($_POST['jumlah_batas'], FILTER_VALIDATE_FLOAT);
    $bulan = (int)$_POST['bulan'];
    $tahun = (int)$_POST['tahun'];

    // Validasi sederhana
    if ($kategori_id && $jumlah_batas > 0 && $bulan && $tahun) {
        // UPSERT: Insert baru, atau update jika sudah ada untuk periode yang sama
        $stmt = $pdo->prepare("
            INSERT INTO budget (kategori_id, jumlah_batas, bulan, tahun) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE jumlah_batas = VALUES(jumlah_batas)
        ");
        
        if ($stmt->execute([$kategori_id, $jumlah_batas, $bulan, $tahun])) {
            setFlashMessage('success', 'Budget berhasil disimpan.');
            header("Location: pantau.php?bulan=$bulan&tahun=$tahun");
            exit;
        }
    }
    setFlashMessage('danger', 'Gagal menyimpan budget. Periksa input Anda.');
}

// Ambil data untuk form
$bulan_list = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$kategori_pengeluaran = $pdo->query("SELECT id, nama FROM kategori WHERE tipe = 'pengeluaran' ORDER BY nama")->fetchAll();
$bulan_sekarang = (int)date('n');
$tahun_sekarang = (int)date('Y');
?>

<h1><i class="fa-solid fa-sliders"></i> Atur Budget Bulanan</h1>

<div class="card">
    <form method="POST" class="validate-on-submit">
        <div class="form-group">
            <label for="kategori_id"><i class="fa-solid fa-tag"></i> Kategori Pengeluaran</label>
            <select name="kategori_id" id="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategori_pengeluaran as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="bulan"><i class="fa-regular fa-calendar"></i> Bulan</label>
                <select name="bulan" id="bulan" required>
                    <?php foreach ($bulan_list as $i => $nama): ?>
                        <option value="<?= $i + 1 ?>" <?= ($i + 1 == $bulan_sekarang) ? 'selected' : '' ?>>
                            <?= $nama ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tahun"><i class="fa-regular fa-calendar-days"></i> Tahun</label>
                <input type="number" name="tahun" id="tahun" value="<?= $tahun_sekarang ?>" min="2020" max="2030" required>
            </div>
        </div>

        <div class="form-group">
            <label for="jumlah_batas"><i class="fa-solid fa-coins"></i> Batas Pengeluaran (Rp)</label>
            <input type="number" name="jumlah_batas" id="jumlah_batas" min="1000" step="1000" placeholder="Contoh: 500000" required>
        </div>

        <button type="submit" class="btn">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Budget
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
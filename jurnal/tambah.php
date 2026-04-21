<?php
$pageTitle = "Tambah Transaksi";
require_once __DIR__ . '/../includes/header.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = sanitize($_POST['tanggal']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $jumlah = filter_var($_POST['jumlah'], FILTER_VALIDATE_FLOAT);
    $tipe = sanitize($_POST['tipe']);
    $kategori_id = (int)$_POST['kategori_id'];

    if (!$tanggal || !$deskripsi || !$jumlah || $jumlah <= 0 || !$kategori_id) {
        setFlashMessage('danger', 'Data transaksi tidak valid.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO transaksi (tanggal, deskripsi, jumlah, tipe, kategori_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$tanggal, $deskripsi, $jumlah, $tipe, $kategori_id])) {
            setFlashMessage('success', 'Transaksi berhasil dicatat.');
            header("Location: daftar.php"); exit;
        }
    }
}

$kategori = $pdo->query("SELECT id, nama, tipe FROM kategori ORDER BY tipe, nama")->fetchAll();
?>

<h1><i class="fa-solid fa-plus"></i> Tambah Transaksi</h1>
<div class="card">
    <form method="POST" class="validate-on-submit">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>">
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <input type="text" name="deskripsi" placeholder="Contoh: Beli makan siang" required>
        </div>
        <div class="form-group">
            <label>Jumlah (Rp)</label>
            <input type="number" name="jumlah" min="1" step="0.01" placeholder="0" required>
        </div>
        <div class="form-group">
            <label>Tipe</label>
            <select name="tipe" id="tipe" required>
                <option value="">Pilih Tipe</option>
                <option value="pemasukan">Pemasukan</option>
                <option value="pengeluaran">Pengeluaran</option>
            </select>
        </div>
        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" required>
                <option value="">Pilih Kategori</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= $k['id'] ?>" data-tipe="<?= $k['tipe'] ?>"><?= $k['nama'] ?> (<?= $k['tipe'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
    </form>
</div>

<script>
    // Sinkronisasi dropdown tipe & kategori
    document.getElementById('tipe').addEventListener('change', function() {
        const opts = document.querySelectorAll('select[name="kategori_id"] option');
        opts.forEach(opt => {
            if (opt.value === '') return;
            opt.style.display = opt.dataset.tipe === this.value ? '' : 'none';
        });
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
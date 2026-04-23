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

    // Handle upload gambar
    $bukti_path = null;
    if (!empty($_FILES['bukti']['name'])) {
        $upload = uploadBukti($_FILES['bukti'], 'jurnal');
        if (!$upload['success']) {
            setFlashMessage('danger', $upload['msg']);
            header("Location: tambah.php");
            exit;
        }
        $bukti_path = $upload['filename'];
    }

    if (!$tanggal || !$deskripsi || !$jumlah || $jumlah <= 0 || !$kategori_id) {
        setFlashMessage('danger', 'Data transaksi tidak valid.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO transaksi (tanggal, deskripsi, jumlah, tipe, kategori_id, bukti_foto) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$tanggal, $deskripsi, $jumlah, $tipe, $kategori_id, $bukti_path])) {
            setFlashMessage('success', 'Transaksi berhasil dicatat.');
            header("Location: daftar.php");
            exit;
        }
    }
}

// Query kategori dengan "Lainnya" di bawah
$kategori = $pdo->query("
    SELECT id, nama, tipe FROM kategori 
    ORDER BY FIELD(nama, 'Lainnya') ASC, tipe ASC, nama ASC
")->fetchAll();
?>

<h1><i class="fa-solid fa-plus"></i> Tambah Transaksi</h1>
<div class="card">
    <form method="POST" enctype="multipart/form-data" class="validate-on-submit">
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
        <div class="form-group">
            <label><i class="fa-solid fa-camera"></i> Bukti Transaksi (Opsional)</label>
            <input type="file" name="bukti" accept="image/*" class="file-input">
            <small style="color:var(--muted)">Format: JPG, PNG, WEBP. Maksimal 2MB.</small>
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
        document.querySelector('.file-input').addEventListener('change', function(e) {
        if(this.files && this.files[0]) {
            console.log('File siap diupload:', this.files[0].name);
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
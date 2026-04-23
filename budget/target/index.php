<?php
$pageTitle = "Target Budgeting";
require_once __DIR__ . '/../../auth/check_auth.php';
require_once __DIR__ . '/../../includes/header.php';
$pdo = getDB();

$targets = $pdo->query("SELECT * FROM target_budget WHERE status='aktif' ORDER BY created_at DESC")->fetchAll();
?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h1><i class="fa-solid fa-piggy-bank"></i> Target Tabungan</h1>
    <a href="tambah.php" class="btn btn-sm"><i class="fa-solid fa-plus"></i> Tambah Target</a>
</div>

<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap:1.5rem;">
<?php if (empty($targets)): ?>
    <div class="card" style="grid-column: 1/-1; text-align:center; color:var(--muted);">
        <i class="fa-regular fa-folder-open" style="font-size:2rem; margin-bottom:0.5rem;"></i>
        <p>Belum ada target tabungan. Mulai buat target Anda sekarang.</p>
    </div>
<?php else: ?>
    <?php foreach ($targets as $t): 
        $persen = min(100, ($t['terkumpul_jumlah'] / $t['target_jumlah']) * 100);
        $kelas = $persen >= 100 ? 'over' : ($persen >= 75 ? 'warn' : '');
        $sisa = max(0, $t['target_jumlah'] - $t['terkumpul_jumlah']);
    ?>
    <div class="card target-card">
        <div class="target-img">
            <?php if ($t['gambar']): ?>
                <img src="<?= APP_URL ?>/uploads/<?= htmlspecialchars($t['gambar']) ?>" alt="<?= htmlspecialchars($t['nama_target']) ?>">
            <?php else: ?>
                <div class="placeholder-img"><i class="fa-solid fa-box-open"></i></div>
            <?php endif; ?>
        </div>
        <h3 style="margin:0.75rem 0 0.25rem; font-size:1.1rem;"><?= htmlspecialchars($t['nama_target']) ?></h3>
        <p style="color:var(--muted); font-size:0.85rem; margin-bottom:0.5rem;"><i class="fa-solid fa-vault"></i> <?= htmlspecialchars($t['sumber_dana']) ?></p>
        
        <div class="progress-bar"><div class="progress-fill <?= $kelas ?>" style="width: <?= $persen ?>%"></div></div>
        <div style="display:flex; justify-content:space-between; font-size:0.85rem; margin-top:0.25rem;">
            <span><?= formatRupiah($t['terkumpul_jumlah']) ?></span>
            <span style="color:var(--muted)"><?= number_format($persen,1) ?>%</span>
        </div>
        <p style="font-size:0.8rem; color:var(--primary); margin-top:0.25rem;">Kurang: <?= formatRupiah($sisa) ?></p>
        
        <div class="target-actions">
            <a href="kelola.php?id=<?= $t['id'] ?>&aksi=setor" class="btn btn-sm" style="flex:1; background:var(--success);"><i class="fa-solid fa-arrow-down"></i> Setor</a>
            <a href="kelola.php?id=<?= $t['id'] ?>&aksi=tarik" class="btn btn-sm" style="flex:1; background:var(--warning); color:#000;"><i class="fa-solid fa-arrow-up"></i> Tarik</a>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
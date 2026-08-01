<?php
/* ─────────────────────────────────────────────────────────────
   admin/statistik.php — Edit Statistik Beranda Desa
   ───────────────────────────────────────────────────────────── */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-auth.php';
cekAdmin();

$admin   = getAdmin();
$success = '';
$errors  = [];

/* ── HANDLE SIMPAN ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'penduduk'     => ['label' => 'Penduduk',            'nilai' => trim($_POST['penduduk_nilai'] ?? ''),  'target' => (int)($_POST['penduduk_target'] ?? 0)],
        'kk'           => ['label' => 'Kepala Keluarga',     'nilai' => trim($_POST['kk_nilai'] ?? ''),        'target' => (int)($_POST['kk_target'] ?? 0)],
        'layanan_bulan'=> ['label' => 'Keseluruhan Layanan', 'nilai' => trim($_POST['layanan_nilai'] ?? ''),   'target' => (int)($_POST['layanan_target'] ?? 0)],
    ];

    foreach ($fields as $kunci => $data) {
        if (empty($data['nilai'])) {
            $errors[] = "Nilai untuk '{$data['label']}' tidak boleh kosong.";
        }
    }

    if (empty($errors)) {
        try {
            $db = getDB();
            $stmt = $db->prepare(
                "INSERT INTO statistik_desa (kunci, label, nilai, target_angka)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE label = VALUES(label), nilai = VALUES(nilai), target_angka = VALUES(target_angka)"
            );
            foreach ($fields as $kunci => $data) {
                $stmt->execute([$kunci, $data['label'], $data['nilai'], $data['target']]);
            }
            $success = 'Statistik beranda berhasil diperbarui!';
        } catch (Exception $e) {
            $errors[] = 'Gagal menyimpan ke database: ' . $e->getMessage();
            error_log('Statistik update error: ' . $e->getMessage());
        }
    }
}

/* ── LOAD DATA STATISTIK ── */
$stats = [];
try {
    $db   = getDB();
    $rows = $db->query("SELECT kunci, label, nilai, target_angka FROM statistik_desa ORDER BY id ASC")->fetchAll();
    foreach ($rows as $r) $stats[$r['kunci']] = $r;
} catch (Exception $e) {}

// Fallback
$defaults = [
    'penduduk'     => ['nilai' => '3.847', 'target_angka' => 3847],
    'kk'           => ['nilai' => '1.124', 'target_angka' => 1124],
    'layanan_bulan'=> ['nilai' => '247',   'target_angka' => 247],
];
foreach ($defaults as $k => $d) {
    if (!isset($stats[$k])) $stats[$k] = $d;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Statistik — Admin Desa Sungai Bakau Kecil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0d0d0d;
            color: #e5e5e5;
            min-height: 100vh;
            display: flex;
        }

        /* ── SIDEBAR (shared) ── */
        .sidebar { width: 240px; min-height: 100vh; background: #111111; border-right: 1px solid rgba(255,255,255,0.08); display: flex; flex-direction: column; flex-shrink: 0; position: fixed; top: 0; left: 0; bottom: 0; }
        .sidebar-brand { padding: 28px 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-brand-label { font-size: 9px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: rgba(255,255,255,0.30); margin-bottom: 4px; }
        .sidebar-brand-name { font-family: 'Playfair Display', serif; font-size: 1rem; color: #ffffff; line-height: 1.3; }
        .sidebar-nav { flex: 1; padding: 16px 0; }
        .nav-section-label { font-size: 9px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: rgba(255,255,255,0.25); padding: 12px 24px 6px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 24px; font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.55); text-decoration: none; transition: color 0.2s, background 0.2s; }
        .nav-item:hover, .nav-item.active { color: #ffffff; background: rgba(255,255,255,0.06); }
        .nav-item.active { border-left: 2px solid #ffffff; padding-left: 22px; }
        .nav-item svg { opacity: 0.70; flex-shrink: 0; }
        .nav-item.active svg { opacity: 1; }
        .sidebar-footer { padding: 16px 24px; border-top: 1px solid rgba(255,255,255,0.08); }
        .admin-info { font-size: 11.5px; color: rgba(255,255,255,0.40); margin-bottom: 10px; }
        .admin-info strong { display: block; color: rgba(255,255,255,0.75); font-size: 12.5px; }
        .btn-logout { display: block; text-align: center; padding: 8px 14px; background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.30); color: #fca5a5; font-size: 11.5px; font-weight: 600; text-decoration: none; border-radius: 3px; transition: background 0.2s; }
        .btn-logout:hover { background: rgba(220,38,38,0.25); }

        /* ── MAIN ── */
        .main { margin-left: 240px; flex: 1; padding: 36px 40px; }
        .page-header { margin-bottom: 32px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 1.6rem; color: #ffffff; margin-bottom: 4px; }
        .page-sub { font-size: 12.5px; color: rgba(255,255,255,0.35); }

        .alert { padding: 12px 16px; border-radius: 3px; font-size: 13px; margin-bottom: 24px; }
        .alert.success { background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.30); color: #6ee7b7; }
        .alert.error   { background: rgba(220,38,38,0.12); border: 1px solid rgba(220,38,38,0.30); color: #fca5a5; }
        .alert ul { padding-left: 16px; }
        .alert li { margin-top: 4px; }

        /* ── PREVIEW STATISTIK ── */
        .stats-preview {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            border: 1px solid rgba(255,255,255,0.10);
            background: #111111;
            border-radius: 4px;
            margin-bottom: 36px;
            overflow: hidden;
        }

        .stat-preview-item {
            text-align: center;
            padding: 24px 20px;
            border-right: 1px solid rgba(255,255,255,0.08);
        }

        .stat-preview-item:last-child { border-right: none; }

        .stat-preview-num {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #ffffff;
            line-height: 1;
            display: block;
            margin-bottom: 6px;
            transition: all 0.3s;
        }

        .stat-preview-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
        }

        /* ── EDIT FORM CARDS ── */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card-edit {
            background: #111111;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 4px;
            padding: 22px;
        }

        .stat-card-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            margin-bottom: 16px;
        }

        .form-group { margin-bottom: 14px; }

        label.field-label {
            display: block;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.40);
            margin-bottom: 7px;
        }

        .field-hint {
            font-size: 10.5px;
            color: rgba(255,255,255,0.22);
            margin-top: 4px;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 3px;
            padding: 10px 12px;
            font-size: 14px;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus, input[type="number"]:focus {
            border-color: rgba(255,255,255,0.40);
            background: rgba(255,255,255,0.07);
        }

        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            color: #111111;
            border: none;
            border-radius: 3px;
            padding: 12px 28px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-save:hover { background: #e5e5e5; }
        .btn-save:active { transform: scale(0.98); }

        .hint-global {
            font-size: 12px;
            color: rgba(255,255,255,0.25);
            margin-top: 12px;
            line-height: 1.5;
        }

        @media (max-width: 900px) {
            .sidebar { width: 200px; }
            .main { margin-left: 200px; padding: 24px; }
            .stats-preview, .stat-cards { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <p class="sidebar-brand-label">Admin Panel</p>
        <p class="sidebar-brand-name">Desa Sungai<br>Bakau Kecil</p>
    </div>
    <nav class="sidebar-nav">
        <p class="nav-section-label">Menu Utama</p>
        <a class="nav-item" href="dashboard.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a class="nav-item" href="banner.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m9 15 3-3 3 3"/><circle cx="9" cy="9" r="1.5" fill="currentColor" stroke="none"/></svg>
            Kelola Banner
        </a>
        <a class="nav-item active" href="statistik.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 4-4"/></svg>
            Edit Statistik
        </a>
        <p class="nav-section-label" style="margin-top:8px;">Beranda</p>
        <a class="nav-item" href="../index.php" target="_blank">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            Lihat Beranda
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="admin-info">
            <strong><?= htmlspecialchars($admin['nama']) ?></strong>
            <?= htmlspecialchars(ucfirst($admin['role'])) ?>
        </div>
        <a href="logout.php" class="btn-logout">Keluar</a>
    </div>
</aside>

<!-- MAIN -->
<main class="main">
    <div class="page-header">
        <h1 class="page-title">Edit Statistik Beranda</h1>
        <p class="page-sub">Perbarui angka yang tampil di bagian statistik halaman beranda</p>
    </div>

    <?php if ($success): ?>
    <div class="alert success">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
    <div class="alert error">
        <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endif; ?>

    <!-- Preview Real-time -->
    <h2 style="font-size:11px; font-weight:700; letter-spacing:0.10em; text-transform:uppercase; color:rgba(255,255,255,0.30); margin-bottom:12px;">Preview Tampilan Beranda</h2>
    <div class="stats-preview">
        <div class="stat-preview-item">
            <span class="stat-preview-num" id="pv-penduduk"><?= htmlspecialchars($stats['penduduk']['nilai'] ?? '3.847') ?></span>
            <span class="stat-preview-label">Penduduk</span>
        </div>
        <div class="stat-preview-item">
            <span class="stat-preview-num" id="pv-kk"><?= htmlspecialchars($stats['kk']['nilai'] ?? '1.124') ?></span>
            <span class="stat-preview-label">Kepala Keluarga</span>
        </div>
        <div class="stat-preview-item">
            <span class="stat-preview-num" id="pv-layanan"><?= htmlspecialchars($stats['layanan_bulan']['nilai'] ?? '247') ?></span>
            <span class="stat-preview-label">Keseluruhan Layanan</span>
        </div>
    </div>

    <!-- Form Edit -->
    <h2 style="font-size:11px; font-weight:700; letter-spacing:0.10em; text-transform:uppercase; color:rgba(255,255,255,0.30); margin-bottom:12px;">Edit Nilai</h2>
    <form method="POST">
        <div class="stat-cards">

            <!-- Penduduk -->
            <div class="stat-card-edit">
                <p class="stat-card-title">Penduduk</p>
                <div class="form-group">
                    <label class="field-label" for="penduduk_nilai">Tampilan Angka</label>
                    <input type="text" id="penduduk_nilai" name="penduduk_nilai"
                           value="<?= htmlspecialchars($stats['penduduk']['nilai'] ?? '3.847') ?>"
                           placeholder="contoh: 3.847"
                           oninput="document.getElementById('pv-penduduk').textContent=this.value" required>
                    <p class="field-hint">Angka yang tampil di beranda (boleh pakai titik/koma)</p>
                </div>
                <div class="form-group">
                    <label class="field-label" for="penduduk_target">Angka Target (animasi)</label>
                    <input type="number" id="penduduk_target" name="penduduk_target"
                           value="<?= (int)($stats['penduduk']['target_angka'] ?? 3847) ?>"
                           placeholder="3847" min="0">
                    <p class="field-hint">Angka asli untuk animasi count-up</p>
                </div>
            </div>

            <!-- Kepala Keluarga -->
            <div class="stat-card-edit">
                <p class="stat-card-title">Kepala Keluarga</p>
                <div class="form-group">
                    <label class="field-label" for="kk_nilai">Tampilan Angka</label>
                    <input type="text" id="kk_nilai" name="kk_nilai"
                           value="<?= htmlspecialchars($stats['kk']['nilai'] ?? '1.124') ?>"
                           placeholder="contoh: 1.124"
                           oninput="document.getElementById('pv-kk').textContent=this.value" required>
                    <p class="field-hint">Angka yang tampil di beranda</p>
                </div>
                <div class="form-group">
                    <label class="field-label" for="kk_target">Angka Target (animasi)</label>
                    <input type="number" id="kk_target" name="kk_target"
                           value="<?= (int)($stats['kk']['target_angka'] ?? 1124) ?>"
                           placeholder="1124" min="0">
                    <p class="field-hint">Angka asli untuk animasi count-up</p>
                </div>
            </div>

            <!-- Keseluruhan Layanan -->
            <div class="stat-card-edit">
                <p class="stat-card-title">Keseluruhan Layanan</p>
                <div class="form-group">
                    <label class="field-label" for="layanan_nilai">Tampilan Angka</label>
                    <input type="text" id="layanan_nilai" name="layanan_nilai"
                           value="<?= htmlspecialchars($stats['layanan_bulan']['nilai'] ?? '247') ?>"
                           placeholder="contoh: 247"
                           oninput="document.getElementById('pv-layanan').textContent=this.value" required>
                    <p class="field-hint">Angka yang tampil di beranda</p>
                </div>
                <div class="form-group">
                    <label class="field-label" for="layanan_target">Angka Target (animasi)</label>
                    <input type="number" id="layanan_target" name="layanan_target"
                           value="<?= (int)($stats['layanan_bulan']['target_angka'] ?? 247) ?>"
                           placeholder="247" min="0">
                    <p class="field-hint">Angka asli untuk animasi count-up</p>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-save">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan Statistik
        </button>
        <p class="hint-global">Perubahan akan langsung tampil di halaman beranda setelah disimpan.</p>
    </form>
</main>

</body>
</html>

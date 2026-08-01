<?php
/* ─────────────────────────────────────────────────────────────
   admin/banner.php — Kelola 3 Gambar Banner Slider Beranda
   ───────────────────────────────────────────────────────────── */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-auth.php';
cekAdmin();

$admin     = getAdmin();
$uploadDir = __DIR__ . '/../assets/images/';
$errors    = [];
$success   = '';

/* ── HANDLE UPLOAD ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();

    foreach ([1, 2, 3] as $pos) {
        $key = "banner_{$pos}";
        if (!isset($_FILES[$key]) || $_FILES[$key]['error'] === UPLOAD_ERR_NO_FILE) continue;

        $file   = $_FILES[$key];
        $ext    = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Banner {$pos}: Gagal upload (kode {$file['error']}).";
            continue;
        }
        if (!in_array($ext, $allowed)) {
            $errors[] = "Banner {$pos}: Format tidak didukung. Gunakan JPG, PNG, atau WEBP.";
            continue;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            $errors[] = "Banner {$pos}: Ukuran file maksimal 5 MB.";
            continue;
        }

        // Validasi mime type
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, ['image/jpeg','image/png','image/webp'])) {
            $errors[] = "Banner {$pos}: File bukan gambar yang valid.";
            continue;
        }

        // Buat nama file unik
        $newName = 'slide' . $pos . '_' . time() . '.' . $ext;
        $dest    = $uploadDir . $newName;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $errors[] = "Banner {$pos}: Gagal menyimpan file.";
            continue;
        }

        // Hapus file lama jika bukan default
        try {
            $old = $db->prepare("SELECT filename FROM banner_slides WHERE posisi = ?");
            $old->execute([$pos]);
            $oldFile = $old->fetchColumn();
            if ($oldFile && !in_array($oldFile, ['bg1.jpg','bg2.png','bg3.png'])) {
                $oldPath = $uploadDir . $oldFile;
                if (file_exists($oldPath)) @unlink($oldPath);
            }
        } catch (Exception $e) {}

        // Update DB
        $stmt = $db->prepare("INSERT INTO banner_slides (posisi, filename) VALUES (?, ?)
                               ON DUPLICATE KEY UPDATE filename = VALUES(filename), updated_at = NOW()");
        $stmt->execute([$pos, $newName]);
    }

    if (empty($errors)) {
        $success = 'Banner berhasil diperbarui!';
    }
}

/* ── LOAD BANNER SAAT INI ── */
$banners = [];
try {
    $db = getDB();
    $rows = $db->query("SELECT posisi, filename FROM banner_slides ORDER BY posisi ASC")->fetchAll();
    foreach ($rows as $r) $banners[$r['posisi']] = $r['filename'];
} catch (Exception $e) {}

// Fallback
$defaults = [1 => 'bg3.png', 2 => 'bg1.jpg', 3 => 'bg2.png'];
for ($i = 1; $i <= 3; $i++) {
    if (!isset($banners[$i])) $banners[$i] = $defaults[$i];
}

$imgBase = '../assets/images/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Banner — Admin Desa Sungai Bakau Kecil</title>
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
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: #111111;
            border-right: 1px solid rgba(255,255,255,0.08);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
        }
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

        /* ── BANNER CARDS ── */
        .banner-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 32px;
        }

        .banner-card {
            background: #111111;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 4px;
            overflow: hidden;
        }

        .banner-preview {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
            background: #1a1a1a;
        }

        .banner-body {
            padding: 16px 18px;
        }

        .banner-label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            margin-bottom: 6px;
        }

        .banner-filename {
            font-size: 11.5px;
            color: rgba(255,255,255,0.50);
            margin-bottom: 14px;
            word-break: break-all;
        }

        /* File input styling */
        .file-input-wrap {
            position: relative;
        }

        .file-input-wrap input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .file-input-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px dashed rgba(255,255,255,0.25);
            border-radius: 3px;
            font-size: 12.5px;
            color: rgba(255,255,255,0.60);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }

        .file-input-wrap:hover .file-input-btn {
            border-color: rgba(255,255,255,0.50);
            background: rgba(255,255,255,0.09);
            color: #ffffff;
        }

        .file-selected-name {
            font-size: 11px;
            color: #6ee7b7;
            margin-top: 6px;
            display: none;
        }

        /* Submit button */
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

        .hint {
            font-size: 12px;
            color: rgba(255,255,255,0.25);
            margin-top: 12px;
        }

        @media (max-width: 900px) {
            .sidebar { width: 200px; }
            .main { margin-left: 200px; padding: 24px; }
            .banner-grid { grid-template-columns: 1fr; }
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
        <a class="nav-item active" href="banner.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m9 15 3-3 3 3"/><circle cx="9" cy="9" r="1.5" fill="currentColor" stroke="none"/></svg>
            Kelola Banner
        </a>
        <a class="nav-item" href="statistik.php">
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
        <h1 class="page-title">Kelola Banner Slider</h1>
        <p class="page-sub">Upload gambar baru untuk mengganti 3 slide banner di halaman beranda</p>
    </div>

    <?php if ($success): ?>
    <div class="alert success">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
    <div class="alert error">
        <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="banner-grid">
            <?php for ($pos = 1; $pos <= 3; $pos++): ?>
            <div class="banner-card">
                <img class="banner-preview"
                     src="<?= $imgBase . htmlspecialchars($banners[$pos]) ?>?t=<?= time() ?>"
                     alt="Banner Slide <?= $pos ?>"
                     id="preview-<?= $pos ?>">
                <div class="banner-body">
                    <p class="banner-label">Banner Slide <?= $pos ?></p>
                    <p class="banner-filename"><?= htmlspecialchars($banners[$pos]) ?></p>
                    <div class="file-input-wrap">
                        <div class="file-input-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Pilih Gambar Baru
                        </div>
                        <input type="file" name="banner_<?= $pos ?>" id="file-<?= $pos ?>"
                               accept="image/jpeg,image/png,image/webp"
                               onchange="previewImage(this, <?= $pos ?>)">
                    </div>
                    <p class="file-selected-name" id="fname-<?= $pos ?>"></p>
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <p class="hint">Format yang didukung: JPG, PNG, WEBP — Ukuran maksimal 5 MB per gambar.<br>Kosongkan jika tidak ingin mengubah banner tersebut.</p>
        <br>
        <button type="submit" class="btn-save">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan Perubahan Banner
        </button>
    </form>
</main>

<script>
function previewImage(input, pos) {
    const fname = document.getElementById('fname-' + pos);
    const preview = document.getElementById('preview-' + pos);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
        fname.textContent = '✓ ' + input.files[0].name;
        fname.style.display = 'block';
    }
}
</script>
</body>
</html>

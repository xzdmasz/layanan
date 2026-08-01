<?php
/* ─────────────────────────────────────────────────────────────
   admin/dashboard.php — Dashboard Admin Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-auth.php';
cekAdmin();

$admin = getAdmin();

// Flash message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Ambil data ringkasan
try {
    $db = getDB();
    $cntKesehatan = $db->query("SELECT COUNT(*) FROM pengaduan_kesehatan")->fetchColumn();
    $cntHukum     = $db->query("SELECT COUNT(*) FROM pengaduan_hukum")->fetchColumn();
    $cntUser      = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $cntMasuk     = $db->query("SELECT COUNT(*) FROM pengaduan_kesehatan WHERE status='masuk'")->fetchColumn()
                  + $db->query("SELECT COUNT(*) FROM pengaduan_hukum WHERE status='masuk'")->fetchColumn();
} catch (Exception $e) {
    $cntKesehatan = $cntHukum = $cntUser = $cntMasuk = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Desa Sungai Bakau Kecil</title>
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

        /* ── SIDEBAR ── */
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

        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-brand-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.30);
            margin-bottom: 4px;
        }

        .sidebar-brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: #ffffff;
            line-height: 1.3;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
        }

        .nav-section-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
            padding: 12px 24px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            transition: color 0.2s, background 0.2s;
            cursor: pointer;
        }

        .nav-item:hover, .nav-item.active {
            color: #ffffff;
            background: rgba(255,255,255,0.06);
        }

        .nav-item.active {
            border-left: 2px solid #ffffff;
            padding-left: 22px;
        }

        .nav-item svg { opacity: 0.70; flex-shrink: 0; }
        .nav-item.active svg { opacity: 1; }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .admin-info {
            font-size: 11.5px;
            color: rgba(255,255,255,0.40);
            margin-bottom: 10px;
        }

        .admin-info strong {
            display: block;
            color: rgba(255,255,255,0.75);
            font-size: 12.5px;
        }

        .btn-logout {
            display: block;
            text-align: center;
            padding: 8px 14px;
            background: rgba(220,38,38,0.15);
            border: 1px solid rgba(220,38,38,0.30);
            color: #fca5a5;
            font-size: 11.5px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 3px;
            transition: background 0.2s;
        }

        .btn-logout:hover { background: rgba(220,38,38,0.25); }

        /* ── MAIN CONTENT ── */
        .main {
            margin-left: 240px;
            flex: 1;
            min-height: 100vh;
            padding: 36px 40px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .page-sub {
            font-size: 12.5px;
            color: rgba(255,255,255,0.35);
        }

        /* Flash message */
        .flash {
            padding: 12px 16px;
            border-radius: 3px;
            font-size: 13px;
            margin-bottom: 24px;
        }

        .flash.success {
            background: rgba(16,185,129,0.12);
            border: 1px solid rgba(16,185,129,0.30);
            color: #6ee7b7;
        }

        /* ── STAT CARDS ── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #111111;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 4px;
            padding: 20px 22px;
        }

        .stat-card-label {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            margin-bottom: 8px;
        }

        .stat-card-num {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #ffffff;
            line-height: 1;
        }

        .stat-card-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.25);
            margin-top: 6px;
        }

        /* ── QUICK ACTION CARDS ── */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .action-card {
            background: #111111;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 4px;
            padding: 28px;
            text-decoration: none;
            transition: border-color 0.2s, background 0.2s, transform 0.2s;
            display: block;
        }

        .action-card:hover {
            border-color: rgba(255,255,255,0.25);
            background: #161616;
            transform: translateY(-2px);
        }

        .action-icon {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.08);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .action-icon svg { color: #ffffff; }

        .action-title {
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .action-desc {
            font-size: 12.5px;
            color: rgba(255,255,255,0.40);
            line-height: 1.5;
        }

        .action-arrow {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 600;
            color: rgba(255,255,255,0.50);
            margin-top: 14px;
            letter-spacing: 0.04em;
        }

        @media (max-width: 900px) {
            .sidebar { width: 200px; }
            .main { margin-left: 200px; padding: 24px; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
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
        <a class="nav-item active" href="dashboard.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a class="nav-item" href="banner.php">
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

<!-- MAIN CONTENT -->
<main class="main">
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-sub">Selamat datang, <?= htmlspecialchars($admin['nama']) ?> 👋</p>
    </div>

    <?php if ($flash): ?>
    <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="stat-grid">
        <div class="stat-card">
            <p class="stat-card-label">Pengaduan Masuk</p>
            <p class="stat-card-num"><?= $cntMasuk ?></p>
            <p class="stat-card-sub">Belum diproses</p>
        </div>
        <div class="stat-card">
            <p class="stat-card-label">Pengaduan Kesehatan</p>
            <p class="stat-card-num"><?= $cntKesehatan ?></p>
            <p class="stat-card-sub">Total keseluruhan</p>
        </div>
        <div class="stat-card">
            <p class="stat-card-label">Pengaduan Hukum</p>
            <p class="stat-card-num"><?= $cntHukum ?></p>
            <p class="stat-card-sub">Total keseluruhan</p>
        </div>
        <div class="stat-card">
            <p class="stat-card-label">Warga Terdaftar</p>
            <p class="stat-card-num"><?= $cntUser ?></p>
            <p class="stat-card-sub">Pengguna aktif</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <h2 style="font-size:13px; font-weight:700; letter-spacing:0.10em; text-transform:uppercase; color:rgba(255,255,255,0.35); margin-bottom:16px;">Kelola Beranda</h2>
    <div class="action-grid">
        <a class="action-card" href="banner.php">
            <div class="action-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m9 15 3-3 3 3"/><circle cx="9" cy="9" r="1.5" fill="currentColor" stroke="none"/></svg>
            </div>
            <p class="action-title">Kelola Banner Slider</p>
            <p class="action-desc">Ganti 3 gambar banner yang tampil di halaman beranda. Mendukung format JPG dan PNG, ukuran maksimal 5 MB.</p>
            <span class="action-arrow">Buka &rarr;</span>
        </a>

        <a class="action-card" href="statistik.php">
            <div class="action-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 4-4"/></svg>
            </div>
            <p class="action-title">Edit Statistik Beranda</p>
            <p class="action-desc">Perbarui angka Penduduk, Kepala Keluarga, dan Keseluruhan Layanan yang tampil di bagian statistik beranda.</p>
            <span class="action-arrow">Buka &rarr;</span>
        </a>
    </div>
</main>

</body>
</html>

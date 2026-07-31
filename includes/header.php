<?php
/* Header — Desa Sungai Bakau Kecil */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Pusat Layanan Desa Sungai Bakau Kecil — Pengaduan penyakit dan layanan hukum untuk warga desa.">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?>Desa Sungai Bakau Kecil</title>

    <!-- Tailwind CSS (utility helpers) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: { extend: {} }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ===== NAVBAR ===== -->
<header>
<nav id="navbar" role="navigation" aria-label="Navigasi Utama">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; height:68px;">

        <!-- Logo / Brand -->
        <a href="index.php" aria-label="Beranda Desa Sungai Bakau Kecil"
           style="display:flex; align-items:center; gap:12px; text-decoration:none;">
            <!-- Logo Image (Kabupaten Mempawah) -->
            <img src="assets/images/logo.png" alt="Logo Kabupaten Mempawah" style="height:40px; width:auto; object-fit:contain; flex-shrink:0;">
            <!-- Text -->
            <div style="line-height:1.1;">
                <div style="font-size:9.5px; font-weight:600; letter-spacing:0.18em; text-transform:uppercase; color:rgba(255,255,255,0.60);" class="nav-brand-sub">Desa</div>
                <div style="font-size:13.5px; font-weight:700; color:#ffffff; letter-spacing:0.04em;" class="nav-brand-name">Sungai Bakau Kecil</div>
            </div>
        </a>

        <!-- Desktop Menu with spacious gap -->
        <div id="desktop-nav" class="nav-desktop">

            <!-- Beranda -->
            <a href="index.php" class="nav-link">Beranda</a>

            <!-- Layanan dropdown -->
            <div class="dropdown-wrapper" id="layanan-wrap">
                <button class="nav-link" id="layanan-btn" aria-haspopup="true" aria-expanded="false" aria-controls="layanan-menu">
                    Layanan
                    <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>

                <div class="dropdown-panel" id="layanan-menu" role="menu" aria-label="Sub-menu Layanan">
                    <a href="layanan-pengaduan.php" class="dropdown-item" role="menuitem">
                        <span class="item-label">Layanan &amp; Pengaduan Penyakit</span>
                        <span class="item-desc">Laporkan masalah kesehatan di lingkungan desa</span>
                    </a>
                    <a href="layanan-hukum.php" class="dropdown-item" role="menuitem">
                        <span class="item-label">Layanan Hukum</span>
                        <span class="item-desc">Konsultasi dan bantuan hukum untuk warga</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- Profile Dropdown — desktop -->
        <div class="nav-auth" id="nav-auth">
            <?php if (isLoggedIn()): ?>
                <div class="profile-menu-wrap" id="profile-menu-wrap">
                    <button class="profile-icon-btn" id="profile-icon-btn" aria-expanded="false" aria-haspopup="true" aria-label="Menu profil">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span class="profile-btn-name"><?= htmlspecialchars(explode(' ', getUser()['nama_lengkap'])[0]) ?></span>
                        <svg class="profile-btn-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="profile-dropdown" id="profile-dropdown" role="menu" aria-hidden="true">
                        <div class="profile-dropdown-header">
                            <div class="profile-dropdown-avatar">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="profile-dropdown-name"><?= htmlspecialchars(getUser()['nama_lengkap']) ?></div>
                                <div class="profile-dropdown-hp"><?= htmlspecialchars(getUser()['no_hp']) ?></div>
                            </div>
                        </div>
                        <div class="profile-dropdown-divider"></div>
                        <a href="riwayat.php" class="profile-dropdown-item" role="menuitem">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            Riwayat Pengaduan
                        </a>
                        <a href="akun.php" class="profile-dropdown-item" role="menuitem">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            Kelola Akun
                        </a>
                        <div class="profile-dropdown-divider"></div>
                        <a href="logout.php" class="profile-dropdown-item profile-dropdown-logout" role="menuitem">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Keluar
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="nav-btn-login" id="btn-login-nav">Masuk</a>
            <?php endif; ?>
        </div>

        </div>

    </div>
</nav>
</header>

<!-- ===== MOBILE BOTTOM NAVIGATION BAR ===== -->
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$isLayananActive = ($currentPage === 'layanan-pengaduan.php' || $currentPage === 'layanan-hukum.php');
?>
<nav class="mobile-bottom-nav" aria-label="Navigasi Bawah">
    <!-- 1. Beranda -->
    <a href="index.php" class="bottom-nav-item <?= ($currentPage === 'index.php') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        <span>Beranda</span>
    </a>

    <!-- 2. Layanan (Trigger Bottom Sheet) -->
    <button type="button" class="bottom-nav-item <?= $isLayananActive ? 'active' : '' ?>" id="btn-bottom-layanan">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg>
        <span>Layanan</span>
    </button>

    <!-- 3. Riwayat Laporan -->
    <a href="<?= isLoggedIn() ? 'riwayat.php' : 'login.php?redirect=riwayat.php' ?>" class="bottom-nav-item <?= ($currentPage === 'riwayat.php') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
        <span>Riwayat</span>
    </a>
</nav>

<!-- ===== BOTTOM SHEET MODAL LAYANAN (MOBILE) ===== -->
<div class="bottom-sheet-overlay" id="bottom-sheet-layanan" aria-hidden="true">
    <div class="bottom-sheet-content" role="dialog" aria-modal="true" aria-labelledby="sheet-title">
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-header">
            <h3 id="sheet-title">Pilih Layanan Desa</h3>
            <p>Silakan pilih jenis pengaduan atau konsultasi</p>
        </div>
        <div class="bottom-sheet-options">
            <a href="layanan-pengaduan.php" class="sheet-option-card">
                <div class="sheet-icon icon-kesehatan">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                </div>
                <div class="sheet-option-text">
                    <strong>Pengaduan Kesehatan</strong>
                    <span>Laporan kasus penyakit, medis &amp; lingkungan</span>
                </div>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sheet-arrow"><polyline points="9 18 15 12 9 6"/></svg>
            </a>

            <a href="layanan-hukum.php" class="sheet-option-card">
                <div class="sheet-icon icon-hukum">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <div class="sheet-option-text">
                    <strong>Layanan Hukum</strong>
                    <span>Konsultasi sengketa, tanah &amp; bantuan hukum</span>
                </div>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sheet-arrow"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
        <button type="button" class="sheet-close-btn" id="sheet-close-layanan">Tutup</button>
    </div>
</div>

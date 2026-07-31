<?php
/* ─────────────────────────────────────────────────────────────
   includes/auth-template.php — Shared Sliding Auth Template
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

// Expects:
// $activeMode : 'login' | 'register'
// $loginError : string
// $registerErrors : array
// $registerOld : array
// $redirect : string

$redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
$isRegister    = ($activeMode === 'register');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal Warga — Sistem Pusat Layanan Desa Sungai Bakau Kecil">
    <title><?= $isRegister ? 'Daftar Akun' : 'Masuk' ?> — Desa Sungai Bakau Kecil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: #0f172a;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            overflow-x: hidden;
        }

        .auth-container {
            background-color: #ffffff;
            position: relative;
            overflow: hidden;
            width: 100vw;
            min-height: 100vh;
            display: flex;
        }

        /* ── DESKTOP FORM CONTAINERS ── */
        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: transform 0.65s cubic-bezier(0.65, 0, 0.35, 1), opacity 0.65s cubic-bezier(0.65, 0, 0.35, 1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px 40px;
            background: #ffffff;
            overflow-y: auto;
        }

        /* LOGIN FORM (MASUK) — POSISI KANAN SAAT LOGIN MODE */
        .sign-in-container {
            left: 50%;
            width: 50%;
            z-index: 2;
            opacity: 1;
        }

        .auth-container.right-panel-active .sign-in-container {
            transform: translateX(-100%);
            opacity: 0;
            z-index: 1;
            pointer-events: none;
        }

        /* REGISTER FORM (DAFTAR) — POSISI KIRI SAAT REGISTER MODE */
        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
            pointer-events: none;
        }

        .auth-container.right-panel-active .sign-up-container {
            transform: translateX(0);
            opacity: 1;
            z-index: 5;
            pointer-events: auto;
        }

        .auth-form-box {
            width: 100%;
            max-width: 420px;
        }

        .auth-form-title {
            font-size: 2.1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .auth-form-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 28px;
            line-height: 1.5;
        }

        /* Alerts */
        .auth-alert {
            border-radius: 8px;
            padding: 13px 16px;
            font-size: 13.5px;
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            line-height: 1.5;
        }
        .auth-alert svg {
            flex-shrink: 0;
            width: 18px;
            height: 18px;
            margin-top: 1px;
        }
        .auth-alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }
        .auth-alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }
        .auth-alert ul {
            margin: 4px 0 0 16px;
        }
        .auth-alert li {
            margin-bottom: 2px;
        }

        /* Form Inputs */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 18px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }

        .form-group input {
            width: 100%;
            height: 46px;
            padding: 0 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus {
            border-color: #0f172a;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
        }

        .input-hint {
            font-size: 11.5px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Password Eye Toggle */
        .pw-wrap {
            position: relative;
        }

        .pw-wrap input {
            padding-right: 48px;
        }

        .pw-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .pw-eye:hover {
            color: #334155;
        }

        /* Submit Button */
        .auth-btn {
            width: 100%;
            height: 48px;
            background: #0f172a;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14.5px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            margin-top: 6px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }

        .auth-btn:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.22);
        }

        .auth-btn:active {
            transform: translateY(0);
        }

        /* Footer Link inside form */
        .auth-footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 13.5px;
            color: #64748b;
        }

        .auth-footer-text a {
            color: #0f172a;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s;
        }

        .auth-footer-text a:hover {
            text-decoration: underline;
            color: #2563eb;
        }

        /* ── DESKTOP OVERLAY CONTAINER & PANELS ── */
        .overlay-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.65s cubic-bezier(0.65, 0, 0.35, 1);
            z-index: 100;
        }

        .auth-container.right-panel-active .overlay-container {
            transform: translateX(100%);
        }

        .overlay {
            background: #0f172a;
            color: #ffffff;
            position: relative;
            left: 0;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.65s cubic-bezier(0.65, 0, 0.35, 1);
        }

        .auth-container.right-panel-active .overlay {
            transform: translateX(-50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 56px;
            top: 0;
            height: 100%;
            width: 50%;
            transition: transform 0.65s cubic-bezier(0.65, 0, 0.35, 1);
        }

        .overlay-left {
            left: 0;
            transform: translateX(0);
        }

        .auth-container.right-panel-active .overlay-left {
            transform: translateX(-20%);
        }

        .overlay-right {
            left: 50%;
            transform: translateX(20%);
        }

        .auth-container.right-panel-active .overlay-right {
            transform: translateX(0);
        }

        .overlay-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            z-index: 1;
        }

        .bg-login-img {
            background-image: url('assets/images/bg2.png');
        }

        .bg-register-img {
            background-image: url('assets/images/bg3.png');
        }

        .overlay-dark-mask {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(8, 12, 18, 0.78) 0%, rgba(8, 12, 18, 0.94) 100%);
            z-index: 2;
        }

        .overlay-content {
            position: relative;
            z-index: 3;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .auth-hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .auth-back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: color 0.2s, transform 0.2s;
        }

        .auth-back-link:hover {
            color: #ffffff;
            transform: translateX(-3px);
        }

        .auth-brand-badge {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .auth-brand-badge img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        .auth-brand-text {
            line-height: 1.15;
        }

        .auth-brand-sub {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.65);
        }

        .auth-brand-name {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.03em;
        }

        .auth-hero-bottom {
            max-width: 480px;
            padding-bottom: 24px;
        }

        .auth-hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 3vw, 2.65rem);
            font-weight: 700;
            line-height: 1.2;
            color: #ffffff;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }

        .auth-hero-desc {
            font-size: 14.5px;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.78);
            font-weight: 400;
            margin-bottom: 24px;
        }

        .overlay-ghost-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            border: 1.5px solid rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.10);
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            backdrop-filter: blur(8px);
            transition: all 0.25s;
        }

        .overlay-ghost-btn:hover {
            background: #ffffff;
            color: #0f172a;
            border-color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        /* ── RESPONSIVE MOBILE (≤ 900px) — VERTICAL SLIDE & HERO BANNER ── */
        @media (max-width: 900px) {
            .auth-container {
                flex-direction: column;
                min-height: 100vh;
                width: 100vw;
                position: relative;
                background: #ffffff;
            }

            .overlay-container {
                display: none !important;
            }

            /* Mobile Hero Banner Top */
            .mobile-hero-banner {
                display: flex !important;
                position: relative;
                width: 100%;
                min-height: 220px;
                background-size: cover;
                background-position: center;
                padding: 22px 20px 20px;
                flex-direction: column;
                justify-content: space-between;
                color: #ffffff;
                overflow: hidden;
                transition: background-image 0.5s ease-in-out;
            }

            .mobile-hero-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, rgba(8, 12, 18, 0.74) 0%, rgba(8, 12, 18, 0.92) 100%);
                z-index: 1;
            }

            .mobile-hero-top,
            .mobile-hero-bottom {
                position: relative;
                z-index: 2;
            }

            .mobile-hero-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .mobile-hero-title {
                font-family: 'Playfair Display', serif;
                font-size: 1.5rem;
                font-weight: 700;
                color: #ffffff;
                margin-bottom: 6px;
                line-height: 1.25;
            }

            .mobile-hero-desc {
                font-size: 12.5px;
                color: rgba(255, 255, 255, 0.80);
                line-height: 1.5;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* Form Containers on Mobile with Vertical Slide */
            .form-container {
                position: relative;
                width: 100% !important;
                left: 0 !important;
                top: 0 !important;
                transform: none !important;
                padding: 28px 20px 48px;
                min-height: auto;
                flex: 1;
                overflow-y: visible;
            }

            /* Vertical Slide Animations on Mobile */
            .sign-in-container {
                display: flex;
                animation: vSlideInDown 0.5s cubic-bezier(0.65, 0, 0.35, 1) forwards;
            }

            .sign-up-container {
                display: none;
                animation: vSlideInUp 0.5s cubic-bezier(0.65, 0, 0.35, 1) forwards;
            }

            .auth-container.right-panel-active .sign-in-container {
                display: none;
            }

            .auth-container.right-panel-active .sign-up-container {
                display: flex;
                animation: vSlideInUp 0.5s cubic-bezier(0.65, 0, 0.35, 1) forwards;
            }
        }

        @media (min-width: 901px) {
            .mobile-hero-banner {
                display: none !important;
            }
        }

        @keyframes vSlideInDown {
            0% { transform: translateY(-40px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        @keyframes vSlideInUp {
            0% { transform: translateY(40px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="auth-container <?= $isRegister ? 'right-panel-active' : '' ?>" id="auth-container">

    <!-- MOBILE HERO BANNER (Top Banner with Image on Mobile) -->
    <div class="mobile-hero-banner" id="mobile-hero-bg" style="background-image: url('assets/images/<?= $isRegister ? 'bg3.png' : 'bg2.png' ?>');">
        <div class="mobile-hero-overlay"></div>

        <div class="mobile-hero-top">
            <a href="index.php" class="auth-back-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
            <div class="auth-brand-badge">
                <img src="assets/images/logo.png" alt="Logo" style="height:32px;">
                <div class="auth-brand-text">
                    <div class="auth-brand-sub">Desa</div>
                    <div class="auth-brand-name">Sungai Bakau Kecil</div>
                </div>
            </div>
        </div>

        <div class="mobile-hero-bottom">
            <h2 class="mobile-hero-title" id="mobile-hero-title">
                <?= $isRegister ? 'Dapatkan Akses Layanan Digital Warga' : 'Sistem Pusat Layanan &amp; Pengaduan Warga Desa' ?>
            </h2>
            <p class="mobile-hero-desc" id="mobile-hero-desc">
                <?= $isRegister
                    ? 'Daftarkan akun Anda hanya dalam beberapa langkah untuk mulai memanfaatkan layanan publik desa.'
                    : 'Bergabunglah dengan portal digital Desa Sungai Bakau Kecil untuk menyampaikan laporan pengaduan penyakit lingkungan &amp; hukum.' ?>
            </p>
        </div>
    </div>

    <!-- 1. SIGN IN FORM (LOGIN) — FORM MASUK DI SEBELAH KANAN (DESKTOP) -->
    <div class="form-container sign-in-container">
        <div class="auth-form-box">
            <h2 class="auth-form-title">Masuk</h2>
            <p class="auth-form-subtitle">Masukkan kredensial Anda untuk mengakses akun Anda.</p>

            <?php if (!empty($loginError)): ?>
            <div class="auth-alert auth-alert-error" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span><?= htmlspecialchars($loginError) ?></span>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
            <div class="auth-alert auth-alert-success" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Pendaftaran berhasil! Silakan masuk dengan Nomor HP &amp; Password Anda.</span>
            </div>
            <?php endif; ?>

            <form method="POST" action="login.php<?= $redirectParam ?>" novalidate>
                <div class="form-group">
                    <label for="no_hp_login">Nomor HP / WhatsApp *</label>
                    <input type="tel" id="no_hp_login" name="no_hp" placeholder="Contoh: 081234567890"
                           value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required autocomplete="tel" inputmode="tel">
                </div>

                <div class="form-group">
                    <label for="password_login">Password *</label>
                    <div class="pw-wrap">
                        <input type="password" id="password_login" name="password" placeholder="Masukkan password Anda" required autocomplete="current-password">
                        <button type="button" class="pw-eye" onclick="togglePw('password_login', this)" aria-label="Tampilkan password">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-btn">Masuk</button>
            </form>

            <div class="auth-footer-text">
                Belum punya akun? <a href="register.php<?= $redirectParam ?>" class="js-switch-btn" data-target="register">Daftar sekarang</a>
            </div>
        </div>
    </div>

    <!-- 2. SIGN UP FORM (REGISTER) — FORM DAFTAR DI SEBELAH KIRI (DESKTOP) -->
    <div class="form-container sign-up-container">
        <div class="auth-form-box">
            <h2 class="auth-form-title">Daftar</h2>
            <p class="auth-form-subtitle">Isi data diri Anda untuk membuat akun warga baru.</p>

            <?php if (!empty($registerErrors)): ?>
            <div class="auth-alert auth-alert-error" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>
                    <strong>Perbaiki kesalahan berikut:</strong>
                    <ul><?php foreach($registerErrors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" action="register.php<?= $redirectParam ?>" novalidate>
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap (sesuai KTP) *</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap"
                           placeholder="Nama lengkap sesuai KTP"
                           value="<?= htmlspecialchars($registerOld['nama_lengkap'] ?? '') ?>"
                           required autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="no_hp_reg">Nomor HP / WhatsApp *</label>
                    <input type="tel" id="no_hp_reg" name="no_hp"
                           placeholder="Contoh: 081234567890"
                           value="<?= htmlspecialchars($registerOld['no_hp'] ?? '') ?>"
                           required autocomplete="tel">
                    <span class="input-hint">Nomor ini digunakan untuk login &amp; notifikasi pengaduan</span>
                </div>

                <div class="form-group">
                    <label for="password_reg">Password *</label>
                    <div class="pw-wrap">
                        <input type="password" id="password_reg" name="password"
                               placeholder="Minimal 6 karakter" required autocomplete="new-password">
                        <button type="button" class="pw-eye" onclick="togglePw('password_reg', this)" aria-label="Tampilkan password">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password *</label>
                    <div class="pw-wrap">
                        <input type="password" id="password_confirm" name="password_confirm"
                               placeholder="Ulangi password Anda" required autocomplete="new-password">
                        <button type="button" class="pw-eye" onclick="togglePw('password_confirm', this)" aria-label="Tampilkan konfirmasi password">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-btn">Daftar Sekarang</button>
            </form>

            <div class="auth-footer-text">
                Sudah punya akun? <a href="login.php<?= $redirectParam ?>" class="js-switch-btn" data-target="login">Masuk sekarang</a>
            </div>
        </div>
    </div>

    <!-- 3. DESKTOP SLIDING OVERLAY CONTAINER -->
    <div class="overlay-container">
        <div class="overlay">

            <!-- OVERLAY LEFT (BG2.png) — TAMPIL DI KIRI SAAT LOGIN MODE -->
            <div class="overlay-panel overlay-left">
                <div class="overlay-bg bg-login-img"></div>
                <div class="overlay-dark-mask"></div>

                <div class="overlay-content">
                    <div class="auth-hero-top">
                        <a href="index.php" class="auth-back-link">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
                            Kembali
                        </a>
                        <div class="auth-brand-badge">
                            <img src="assets/images/logo.png" alt="Logo">
                            <div class="auth-brand-text">
                                <div class="auth-brand-sub">Desa</div>
                                <div class="auth-brand-name">Sungai Bakau Kecil</div>
                            </div>
                        </div>
                    </div>

                    <div class="auth-hero-bottom">
                        <h1 class="auth-hero-title">Sistem Pusat Layanan &amp; Pengaduan Warga Desa</h1>
                        <p class="auth-hero-desc">
                            Bergabunglah dengan portal digital Desa Sungai Bakau Kecil untuk menyampaikan laporan pengaduan penyakit lingkungan dan konsultasi hukum dengan cepat, aman, dan transparan.
                        </p>
                        <button type="button" class="overlay-ghost-btn js-switch-btn" data-target="register">
                            Daftar Sekarang →
                        </button>
                    </div>
                </div>
            </div>

            <!-- OVERLAY RIGHT (BG3.png) — TAMPIL DI KANAN SAAT REGISTER MODE -->
            <div class="overlay-panel overlay-right">
                <div class="overlay-bg bg-register-img"></div>
                <div class="overlay-dark-mask"></div>

                <div class="overlay-content">
                    <div class="auth-hero-top">
                        <a href="index.php" class="auth-back-link">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
                            Kembali
                        </a>
                        <div class="auth-brand-badge">
                            <img src="assets/images/logo.png" alt="Logo">
                            <div class="auth-brand-text">
                                <div class="auth-brand-sub">Desa</div>
                                <div class="auth-brand-name">Sungai Bakau Kecil</div>
                            </div>
                        </div>
                    </div>

                    <div class="auth-hero-bottom">
                        <h1 class="auth-hero-title">Dapatkan Akses Layanan Digital Warga</h1>
                        <p class="auth-hero-desc">
                            Daftarkan akun Anda hanya dalam beberapa langkah untuk mulai memanfaatkan layanan publik desa, pengaduan kesehatan, dan konsultasi hukum.
                        </p>
                        <button type="button" class="overlay-ghost-btn js-switch-btn" data-target="login">
                            ← Masuk Sekarang
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
// Eye password toggle
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const svg   = btn.querySelector('svg');
    if (!input || !svg) return;
    if (input.type === 'password') {
        input.type = 'text';
        svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
        input.type = 'password';
        svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
}

// Seamless Sliding Panel Toggle & Push State
document.addEventListener('DOMContentLoaded', function () {
    const container      = document.getElementById('auth-container');
    const switchBtns     = document.querySelectorAll('.js-switch-btn');
    const redirect       = <?= json_encode($redirectParam) ?>;
    const mobileHeroImg  = document.getElementById('mobile-hero-bg');
    const mobileTitle    = document.getElementById('mobile-hero-title');
    const mobileDesc     = document.getElementById('mobile-hero-desc');

    switchBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const target = this.getAttribute('data-target');

            if (target === 'register') {
                container.classList.add('right-panel-active');
                history.pushState({ mode: 'register' }, '', 'register.php' + redirect);
                document.title = 'Daftar Akun — Desa Sungai Bakau Kecil';
                if (mobileHeroImg) mobileHeroImg.style.backgroundImage = "url('assets/images/bg3.png')";
                if (mobileTitle)   mobileTitle.textContent = "Dapatkan Akses Layanan Digital Warga";
                if (mobileDesc)    mobileDesc.textContent = "Daftarkan akun Anda hanya dalam beberapa langkah untuk mulai memanfaatkan layanan publik desa.";
            } else {
                container.classList.remove('right-panel-active');
                history.pushState({ mode: 'login' }, '', 'login.php' + redirect);
                document.title = 'Masuk — Desa Sungai Bakau Kecil';
                if (mobileHeroImg) mobileHeroImg.style.backgroundImage = "url('assets/images/bg2.png')";
                if (mobileTitle)   mobileTitle.textContent = "Sistem Pusat Layanan & Pengaduan Warga Desa";
                if (mobileDesc)    mobileDesc.textContent = "Bergabunglah dengan portal digital Desa Sungai Bakau Kecil untuk menyampaikan laporan pengaduan penyakit lingkungan & hukum.";
            }
        });
    });

    // Handle Browser Back/Forward buttons
    window.addEventListener('popstate', function (e) {
        const isReg = (e.state && e.state.mode === 'register') || window.location.pathname.endsWith('register.php');
        if (isReg) {
            container.classList.add('right-panel-active');
            document.title = 'Daftar Akun — Desa Sungai Bakau Kecil';
            if (mobileHeroImg) mobileHeroImg.style.backgroundImage = "url('assets/images/bg3.png')";
            if (mobileTitle)   mobileTitle.textContent = "Dapatkan Akses Layanan Digital Warga";
            if (mobileDesc)    mobileDesc.textContent = "Daftarkan akun Anda hanya dalam beberapa langkah untuk mulai memanfaatkan layanan publik desa.";
        } else {
            container.classList.remove('right-panel-active');
            document.title = 'Masuk — Desa Sungai Bakau Kecil';
            if (mobileHeroImg) mobileHeroImg.style.backgroundImage = "url('assets/images/bg2.png')";
            if (mobileTitle)   mobileTitle.textContent = "Sistem Pusat Layanan & Pengaduan Warga Desa";
            if (mobileDesc)    mobileDesc.textContent = "Bergabunglah dengan portal digital Desa Sungai Bakau Kecil untuk menyampaikan laporan pengaduan penyakit lingkungan & hukum.";
        }
    });
});
</script>

</body>
</html>

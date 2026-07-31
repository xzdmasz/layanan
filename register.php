<?php
/* ─────────────────────────────────────────────────────────────
   register.php — Registrasi Warga (Split Screen NextStep Style)
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) { header('Location: index.php'); exit; }

$redirect = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
$allowedRedirects = ['layanan-pengaduan.php', 'layanan-hukum.php', 'index.php'];
if (!in_array($redirect, $allowedRedirects)) $redirect = '';

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old    = $_POST;
    $nama   = trim($_POST['nama_lengkap']    ?? '');
    $no_hp  = trim($_POST['no_hp']           ?? '');
    $pass1  = $_POST['password']             ?? '';
    $pass2  = $_POST['password_confirm']     ?? '';

    if (empty($nama))         $errors[] = 'Nama lengkap wajib diisi.';
    elseif (mb_strlen($nama) < 3) $errors[] = 'Nama minimal 3 karakter.';

    if (empty($no_hp))        $errors[] = 'Nomor HP / WhatsApp wajib diisi.';
    elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $no_hp)) $errors[] = 'Format nomor HP tidak valid.';

    if (empty($pass1))        $errors[] = 'Password wajib diisi.';
    elseif (strlen($pass1) < 6) $errors[] = 'Password minimal 6 karakter.';
    elseif ($pass1 !== $pass2)  $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        try {
            $db   = getDB();
            $stmt = $db->prepare('SELECT id FROM users WHERE no_hp = ? LIMIT 1');
            $stmt->execute([$no_hp]);
            if ($stmt->fetch()) {
                $errors[] = 'Nomor HP sudah terdaftar. Silakan login atau gunakan nomor yang berbeda.';
            }
        } catch (Exception $e) {
            $errors[] = 'Terjadi kesalahan sistem. Coba lagi sebentar.';
            error_log('Register check no_hp: ' . $e->getMessage());
        }
    }

    if (empty($errors)) {
        try {
            $db     = getDB();
            $hashed = password_hash($pass1, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt   = $db->prepare('INSERT INTO users (nama_lengkap, no_hp, password) VALUES (?, ?, ?)');
            $stmt->execute([$nama, $no_hp, $hashed]);
            $dest = 'login.php?registered=1' . ($redirect ? '&redirect='.urlencode($redirect) : '');
            header('Location: ' . $dest);
            exit;
        } catch (Exception $e) {
            $errors[] = 'Pendaftaran gagal. Silakan coba lagi.';
            error_log('Register insert: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar Akun Warga — Sistem Pusat Layanan Desa Sungai Bakau Kecil">
    <title>Daftar Akun — Desa Sungai Bakau Kecil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            overflow-x: hidden;
        }

        .auth-split-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100vw;
        }

        /* ── LEFT HERO BANNER (bg3.png) ── */
        .auth-hero-side {
            flex: 1;
            position: relative;
            background-image: url('assets/images/bg3.png');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 56px;
            color: #ffffff;
            overflow: hidden;
        }

        .auth-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(8, 12, 18, 0.76) 0%, rgba(8, 12, 18, 0.92) 100%);
            z-index: 1;
        }

        .auth-hero-top,
        .auth-hero-bottom {
            position: relative;
            z-index: 2;
        }

        /* Top Nav inside Left Banner */
        .auth-hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .auth-back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.82);
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

        /* Bottom Text inside Left Banner */
        .auth-hero-bottom {
            max-width: 500px;
            padding-bottom: 24px;
            animation: fadeInSlideUp 0.5s ease-out;
        }

        .auth-hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 3.2vw, 2.75rem);
            font-weight: 700;
            line-height: 1.2;
            color: #ffffff;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }

        .auth-hero-desc {
            font-size: 14.5px;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 400;
        }

        /* ── RIGHT FORM PANEL ── */
        .auth-form-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px 40px;
            background: #ffffff;
            position: relative;
        }

        .auth-form-box {
            width: 100%;
            max-width: 440px;
            animation: fadeInSlide 0.4s ease-out;
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
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .auth-alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .auth-alert ul {
            margin: 6px 0 0 18px;
        }

        .auth-alert li {
            margin-bottom: 3px;
        }

        /* Form Groups */
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
            margin-top: 8px;
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

        /* Footer Link */
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

        /* Keyframes */
        @keyframes fadeInSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInSlide {
            from { opacity: 0; transform: translateX(15px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* ── RESPONSIVE MOBILE ── */
        @media (max-width: 900px) {
            .auth-split-wrapper {
                flex-direction: column;
            }
            .auth-hero-side {
                padding: 32px 24px;
                min-height: 260px;
                justify-content: space-between;
            }
            .auth-hero-title {
                font-size: 1.75rem;
                margin-bottom: 8px;
            }
            .auth-hero-desc {
                font-size: 13px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .auth-form-side {
                padding: 36px 24px;
            }
            .auth-form-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

<div class="auth-split-wrapper">

    <!-- LEFT HERO BANNER (Gambar bg3.png) -->
    <div class="auth-hero-side">
        <div class="auth-hero-overlay"></div>

        <!-- Top Navigation inside Banner -->
        <div class="auth-hero-top">
            <a href="index.php" class="auth-back-link" aria-label="Kembali ke Beranda">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Kembali
            </a>

            <div class="auth-brand-badge">
                <img src="assets/images/logo.png" alt="Logo Kabupaten Mempawah">
                <div class="auth-brand-text">
                    <div class="auth-brand-sub">Desa</div>
                    <div class="auth-brand-name">Sungai Bakau Kecil</div>
                </div>
            </div>
        </div>

        <!-- Bottom Text Content inside Banner -->
        <div class="auth-hero-bottom">
            <h1 class="auth-hero-title">Dapatkan Akses Layanan Digital Warga</h1>
            <p class="auth-hero-desc">
                Daftarkan akun Anda hanya dalam beberapa langkah mudah untuk mulai memanfaatkan layanan publik desa, pengaduan kesehatan, dan konsultasi hukum secara digital.
            </p>
        </div>
    </div>

    <!-- RIGHT FORM PANEL (Form Daftar) -->
    <div class="auth-form-side">
        <div class="auth-form-box">

            <h2 class="auth-form-title">Daftar</h2>
            <p class="auth-form-subtitle">Isi data diri Anda untuk membuat akun warga baru.</p>

            <?php if (!empty($errors)): ?>
            <div class="auth-alert auth-alert-error" role="alert">
                <strong>Perbaiki kesalahan berikut:</strong>
                <ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="register.php<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>" novalidate>

                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap (sesuai KTP) *</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap"
                           placeholder="Nama lengkap sesuai KTP"
                           value="<?= htmlspecialchars($old['nama_lengkap'] ?? '') ?>"
                           required autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="no_hp">Nomor HP / WhatsApp *</label>
                    <input type="tel" id="no_hp" name="no_hp"
                           placeholder="Contoh: 081234567890"
                           value="<?= htmlspecialchars($old['no_hp'] ?? '') ?>"
                           required autocomplete="tel">
                    <span class="input-hint">Nomor ini digunakan untuk login &amp; notifikasi pengaduan</span>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <div class="pw-wrap">
                        <input type="password" id="password" name="password"
                               placeholder="Minimal 6 karakter" required autocomplete="new-password">
                        <button type="button" class="pw-eye" onclick="togglePw('password', this)" aria-label="Tampilkan password">
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
                Sudah punya akun? <a href="login.php<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>">Masuk sekarang</a>
            </div>

        </div>
    </div>

</div>

<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const svg   = btn.querySelector('svg');
    if (input.type === 'password') {
        input.type = 'text';
        svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
        input.type = 'password';
        svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
}
</script>

</body>
</html>

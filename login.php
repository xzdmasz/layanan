<?php
/* ─────────────────────────────────────────────────────────────
   login.php — Halaman Login User (gunakan No HP + Password)
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    header('Location: ' . htmlspecialchars($redirect));
    exit;
}

$error    = '';
$redirect = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
$allowedRedirects = ['layanan-pengaduan.php', 'layanan-hukum.php', 'index.php'];
if (!in_array($redirect, $allowedRedirects)) $redirect = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_hp    = trim($_POST['no_hp']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($no_hp) || empty($password)) {
        $error = 'Nomor HP dan password wajib diisi.';
    } else {
        try {
            $db   = getDB();
            $stmt = $db->prepare('SELECT * FROM users WHERE no_hp = ? LIMIT 1');
            $stmt->execute([$no_hp]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                setUserSession($user);
                $dest = $redirect !== '' ? $redirect : 'index.php';
                header('Location: ' . $dest);
                exit;
            } else {
                $error = 'Nomor HP atau password salah. Silakan coba lagi.';
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.';
            error_log('Login error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login Warga — Sistem Pusat Layanan Desa Sungai Bakau Kecil">
    <title>Login Warga — Desa Sungai Bakau Kecil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; background: #0a0a0a; display: flex; flex-direction: column; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .auth-bg { position: fixed; inset: 0; background-image: url('assets/images/bg1.jpg'); background-size: cover; background-position: center 40%; opacity: 0.18; z-index: 0; }
        .auth-navbar { position: relative; z-index: 10; display: flex; align-items: center; justify-content: space-between; padding: 20px 32px; border-bottom: 1px solid rgba(255,255,255,0.07); }
        .auth-navbar-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .auth-navbar-brand img { height: 36px; width: auto; }
        .auth-navbar-brand .sub { font-size: 9px; font-weight: 600; letter-spacing: 0.18em; text-transform: uppercase; color: rgba(255,255,255,0.45); }
        .auth-navbar-brand .name { font-size: 13px; font-weight: 700; color: #ffffff; }
        .auth-navbar-back { font-size: 12.5px; color: rgba(255,255,255,0.55); text-decoration: none; display: flex; align-items: center; gap: 6px; transition: color 0.2s; }
        .auth-navbar-back:hover { color: #fff; }
        .auth-main { flex: 1; position: relative; z-index: 2; display: flex; align-items: center; justify-content: center; padding: 48px 20px; }
        .auth-card { width: 100%; max-width: 420px; background: #ffffff; border-radius: 4px; overflow: hidden; box-shadow: 0 24px 80px rgba(0,0,0,0.6); }
        .auth-card-header { background: #111111; padding: 32px 36px 28px; }
        .auth-card-header .label { font-size: 10px; font-weight: 600; letter-spacing: 0.2em; text-transform: uppercase; color: rgba(255,255,255,0.45); margin-bottom: 8px; }
        .auth-card-header h1 { font-family: 'Playfair Display', serif; font-size: 1.65rem; font-weight: 700; color: #ffffff; line-height: 1.2; margin: 0; }
        .auth-card-header p { font-size: 13px; color: rgba(255,255,255,0.55); margin: 10px 0 0; line-height: 1.6; }
        .auth-card-body { padding: 32px 36px 36px; }
        .auth-alert { border-radius: 3px; padding: 12px 16px; font-size: 13px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 10px; line-height: 1.5; }
        .auth-alert svg { flex-shrink: 0; width: 16px; height: 16px; margin-top: 1px; }
        .auth-alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        .auth-alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
        .form-group label { font-size: 12.5px; font-weight: 600; color: #333; }
        .form-group input { width: 100%; height: 44px; padding: 0 14px; border: 1.5px solid #ddd; border-radius: 3px; font-size: 14px; font-family: 'Inter', sans-serif; color: #111; background: #fafafa; outline: none; transition: border-color 0.2s, background 0.2s; }
        .form-group input:focus { border-color: #111; background: #fff; }
        .pw-wrap { position: relative; }
        .pw-wrap input { padding-right: 44px; }
        .pw-eye { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #aaa; padding: 4px; display: flex; align-items: center; transition: color 0.2s; }
        .pw-eye:hover { color: #555; }
        .input-hint { font-size: 11px; color: #999; }
        .auth-btn { width: 100%; height: 46px; background: #111111; color: #ffffff; border: none; border-radius: 3px; font-size: 13.5px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; transition: background 0.2s; margin-top: 8px; }
        .auth-btn:hover { background: #333; }
        .auth-footer-link { text-align: center; padding: 20px 36px 24px; border-top: 1px solid #f0f0f0; font-size: 13px; color: #666; }
        .auth-footer-link a { color: #111; font-weight: 600; text-decoration: none; }
        .auth-footer-link a:hover { text-decoration: underline; }
        @media (max-width: 480px) {
            .auth-card-header { padding: 24px; }
            .auth-card-body   { padding: 24px; }
            .auth-footer-link { padding: 16px 24px 20px; }
            .auth-navbar      { padding: 16px 20px; }
        }
    </style>
</head>
<body>
<div class="auth-bg"></div>

<nav class="auth-navbar">
    <a href="index.php" class="auth-navbar-brand">
        <img src="assets/images/logo.png" alt="Logo">
        <div><div class="sub">Desa</div><div class="name">Sungai Bakau Kecil</div></div>
    </a>
    <a href="index.php" class="auth-navbar-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali ke Beranda
    </a>
</nav>

<main class="auth-main">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="label">Portal Warga</div>
            <h1>Masuk ke Akun Anda</h1>
            <p>Gunakan nomor HP dan password yang terdaftar untuk mengakses layanan desa.</p>
        </div>
        <div class="auth-card-body">
            <?php if ($error): ?>
            <div class="auth-alert auth-alert-error" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
            <div class="auth-alert auth-alert-success" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Pendaftaran berhasil! Silakan login dengan nomor HP dan password Anda.
            </div>
            <?php endif; ?>

            <form method="POST" action="login.php<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>" novalidate>
                <div class="form-group">
                    <label for="no_hp">Nomor HP / WhatsApp *</label>
                    <input type="tel" id="no_hp" name="no_hp" placeholder="Contoh: 081234567890"
                           value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required autocomplete="tel" inputmode="tel">
                </div>
                <div class="form-group">
                    <label for="password">Password *</label>
                    <div class="pw-wrap">
                        <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required autocomplete="current-password">
                        <button type="button" class="pw-eye" onclick="togglePw('password', this)" aria-label="Tampilkan password">
                            <svg id="eye-password" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="auth-btn">Masuk →</button>
            </form>
        </div>
        <div class="auth-footer-link">
            Belum punya akun? <a href="register.php<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>">Daftar sekarang</a>
        </div>
    </div>
</main>

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

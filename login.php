<?php
/* ─────────────────────────────────────────────────────────────
   login.php — Halaman Login User (dengan Transisi Sliding Panel)
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

$loginError    = '';
$registerErrors = [];
$registerOld    = [];
$redirect       = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
$allowedRedirects = ['layanan-pengaduan.php', 'layanan-hukum.php', 'index.php'];
if (!in_array($redirect, $allowedRedirects)) $redirect = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_hp    = trim($_POST['no_hp']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($no_hp) || empty($password)) {
        $loginError = 'Nomor HP dan password wajib diisi.';
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
                $loginError = 'Nomor HP atau password salah. Silakan coba lagi.';
            }
        } catch (Exception $e) {
            $loginError = 'Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.';
            error_log('Login error: ' . $e->getMessage());
        }
    }
}

$activeMode = 'login';
require_once __DIR__ . '/includes/auth-template.php';

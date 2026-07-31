<?php
/* ─────────────────────────────────────────────────────────────
   register.php — Halaman Registrasi User (dengan Transisi Sliding Panel)
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) { header('Location: index.php'); exit; }

$loginError     = '';
$registerErrors = [];
$registerOld    = [];
$redirect       = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
$allowedRedirects = ['layanan-pengaduan.php', 'layanan-hukum.php', 'index.php'];
if (!in_array($redirect, $allowedRedirects)) $redirect = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registerOld = $_POST;
    $nama   = trim($_POST['nama_lengkap']    ?? '');
    $no_hp  = trim($_POST['no_hp']           ?? '');
    $pass1  = $_POST['password']             ?? '';
    $pass2  = $_POST['password_confirm']     ?? '';

    if (empty($nama))         $registerErrors[] = 'Nama lengkap wajib diisi.';
    elseif (mb_strlen($nama) < 3) $registerErrors[] = 'Nama minimal 3 karakter.';

    if (empty($no_hp))        $registerErrors[] = 'Nomor HP / WhatsApp wajib diisi.';
    elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $no_hp)) $registerErrors[] = 'Format nomor HP tidak valid.';

    if (empty($pass1))        $registerErrors[] = 'Password wajib diisi.';
    elseif (strlen($pass1) < 6) $registerErrors[] = 'Password minimal 6 karakter.';
    elseif ($pass1 !== $pass2)  $registerErrors[] = 'Konfirmasi password tidak cocok.';

    if (empty($registerErrors)) {
        try {
            $db   = getDB();
            $stmt = $db->prepare('SELECT id FROM users WHERE no_hp = ? LIMIT 1');
            $stmt->execute([$no_hp]);
            if ($stmt->fetch()) {
                $registerErrors[] = 'Nomor HP sudah terdaftar. Silakan login atau gunakan nomor yang berbeda.';
            }
        } catch (Exception $e) {
            $registerErrors[] = 'Terjadi kesalahan sistem. Coba lagi sebentar.';
            error_log('Register check no_hp: ' . $e->getMessage());
        }
    }

    if (empty($registerErrors)) {
        try {
            $db     = getDB();
            $hashed = password_hash($pass1, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt   = $db->prepare('INSERT INTO users (nama_lengkap, no_hp, password) VALUES (?, ?, ?)');
            $stmt->execute([$nama, $no_hp, $hashed]);
            $dest = 'login.php?registered=1' . ($redirect ? '&redirect='.urlencode($redirect) : '');
            header('Location: ' . $dest);
            exit;
        } catch (Exception $e) {
            $registerErrors[] = 'Pendaftaran gagal. Silakan coba lagi.';
            error_log('Register insert: ' . $e->getMessage());
        }
    }
}

$activeMode = 'register';
require_once __DIR__ . '/includes/auth-template.php';

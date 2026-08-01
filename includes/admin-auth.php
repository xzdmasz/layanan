<?php
/* ─────────────────────────────────────────────────────────────
   includes/admin-auth.php — Helper Autentikasi Session Admin
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Cek apakah admin sudah login; jika tidak, redirect ke admin/login.php */
function cekAdmin(): void {
    if (!isset($_SESSION['admin']['id'])) {
        header('Location: ' . (defined('ADMIN_ROOT') ? ADMIN_ROOT : '') . 'login.php');
        exit;
    }
}

/** Cek status login admin (tanpa redirect) */
function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin']['id']);
}

/** Ambil data admin dari session */
function getAdmin(): ?array {
    return $_SESSION['admin'] ?? null;
}

/** Set session setelah admin login */
function setAdminSession(array $data): void {
    $_SESSION['admin'] = [
        'id'       => $data['id'],
        'username' => $data['username'],
        'nama'     => $data['nama'],
        'role'     => $data['role'],
    ];
}

/** Hapus session admin (logout) */
function logoutAdmin(): void {
    unset($_SESSION['admin']);
}

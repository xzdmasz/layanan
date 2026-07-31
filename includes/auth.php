<?php
/* ─────────────────────────────────────────────────────────────
   includes/auth.php — Helper Autentikasi Session User
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah user sudah login.
 * Jika belum, redirect ke login.php dengan parameter redirect.
 *
 * @param string $redirect Nama halaman tujuan setelah login berhasil
 */
function cekLoginUser(string $redirect = ''): void {
    if (!isset($_SESSION['user']['id'])) {
        $url = 'login.php';
        if ($redirect !== '') {
            $url .= '?redirect=' . urlencode($redirect);
        }
        header('Location: ' . $url);
        exit;
    }
}

/**
 * Cek apakah user sudah login (tanpa redirect).
 *
 * @return bool
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user']['id']);
}

/**
 * Ambil data session user yang sedang login.
 *
 * @return array|null
 */
function getUser(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Set session setelah login berhasil.
 *
 * @param array $userData Data user dari database
 */
function setUserSession(array $userData): void {
    $_SESSION['user'] = [
        'id'          => $userData['id'],
        'nama_lengkap'=> $userData['nama_lengkap'],
        'no_hp'       => $userData['no_hp'],
    ];
}

/**
 * Hapus session user (logout).
 */
function logoutUser(): void {
    unset($_SESSION['user']);
}

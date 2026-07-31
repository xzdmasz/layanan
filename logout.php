<?php
/* ─────────────────────────────────────────────────────────────
   logout.php — Proses Logout User
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/auth.php';
logoutUser();

header('Location: index.php');
exit;

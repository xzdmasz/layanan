<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/admin-auth.php';
logoutAdmin();
header('Location: login.php');
exit;

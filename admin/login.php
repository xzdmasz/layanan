<?php
/* ─────────────────────────────────────────────────────────────
   admin/login.php — Login Halaman Admin Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-auth.php';

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        try {
            $db   = getDB();
            $stmt = $db->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                setAdminSession($admin);
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Username atau password salah.';
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan sistem. Coba beberapa saat lagi.';
            error_log('Admin login error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Desa Sungai Bakau Kecil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Background pattern */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(255,255,255,0.03) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 70%, rgba(255,255,255,0.02) 0%, transparent 60%);
            pointer-events: none;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #111111;
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 4px;
            padding: 48px 40px;
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: #ffffff;
            border-radius: 4px 4px 0 0;
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.40);
            margin-bottom: 12px;
        }

        .login-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.7rem;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .login-sub {
            font-size: 12px;
            color: rgba(255,255,255,0.35);
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.50);
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 3px;
            padding: 12px 14px;
            font-size: 14px;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: rgba(255,255,255,0.40);
            background: rgba(255,255,255,0.07);
        }

        .error-box {
            background: rgba(220, 38, 38, 0.12);
            border: 1px solid rgba(220, 38, 38, 0.35);
            border-radius: 3px;
            padding: 10px 14px;
            font-size: 12.5px;
            color: #fca5a5;
            margin-bottom: 20px;
        }

        .btn-login {
            width: 100%;
            background: #ffffff;
            color: #111111;
            border: none;
            border-radius: 3px;
            padding: 13px 20px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            margin-top: 8px;
        }

        .btn-login:hover { background: #e5e5e5; }
        .btn-login:active { transform: scale(0.98); }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 11.5px;
            color: rgba(255,255,255,0.25);
        }

        .login-footer a {
            color: rgba(255,255,255,0.45);
            text-decoration: none;
        }

        .login-footer a:hover { color: #ffffff; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <span class="login-badge">Admin Panel</span>
            <h1 class="login-title">Masuk sebagai Admin</h1>
            <p class="login-sub">Desa Sungai Bakau Kecil</p>
        </div>

        <?php if ($error): ?>
        <div class="error-box"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       placeholder="Masukkan username" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Masukkan password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn-login">Masuk ke Dashboard</button>
        </form>

        <p class="login-footer">
            <a href="../index.php">← Kembali ke Beranda</a>
        </p>
    </div>
</body>
</html>

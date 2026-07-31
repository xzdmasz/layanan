<?php
/* ─────────────────────────────────────────────────────────────
   akun.php — Kelola Profil & Ubah Password User
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

require_once 'includes/auth.php';
cekLoginUser('akun.php');
require_once 'includes/db.php';

$user      = getUser();
$db        = getDB();
$errors    = [];
$success   = '';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. UPDATE PROFIL
    if ($action === 'update_profile') {
        $nama  = trim($_POST['nama_lengkap'] ?? '');
        $no_hp = trim($_POST['no_hp'] ?? '');

        if (empty($nama)) {
            $errors[] = 'Nama lengkap wajib diisi.';
        } elseif (mb_strlen($nama) < 3) {
            $errors[] = 'Nama minimal 3 karakter.';
        }

        if (empty($no_hp)) {
            $errors[] = 'Nomor HP wajib diisi.';
        } elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $no_hp)) {
            $errors[] = 'Format nomor HP tidak valid.';
        }

        // Check HP Uniqueness if changed
        if (empty($errors) && $no_hp !== $user['no_hp']) {
            $stmt = $db->prepare('SELECT id FROM users WHERE no_hp = ? AND id != ? LIMIT 1');
            $stmt->execute([$no_hp, $user['id']]);
            if ($stmt->fetch()) {
                $errors[] = 'Nomor HP tersebut sudah digunakan oleh akun lain.';
            }
        }

        if (empty($errors)) {
            try {
                $stmt = $db->prepare('UPDATE users SET nama_lengkap = ?, no_hp = ? WHERE id = ?');
                $stmt->execute([$nama, $no_hp, $user['id']]);

                // Update session
                $_SESSION['user']['nama_lengkap'] = $nama;
                $_SESSION['user']['no_hp']        = $no_hp;
                $user = getUser();

                $success = 'Profil Anda berhasil diperbarui!';
            } catch (Exception $e) {
                $errors[] = 'Gagal memperbarui profil. Silakan coba lagi.';
                error_log('Update Profile Error: ' . $e->getMessage());
            }
        }
    }

    // 2. UBAH PASSWORD
    if ($action === 'change_password') {
        $pw_lama  = $_POST['pw_lama']  ?? '';
        $pw_baru  = $_POST['pw_baru']  ?? '';
        $pw_konf  = $_POST['pw_konf']  ?? '';

        if (empty($pw_lama) || empty($pw_baru) || empty($pw_konf)) {
            $errors[] = 'Semua field password wajib diisi.';
        } elseif (strlen($pw_baru) < 6) {
            $errors[] = 'Password baru minimal 6 karakter.';
        } elseif ($pw_baru !== $pw_konf) {
            $errors[] = 'Konfirmasi password baru tidak cocok.';
        } else {
            // Verify current password from DB
            $stmt = $db->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([$user['id']]);
            $currentData = $stmt->fetch();

            if (!$currentData || !password_verify($pw_lama, $currentData['password'])) {
                $errors[] = 'Password saat ini salah.';
            } else {
                try {
                    $newHashed = password_hash($pw_baru, PASSWORD_BCRYPT, ['cost' => 12]);
                    $updateStmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $updateStmt->execute([$newHashed, $user['id']]);
                    $success = 'Password berhasil diubah!';
                } catch (Exception $e) {
                    $errors[] = 'Gagal mengubah password. Coba lagi.';
                    error_log('Change Password Error: ' . $e->getMessage());
                }
            }
        }
    }
}

$pageTitle = 'Kelola Akun';
require_once 'includes/header.php';
?>

<!-- Banner Header -->
<section style="background:#0a0a0a; position:relative; padding:100px 0 50px; overflow:hidden;">
    <div style="position:absolute; inset:0; background-image:url('assets/images/bg2.png'); background-size:cover; background-position:center; opacity:0.25;"></div>
    <div class="container" style="position:relative; z-index:2;">
        <span class="section-label" style="color:rgba(255,255,255,0.60);">Portal Warga</span>
        <h1 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,3.5vw,2.8rem); font-weight:900; color:#ffffff; margin:0 0 8px;">
            Kelola Akun Anda
        </h1>
        <p style="font-size:13.5px; color:rgba(255,255,255,0.70); margin:0;">
            Perbarui data diri atau atur kata sandi akun Anda secara berkala untuk menjaga keamanan.
        </p>
    </div>
</section>

<!-- Content -->
<section style="background:#ffffff; padding:60px 0 80px;">
    <div class="container">
        <div style="max-width:720px; margin:0 auto;">

            <?php if ($success): ?>
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:14px 18px; border-radius:4px; font-size:13.5px; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:14px 18px; border-radius:4px; font-size:13.5px; margin-bottom:24px;">
                    <strong>Perbaiki kesalahan berikut:</strong>
                    <ul style="margin:6px 0 0 16px;">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Card 1: Data Profil -->
            <div style="border:1px solid #e5e5e5; border-radius:6px; background:#fff; padding:28px; margin-bottom:32px;">
                <h2 style="font-family:'Playfair Display',serif; font-size:1.35rem; font-weight:700; color:#111; margin:0 0 20px;">
                    Informasi Profil
                </h2>
                
                <form method="POST" action="akun.php" style="display:flex; flex-direction:column; gap:18px;">
                    <input type="hidden" name="action" value="update_profile">

                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <label style="font-size:12.5px; font-weight:600; color:#333;">Nama Lengkap (sesuai KTP) *</label>
                        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required class="form-input" style="height:44px; padding:0 14px; border:1.5px solid #ddd; border-radius:3px; font-size:14px;">
                    </div>

                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <label style="font-size:12.5px; font-weight:600; color:#333;">Nomor HP / WhatsApp *</label>
                        <input type="tel" name="no_hp" value="<?= htmlspecialchars($user['no_hp']) ?>" required class="form-input" style="height:44px; padding:0 14px; border:1.5px solid #ddd; border-radius:3px; font-size:14px;">
                    </div>

                    <button type="submit" class="btn-dark" style="align-self:flex-start; height:42px; padding:0 24px; cursor:pointer;">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            <!-- Card 2: Ubah Password -->
            <div style="border:1px solid #e5e5e5; border-radius:6px; background:#fff; padding:28px;">
                <h2 style="font-family:'Playfair Display',serif; font-size:1.35rem; font-weight:700; color:#111; margin:0 0 20px;">
                    Ubah Password
                </h2>

                <form method="POST" action="akun.php" style="display:flex; flex-direction:column; gap:18px;">
                    <input type="hidden" name="action" value="change_password">

                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <label style="font-size:12.5px; font-weight:600; color:#333;">Password Saat Ini *</label>
                        <div style="position:relative;">
                            <input type="password" id="pw_lama" name="pw_lama" required class="form-input" style="width:100%; height:44px; padding:0 44px 0 14px; border:1.5px solid #ddd; border-radius:3px; font-size:14px;">
                            <button type="button" onclick="togglePw('pw_lama', this)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#aaa;" aria-label="Toggle password">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div style="display:flex; flex-direction:column; gap:6px;">
                            <label style="font-size:12.5px; font-weight:600; color:#333;">Password Baru *</label>
                            <div style="position:relative;">
                                <input type="password" id="pw_baru" name="pw_baru" required class="form-input" style="width:100%; height:44px; padding:0 44px 0 14px; border:1.5px solid #ddd; border-radius:3px; font-size:14px;">
                                <button type="button" onclick="togglePw('pw_baru', this)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#aaa;" aria-label="Toggle password">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column; gap:6px;">
                            <label style="font-size:12.5px; font-weight:600; color:#333;">Konfirmasi Password Baru *</label>
                            <div style="position:relative;">
                                <input type="password" id="pw_konf" name="pw_konf" required class="form-input" style="width:100%; height:44px; padding:0 44px 0 14px; border:1.5px solid #ddd; border-radius:3px; font-size:14px;">
                                <button type="button" onclick="togglePw('pw_konf', this)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#aaa;" aria-label="Toggle password">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-dark" style="align-self:flex-start; height:42px; padding:0 24px; cursor:pointer; background:#333;">
                        Ubah Password
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

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

<?php require_once 'includes/footer.php'; ?>

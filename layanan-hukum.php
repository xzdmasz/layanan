<?php
/* ─────────────────────────────────────────────────────────────
   layanan-hukum.php — Layanan Hukum Desa
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

require_once 'includes/auth.php';
cekLoginUser('layanan-hukum.php');
require_once 'includes/db.php';

$user      = getUser();
$errors    = [];
$success   = false;
$noLaporan = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_masalah = trim($_POST['jenis_masalah'] ?? '');
    $ringkasan     = trim($_POST['ringkasan']     ?? '');

    $jenisList = ['tanah','sengketa','keluarga','perjanjian','lainnya'];
    if (empty($jenis_masalah) || !in_array($jenis_masalah, $jenisList)) {
        $errors[] = 'Pilih jenis permasalahan hukum yang valid.';
    }
    if (empty($ringkasan)) {
        $errors[] = 'Ringkasan permasalahan wajib diisi.';
    }

    if (empty($errors)) {
        try {
            $db   = getDB();
            $stmt = $db->prepare('
                INSERT INTO pengaduan_hukum
                    (user_id, nama_pemohon, nik_pemohon, no_telp, jenis_masalah, ringkasan)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $user['id'],
                $user['nama_lengkap'],
                '-', // Fallback value for NIK
                $user['no_hp'],
                $jenis_masalah,
                $ringkasan,
            ]);
            $noLaporan = $db->lastInsertId();
            $success   = true;
        } catch (Exception $e) {
            $errors[] = 'Gagal menyimpan permohonan. Silakan coba lagi.';
            error_log('Pengaduan Hukum insert error: ' . $e->getMessage());
        }
    }
}

$pageTitle = 'Layanan Hukum';
require_once 'includes/header.php';
?>

<!-- Banner Header -->
<section style="background:#0a0a0a; position:relative; padding:120px 0 60px; overflow:hidden;">
    <div style="position:absolute; inset:0; background-image:url('assets/images/bg2.png'); background-size:cover; background-position:center 50%; opacity:0.35;"></div>
    <div class="container" style="position:relative; z-index:2;">
        <span class="section-label" style="color:rgba(255,255,255,0.60);">Bantuan Warga</span>
        <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,4vw,3.2rem); font-weight:900; color:#ffffff; margin:0 0 12px; line-height:1.15;">
            Layanan Hukum Desa
        </h1>
        <p style="font-size:14px; color:rgba(255,255,255,0.75); max-width:560px; margin:0; line-height:1.7;">
            Konsultasi hukum gratis, pendampingan masalah pertanahan, sengketa antar warga, dan administrasi hukum bagi warga Desa Sungai Bakau Kecil.
        </p>
    </div>
</section>

<!-- Content -->
<section style="background:#ffffff; padding:72px 0;">
    <div class="container">

        <!-- Bidang Layanan -->
        <div style="margin-bottom:56px;">
            <span class="section-label">Bantuan &amp; Konsultasi</span>
            <span class="divider"></span>
            <h2 style="font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:700; margin:0 0 32px; color:#111;">
                Cakupan Layanan Hukum Warga
            </h2>

            <div class="legal-services-grid">
                <div style="background:#fff; padding:28px 24px;">
                    <h3 style="font-family:'Playfair Display',serif; font-size:1.15rem; font-weight:700; color:#111; margin:0 0 10px;">Sengketa Lahan &amp; Tanah</h3>
                    <p style="font-size:13px; color:#555; line-height:1.65; margin:0;">
                        Mediasi batas kepemilikan tanah, surat riwayat tanah, dan konsultasi legalitas sertifikat untuk warga desa.
                    </p>
                </div>
                <div style="background:#fff; padding:28px 24px;">
                    <h3 style="font-family:'Playfair Display',serif; font-size:1.15rem; font-weight:700; color:#111; margin:0 0 10px;">Konsultasi Keluarga</h3>
                    <p style="font-size:13px; color:#555; line-height:1.65; margin:0;">
                        Bantuan informasi hukum waris, hak asuh, perizinan pernikahan, dan masalah administrasi keluarga.
                    </p>
                </div>
                <div style="background:#fff; padding:28px 24px;">
                    <h3 style="font-family:'Playfair Display',serif; font-size:1.15rem; font-weight:700; color:#111; margin:0 0 10px;">Pendampingan Mediasi</h3>
                    <p style="font-size:13px; color:#555; line-height:1.65; margin:0;">
                        Penyelesaian perselisihan secara kekeluargaan (restoratif) dipimpin oleh Kepala Desa dan Pos Bantuan Hukum.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Permohonan Konsultasi -->
        <div class="form-layout">
            <div>
                <span class="section-label">Pengajuan Jadwal</span>
                <span class="divider"></span>
                <h2 style="font-family:'Playfair Display',serif; font-size:1.6rem; font-weight:700; margin:0 0 24px; color:#111;">
                    Form Permohonan Konsultasi Hukum
                </h2>

                <?php if ($success): ?>
                <div style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:4px; padding:24px 28px; margin-bottom:28px;">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11.5 14.5 15 10"/></svg>
                        <strong style="font-size:15px; color:#15803d; font-family:'Playfair Display',serif;">Permohonan Berhasil Diajukan!</strong>
                    </div>
                    <p style="font-size:13.5px; color:#166534; margin:0 0 8px; line-height:1.6;">
                        Permohonan Anda telah tercatat dengan <strong>Nomor Laporan #<?= $noLaporan ?></strong>.
                        Tim hukum desa akan menghubungi Anda via WhatsApp di nomor
                        <strong><?= htmlspecialchars($user['no_hp']) ?></strong> untuk konfirmasi jadwal konsultasi.
                    </p>
                    <div style="display:flex; gap:12px; margin-top:16px;">
                        <a href="riwayat.php" class="btn-dark" style="font-size:12.5px; padding:8px 16px;">Lihat Riwayat Pengaduan</a>
                        <a href="layanan-hukum.php" style="font-size:12.5px; padding:8px 16px; border:1px solid #ddd; color:#333; text-decoration:none; border-radius:3px;">Buat Permohonan Baru</a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                <div style="background:#fef2f2; border:1.5px solid #fecaca; border-radius:4px; padding:16px 20px; margin-bottom:24px;">
                    <strong style="font-size:13px; color:#b91c1c; display:block; margin-bottom:8px;">Perbaiki kesalahan berikut:</strong>
                    <ul style="margin:0; padding-left:18px; font-size:13px; color:#b91c1c; line-height:1.8;">
                        <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!$success): ?>
                <form action="layanan-hukum.php" method="POST" style="display:flex; flex-direction:column; gap:20px;">

                    <div class="form-row-2col">
                        <div>
                            <label class="form-label">Nama Pemohon</label>
                            <input type="text" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" class="form-input" disabled style="background:#f5f5f5; color:#666; cursor:not-allowed;">
                        </div>
                        <div>
                            <label class="form-label">Nomor Telepon / WA</label>
                            <input type="tel" value="<?= htmlspecialchars($user['no_hp']) ?>" class="form-input" disabled style="background:#f5f5f5; color:#666; cursor:not-allowed;">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Jenis Permasalahan Hukum *</label>
                        <select name="jenis_masalah" required class="form-input">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="tanah"     <?= (($_POST['jenis_masalah']??'')==='tanah')?'selected':'' ?>>Pertanahan &amp; Batas Lahan</option>
                            <option value="sengketa"  <?= (($_POST['jenis_masalah']??'')==='sengketa')?'selected':'' ?>>Sengketa Antar Warga</option>
                            <option value="keluarga"  <?= (($_POST['jenis_masalah']??'')==='keluarga')?'selected':'' ?>>Hukum Keluarga &amp; Waris</option>
                            <option value="perjanjian"<?= (($_POST['jenis_masalah']??'')==='perjanjian')?'selected':'' ?>>Perjanjian &amp; Usaha Desa</option>
                            <option value="lainnya"   <?= (($_POST['jenis_masalah']??'')==='lainnya')?'selected':'' ?>>Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Ringkasan Permasalahan *</label>
                        <textarea name="ringkasan" rows="5" required
                                  placeholder="Uraikan kronologi singkat masalah yang ingin dikonsultasikan secara rahasia dan aman..."
                                  class="form-input" style="resize:vertical;"><?= htmlspecialchars($_POST['ringkasan'] ?? '') ?></textarea>
                    </div>

                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <button type="submit" class="btn-dark" style="cursor:pointer;">
                            Ajukan Jadwal Konsultasi
                        </button>
                    </div>

                </form>
                <?php endif; ?>
            </div>

            <!-- Sidebar Info -->
            <div class="form-sidebar">
                <div class="accordion-box">
                    <button class="accordion-header" type="button" aria-expanded="false">
                        <span>Pos Bantuan Hukum Desa</span>
                        <svg class="accordion-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="accordion-body">
                        <p style="font-size:13px; color:#555; line-height:1.7; margin:0 0 16px;">
                            Jadwal konsultasi tatap muka gratis bersama paralegal / advokat pendamping desa:
                        </p>
                        <div style="font-size:13px; color:#333; line-height:1.8;">
                            <strong style="color:#111111; display:block;">Jadwal Layanan:</strong>
                            <span>Setiap Hari Selasa &amp; Kamis</span><br>
                            <span>Pukul 09.00 – 14.00 WIB</span><br><br>
                            <strong style="color:#111111; display:block;">Lokasi:</strong>
                            <span>Ruang Mediasi Balai Desa Sungai Bakau Kecil</span>
                        </div>
                    </div>
                </div>

                <div class="accordion-box">
                    <button class="accordion-header" type="button" aria-expanded="false">
                        <span>Prinsip Kerahasiaan</span>
                        <svg class="accordion-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="accordion-body">
                        <p style="font-size:13px; color:#555; line-height:1.7; margin:0;">
                            Seluruh informasi dan berkas yang disampaikan dalam layanan hukum desa terjamin kerahasiaannya sesuai ketentuan undang-undang bantuan hukum.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

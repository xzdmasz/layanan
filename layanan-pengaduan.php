<?php
/* ─────────────────────────────────────────────────────────────
   layanan-pengaduan.php — Layanan & Pengaduan Penyakit
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

require_once 'includes/auth.php';
cekLoginUser('layanan-pengaduan.php');
require_once 'includes/db.php';

$user      = getUser();
$errors    = [];
$success   = false;
$noLaporan = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori        = trim($_POST['kategori']        ?? '');
    $alamat_kejadian = trim($_POST['alamat_kejadian'] ?? '');
    $detail_gejala   = trim($_POST['detail_gejala']  ?? '');

    $kategoris = ['demam_berdarah','penyakit_menular','sanitasi','posyandu','lainnya'];
    if (empty($kategori) || !in_array($kategori, $kategoris)) {
        $errors[] = 'Pilih kategori pengaduan yang valid.';
    }
    if (empty($alamat_kejadian)) {
        $errors[] = 'Alamat / lokasi kejadian wajib diisi.';
    }
    if (empty($detail_gejala)) {
        $errors[] = 'Detail gejala / masalah wajib diisi.';
    }

    if (empty($errors)) {
        try {
            $db   = getDB();
            $stmt = $db->prepare('
                INSERT INTO pengaduan_kesehatan
                    (user_id, nama_lengkap, nik, no_hp, kategori, alamat_kejadian, detail_gejala)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $user['id'],
                $user['nama_lengkap'],
                '-', // Fallback value for NIK
                $user['no_hp'],
                $kategori,
                $alamat_kejadian,
                $detail_gejala,
            ]);
            $noLaporan = $db->lastInsertId();
            $success   = true;
        } catch (Exception $e) {
            $errors[] = 'Gagal menyimpan laporan. Silakan coba lagi.';
            error_log('Pengaduan Kesehatan insert error: ' . $e->getMessage());
        }
    }
}

$pageTitle = 'Layanan & Pengaduan Penyakit';
require_once 'includes/header.php';
?>

<!-- Banner Header -->
<section style="background:#0a0a0a; position:relative; padding:120px 0 60px; overflow:hidden;">
    <div style="position:absolute; inset:0; background-image:url('assets/images/bg3.png'); background-size:cover; background-position:center 67%; opacity:0.40;"></div>
    <div class="container" style="position:relative; z-index:2;">
        <span class="section-label" style="color:rgba(255,255,255,0.60);">Pusat Layanan Warga</span>
        <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,4vw,3.2rem); font-weight:900; color:#ffffff; margin:0 0 12px; line-height:1.15;">
            Layanan &amp; Pengaduan Penyakit
        </h1>
        <p style="font-size:14px; color:rgba(255,255,255,0.75); max-width:560px; margin:0; line-height:1.7;">
            Sampaikan pengaduan penyakit menular, masalah kesehatan lingkungan, atau kebutuhan penanganan medis darurat di Desa Sungai Bakau Kecil.
        </p>
    </div>
</section>

<!-- Content -->
<section style="background:#ffffff; padding:72px 0;">
    <div class="container">
        <div class="form-layout">

            <!-- Form Pengaduan -->
            <div>
                <span class="section-label">Formulir Laporan</span>
                <span class="divider"></span>
                <h2 style="font-family:'Playfair Display',serif; font-size:1.6rem; font-weight:700; margin:0 0 24px; color:#111;">
                    Formulir Pengaduan Kesehatan
                </h2>

                <?php if ($success): ?>
                <div style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:4px; padding:24px 28px; margin-bottom:28px;">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11.5 14.5 15 10"/></svg>
                        <strong style="font-size:15px; color:#15803d; font-family:'Playfair Display',serif;">Laporan Berhasil Dikirim!</strong>
                    </div>
                    <p style="font-size:13.5px; color:#166534; margin:0 0 8px; line-height:1.6;">
                        Pengaduan Anda telah tercatat dengan <strong>Nomor Laporan #<?= $noLaporan ?></strong>.
                        Tim kesehatan desa akan menghubungi Anda via WhatsApp di nomor
                        <strong><?= htmlspecialchars($user['no_hp']) ?></strong> untuk tindak lanjut.
                    </p>
                    <div style="display:flex; gap:12px; margin-top:16px;">
                        <a href="riwayat.php" class="btn-dark" style="font-size:12.5px; padding:8px 16px;">Lihat Riwayat Pengaduan</a>
                        <a href="layanan-pengaduan.php" style="font-size:12.5px; padding:8px 16px; border:1px solid #ddd; color:#333; text-decoration:none; border-radius:3px;">Buat Laporan Baru</a>
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
                <form action="layanan-pengaduan.php" method="POST" style="display:flex; flex-direction:column; gap:20px;">

                    <div class="form-row-2col">
                        <div>
                            <label class="form-label">Nama Pelapor</label>
                            <input type="text" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" class="form-input" disabled style="background:#f5f5f5; color:#666; cursor:not-allowed;">
                        </div>
                        <div>
                            <label class="form-label">Nomor HP / WhatsApp</label>
                            <input type="tel" value="<?= htmlspecialchars($user['no_hp']) ?>" class="form-input" disabled style="background:#f5f5f5; color:#666; cursor:not-allowed;">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Kategori Pengaduan *</label>
                        <select name="kategori" required class="form-input">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="demam_berdarah" <?= (($_POST['kategori']??'')==='demam_berdarah')?'selected':'' ?>>Laporan Kasus Demam Berdarah (DBD)</option>
                            <option value="penyakit_menular" <?= (($_POST['kategori']??'')==='penyakit_menular')?'selected':'' ?>>Penyakit Menular Lainnya</option>
                            <option value="sanitasi" <?= (($_POST['kategori']??'')==='sanitasi')?'selected':'' ?>>Masalah Air Bersih &amp; Sanitasi Lingkungan</option>
                            <option value="posyandu" <?= (($_POST['kategori']??'')==='posyandu')?'selected':'' ?>>Layanan Balita / Ibu Hamil</option>
                            <option value="lainnya" <?= (($_POST['kategori']??'')==='lainnya')?'selected':'' ?>>Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Alamat / Lokasi Kejadian *</label>
                        <input type="text" name="alamat_kejadian" required
                               placeholder="Dusun, RT/RW, atau petunjuk lokasi"
                               value="<?= htmlspecialchars($_POST['alamat_kejadian'] ?? '') ?>"
                               class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Detail Gejala / Masalah Kesehatan *</label>
                        <textarea name="detail_gejala" rows="5" required
                                  placeholder="Jelaskan secara singkat gejala yang dialami, jumlah orang terdampak, atau masalah yang ditemukan..."
                                  class="form-input" style="resize:vertical;"><?= htmlspecialchars($_POST['detail_gejala'] ?? '') ?></textarea>
                    </div>

                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <button type="submit" class="btn-dark" style="cursor:pointer;">
                            Kirim Laporan Pengaduan
                        </button>
                    </div>

                </form>
                <?php endif; ?>
            </div>

            <!-- Sidebar Info -->
            <div class="form-sidebar">
                <div class="accordion-box">
                    <button class="accordion-header" type="button" aria-expanded="false">
                        <span>Kontak Darurat Kesehatan</span>
                        <svg class="accordion-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="accordion-body">
                        <p style="font-size:13px; color:#555; line-height:1.7; margin:0 0 16px;">
                            Jika mengalami kondisi darurat medis yang butuh penanganan secepatnya, segera hubungi kontak berikut:
                        </p>
                        <div style="display:flex; flex-direction:column; gap:12px; font-size:13px;">
                            <div>
                                <strong style="color:#111111; display:block;">Puskesmas Pembantu Desa</strong>
                                <span style="color:#444;">(0561) 987-6543</span>
                            </div>
                            <div>
                                <strong style="color:#111111; display:block;">Ambulans Desa (24 Jam)</strong>
                                <span style="color:#444;">0812-3456-7890</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-box">
                    <button class="accordion-header" type="button" aria-expanded="false">
                        <span>Prosedur Penanganan</span>
                        <svg class="accordion-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="accordion-body">
                        <ol style="font-size:13px; color:#555; line-height:1.8; margin:0; padding-left:18px;">
                            <li style="margin-bottom:8px;">Laporan masuk dan diverifikasi tim kesehatan desa.</li>
                            <li style="margin-bottom:8px;">Petugas kesehatan melakukan konfirmasi via telepon/WA.</li>
                            <li>Laporan dicatat dalam sistem pemantauan kesehatan desa.</li>
                        </ol>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

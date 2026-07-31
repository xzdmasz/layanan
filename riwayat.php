<?php
/* ─────────────────────────────────────────────────────────────
   riwayat.php — Riwayat Pengaduan User
   Desa Sungai Bakau Kecil
   ───────────────────────────────────────────────────────────── */

require_once 'includes/auth.php';
cekLoginUser('riwayat.php');
require_once 'includes/db.php';

$user = getUser();
$db   = getDB();

// Fetch pengaduan kesehatan
$stmtKesehatan = $db->prepare('
    SELECT *, "kesehatan" AS jenis_layanan
    FROM pengaduan_kesehatan
    WHERE user_id = ?
    ORDER BY created_at DESC
');
$stmtKesehatan->execute([$user['id']]);
$laporanKesehatan = $stmtKesehatan->fetchAll();

// Fetch pengaduan hukum
$stmtHukum = $db->prepare('
    SELECT *, "hukum" AS jenis_layanan
    FROM pengaduan_hukum
    WHERE user_id = ?
    ORDER BY created_at DESC
');
$stmtHukum->execute([$user['id']]);
$laporanHukum = $stmtHukum->fetchAll();

// Gabungkan & urutkan berdasarkan tanggal terbaru
$semuaLaporan = array_merge($laporanKesehatan, $laporanHukum);
usort($semuaLaporan, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$pageTitle = 'Riwayat Pengaduan';
require_once 'includes/header.php';
?>

<!-- Banner Header -->
<section style="background:#0a0a0a; position:relative; padding:100px 0 50px; overflow:hidden;">
    <div style="position:absolute; inset:0; background-image:url('assets/images/bg1.jpg'); background-size:cover; background-position:center; opacity:0.25;"></div>
    <div class="container" style="position:relative; z-index:2;">
        <span class="section-label" style="color:rgba(255,255,255,0.60);">Portal Warga</span>
        <h1 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,3.5vw,2.8rem); font-weight:900; color:#ffffff; margin:0 0 8px;">
            Riwayat Pengaduan Anda
        </h1>
        <p style="font-size:13.5px; color:rgba(255,255,255,0.70); margin:0;">
            Pantau status tindak lanjut pengaduan kesehatan dan permohonan konsultasi hukum yang telah Anda ajukan.
        </p>
    </div>
</section>

<!-- Main Content -->
<section style="background:#ffffff; padding:60px 0 80px;">
    <div class="container">

        <?php if (empty($semuaLaporan)): ?>
            <div style="text-align:center; padding:60px 20px; background:#fafafa; border:1px dashed #ddd; border-radius:6px; max-width:540px; margin:0 auto;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:16px;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <h3 style="font-family:'Playfair Display',serif; font-size:1.25rem; font-weight:700; color:#111; margin:0 0 8px;">Belum Ada Pengaduan</h3>
                <p style="font-size:13px; color:#666; margin:0 0 24px; line-height:1.6;">
                    Anda belum pernah mengirimkan pengaduan kesehatan maupun permohonan konsultasi hukum.
                </p>
                <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                    <a href="layanan-pengaduan.php" class="btn-dark" style="font-size:12px; padding:10px 18px;">Pengaduan Kesehatan</a>
                    <a href="layanan-hukum.php" class="btn-dark" style="font-size:12px; padding:10px 18px; background:#333;">Layanan Hukum</a>
                </div>
            </div>
        <?php else: ?>

            <div style="display:flex; flex-direction:column; gap:20px; max-width:840px; margin:0 auto;">
                <?php foreach ($semuaLaporan as $item): ?>
                    <?php
                    $isKesehatan = $item['jenis_layanan'] === 'kesehatan';
                    $status      = strtolower($item['status']);
                    
                    // Badge styles
                    $badgeStyle = 'background:#fef3c7; color:#92400e; border:1px solid #fde68a;'; // masuk (amber)
                    $statusLabel = 'Masuk';
                    if ($status === 'proses') {
                        $badgeStyle = 'background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe;'; // proses (blue)
                        $statusLabel = 'Sedang Diproses';
                    } elseif ($status === 'selesai') {
                        $badgeStyle = 'background:#dcfce7; color:#166534; border:1px solid #bbf7d0;'; // selesai (green)
                        $statusLabel = 'Selesai';
                    }
                    ?>
                    
                    <div style="border:1px solid #e5e5e5; border-radius:6px; background:#fff; padding:24px; transition:box-shadow 0.2s, border-color 0.2s;" onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.06)'; this.style.borderColor='#ccc';" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e5e5e5';">
                        
                        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid #f0f0f0;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; padding:4px 10px; border-radius:3px; <?= $isKesehatan ? 'background:#111; color:#fff;' : 'background:#444; color:#fff;' ?>">
                                    <?= $isKesehatan ? '🏥 Pengaduan Kesehatan' : '⚖️ Layanan Hukum' ?>
                                </span>
                                <span style="font-size:12px; color:#888;">
                                    #<?= $item['id'] ?> • <?= date('d M Y, H:i', strtotime($item['created_at'])) ?> WIB
                                </span>
                            </div>

                            <span style="font-size:12px; font-weight:600; padding:4px 12px; border-radius:20px; <?= $badgeStyle ?>">
                                ● <?= $statusLabel ?>
                            </span>
                        </div>

                        <!-- Content Detail -->
                        <div style="font-size:13.5px; color:#333; line-height:1.6;">
                            <?php if ($isKesehatan): ?>
                                <p style="margin:0 0 6px;"><strong>Kategori:</strong> <?= htmlspecialchars(str_replace('_', ' ', strtoupper($item['kategori']))) ?></p>
                                <p style="margin:0 0 6px;"><strong>Lokasi:</strong> <?= htmlspecialchars($item['alamat_kejadian']) ?></p>
                                <p style="margin:0; color:#555;"><strong>Detail Gejala/Masalah:</strong> <?= nl2br(htmlspecialchars($item['detail_gejala'])) ?></p>
                            <?php else: ?>
                                <p style="margin:0 0 6px;"><strong>Jenis Permasalahan:</strong> <?= htmlspecialchars(str_replace('_', ' ', strtoupper($item['jenis_masalah']))) ?></p>
                                <p style="margin:0; color:#555;"><strong>Ringkasan Permasalahan:</strong> <?= nl2br(htmlspecialchars($item['ringkasan'])) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($item['catatan_admin'])): ?>
                                <div style="margin-top:14px; padding:12px 16px; background:#fafafa; border-left:3px solid #111; font-size:13px;">
                                    <strong style="color:#111; display:block; margin-bottom:2px;">Tanggapan Admin Desa:</strong>
                                    <span style="color:#555;"><?= nl2br(htmlspecialchars($item['catatan_admin'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

<?php
$pageTitle = 'Beranda';
require_once 'includes/db.php';

// Statistik layanan — fallback ke default jika DB belum terhubung (3 item utama)
$statsDefault = [
    ['kunci'=>'penduduk',     'label'=>'Penduduk',            'nilai'=>'3.847', 'target_angka'=>3847],
    ['kunci'=>'kk',           'label'=>'Kepala Keluarga',     'nilai'=>'1.124', 'target_angka'=>1124],
    ['kunci'=>'layanan_bulan','label'=>'Keseluruhan Layanan', 'nilai'=>'247',   'target_angka'=>247],
];
try {
    $db       = getDB();
    $stmt     = $db->query("SELECT * FROM statistik_desa WHERE kunci != 'kepuasan' ORDER BY id ASC");
    $statsDB  = $stmt->fetchAll();
    $stats    = !empty($statsDB)
        ? array_map(function($r) {
            $lbl = ($r['label'] === 'Layanan Bulan Ini') ? 'Keseluruhan Layanan' : $r['label'];
            return ['label' => $lbl, 'num' => $r['nilai'], 'target' => (int)$r['target_angka']];
          }, $statsDB)
        : array_map(fn($r) => ['label'=>$r['label'],'num'=>$r['nilai'],'target'=>$r['target_angka']], $statsDefault);
} catch (Exception $e) {
    $stats = array_map(fn($r) => ['label'=>$r['label'],'num'=>$r['nilai'],'target'=>$r['target_angka']], $statsDefault);
}

require_once 'includes/header.php';
?>

<!-- ======================================================
     HERO SLIDER — Black & White Theme (3 Slides)
     ====================================================== -->
<section id="hero" aria-label="Beranda Slider">

    <!-- Slide 1 (Initial) -->
    <div class="slide active" style="background-image:url('assets/images/bg3.png'); background-position:center 65%;" role="img" aria-label="Layanan Kesehatan Desa Sungai Bakau Kecil"></div>

    <!-- Slide 2 -->
    <div class="slide" style="background-image:url('assets/images/bg1.jpg'); background-position:center 55%;" role="img" aria-label="Konsultasi Hukum Warga Desa Sungai Bakau Kecil"></div>

    <!-- Slide 3 -->
    <div class="slide" style="background-image:url('assets/images/bg2.png'); background-position:center 40%;" role="img" aria-label="Program KKN Layanan Desa Sungai Bakau Kecil"></div>

    <!-- Main content -->
    <div class="hero-content">
        <div class="hero-inner">
            <h1 class="hero-title">
                Pusat Layanan Hukum<br>&amp; Kesehatan Warga
            </h1>
            <p class="hero-quote">
                "Akses layanan hukum dan kesehatan secara mudah, gratis, dan terpercaya — hadir untuk warga Desa Sungai Bakau Kecil."
            </p>
            <p class="hero-author">Program KKN — Desa Sungai Bakau Kecil, Kalimantan Barat</p>
        </div>
    </div>

    <!-- Slide number bottom-left -->
    <div class="slide-number" id="slide-number" aria-live="polite">
        <span class="current" id="slide-current">01</span>
        <span style="color:rgba(255,255,255,0.30); margin:0 5px;">/</span>
        <span>03</span>
    </div>

    <!-- Slide dots center-bottom -->
    <div class="slide-dots" role="tablist" aria-label="Navigasi Slide">
        <button class="dot active" role="tab" aria-selected="true"  aria-label="Slide 1" data-index="0"></button>
        <button class="dot"        role="tab" aria-selected="false" aria-label="Slide 2" data-index="1"></button>
        <button class="dot"        role="tab" aria-selected="false" aria-label="Slide 3" data-index="2"></button>
    </div>

    <!-- Arrow nav bottom-right -->
    <div class="slide-nav" aria-label="Kontrol Slider">
        <button class="slide-btn" id="btn-prev" aria-label="Slide sebelumnya">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button class="slide-btn" id="btn-next" aria-label="Slide berikutnya">
            <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>

    <!-- Progress line -->
    <div class="hero-progress-wrap">
        <div class="hero-progress-bar" id="hero-progress"></div>
    </div>
</section>


<!-- ======================================================
     ANNOUNCEMENT BAR
     ====================================================== -->
<div class="announce-bar" aria-label="Informasi Layanan">
    <div class="ticker-track" id="ticker">
        <span>Layanan konsultasi hukum tersedia setiap Senin–Jumat pukul 09.00–15.00 WIB</span>
        <span class="ticker-sep">|</span>
        <span>Posyandu Balita: 5 Agustus 2025, Pukul 09.00 WIB di Balai Desa</span>
        <span class="ticker-sep">|</span>
        <span>Pelaporan masalah kesehatan lingkungan kini tersedia secara online</span>
        <span class="ticker-sep">|</span>
        <span>Konsultasi hukum gratis untuk warga — Daftar sekarang tanpa biaya</span>
        <span class="ticker-sep">|</span>
        <!-- Duplicate for seamless loop -->
        <span>Layanan konsultasi hukum tersedia setiap Senin–Jumat pukul 09.00–15.00 WIB</span>
        <span class="ticker-sep">|</span>
        <span>Posyandu Balita: 5 Agustus 2025, Pukul 09.00 WIB di Balai Desa</span>
        <span class="ticker-sep">|</span>
        <span>Pelaporan masalah kesehatan lingkungan kini tersedia secara online</span>
        <span class="ticker-sep">|</span>
        <span>Konsultasi hukum gratis untuk warga — Daftar sekarang tanpa biaya</span>
        <span class="ticker-sep">|</span>
    </div>
</div>


<!-- ======================================================
     STATS (3 Items: Penduduk, Kepala Keluarga, Layanan Bulan Ini)
     ====================================================== -->
<section class="stats-section py-12 reveal" aria-label="Statistik Layanan" style="padding:60px 0;">
    <div class="container">
        <!-- Section Header -->
        <div style="text-align:center; margin-bottom:32px;">
            <span class="section-label" style="display:inline-block; margin-bottom:6px;">DATA &amp; STATISTIK DESA</span>
            <h2 style="font-family:'Playfair Display',serif; font-size:1.85rem; font-weight:700; color:#111111; margin:0 0 8px; line-height:1.2;">
                Sekilas Demografi &amp; Layanan Desa
            </h2>
            <p style="font-size:13.5px; color:#666666; margin:0 auto; max-width:520px; line-height:1.6;">
                Informasi kependudukan dan statistik rekapitulasi pelayanan warga Desa Sungai Bakau Kecil
            </p>
        </div>

        <div class="stats-grid-wrap" style="display:grid; grid-template-columns:repeat(3,1fr); gap:0; border:1px solid #e5e5e5; background:#ffffff;">
            <?php foreach ($stats as $i => $s): ?>
            <div class="stat-item" style="padding:28px 20px; <?= $i < count($stats) - 1 ? 'border-right:1px solid #e5e5e5;' : '' ?>">
                <span class="stat-num" data-target="<?= $s['target'] ?>"><?= htmlspecialchars($s['num']) ?></span>
                <span class="stat-label"><?= htmlspecialchars($s['label']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ======================================================
     LAYANAN UTAMA
     ====================================================== -->
<section id="layanan" aria-labelledby="layanan-heading" style="background:#ffffff; padding:72px 0;">
    <div class="container">

        <div class="reveal" style="margin-bottom:40px;">
            <span class="section-label">Pilih Layanan</span>
            <span class="divider"></span>
            <h2 class="section-title" id="layanan-heading" style="font-size:2rem;">Layanan Kami</h2>
            <p style="font-size:14px; color:#666; max-width:520px; line-height:1.7; margin:0;">
                Dua layanan utama yang kami sediakan secara gratis untuk seluruh warga Desa Sungai Bakau Kecil — mudah diakses kapan saja dan di mana saja.
            </p>
        </div>

        <div class="service-img-grid reveal">
            <!-- Card 1: Kesehatan -->
            <a href="layanan-pengaduan.php" class="service-img-card">
                <div class="service-img-bg" style="background-image:url('assets/images/service-kesehatan.png');"></div>
                <div class="service-img-overlay"></div>
                <div class="service-img-content">
                    <span class="service-img-tag">Kesehatan</span>
                    <h3 class="service-img-title">Layanan &amp; Pengaduan Kesehatan</h3>
                    <p class="service-img-desc">Laporkan masalah kesehatan, penyakit menular, atau kondisi lingkungan yang membahayakan warga. Tim kami akan menindaklanjuti setiap laporan dengan cepat.</p>
                    <span class="service-img-cta">
                        Ajukan Pengaduan
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>

            <!-- Card 2: Hukum -->
            <a href="layanan-hukum.php" class="service-img-card">
                <div class="service-img-bg" style="background-image:url('assets/images/service-hukum.png');"></div>
                <div class="service-img-overlay"></div>
                <div class="service-img-content">
                    <span class="service-img-tag">Hukum &amp; Bantuan Hukum</span>
                    <h3 class="service-img-title">Konsultasi &amp; Bantuan Hukum</h3>
                    <p class="service-img-desc">Dapatkan konsultasi hukum gratis untuk permasalahan warga, sengketa tanah, administrasi kependudukan, dan kebutuhan hukum lainnya bersama tim paralegal kami.</p>
                    <span class="service-img-cta">
                        Ajukan Konsultasi
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>
        </div>
    </div>
</section>


<!-- ======================================================
     TENTANG PROGRAM
     ====================================================== -->
<section aria-labelledby="about-heading" style="background:#fafafa; padding:72px 0; border-top:1px solid #e5e5e5;">
    <div class="container">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:56px; align-items:center;" class="reveal">

            <!-- Image -->
            <div class="about-img-wrap">
                <img src="assets/images/kantordesa.png" alt="Kantor Desa Sungai Bakau Kecil" loading="lazy" class="about-img-grayscale">
            </div>

            <!-- Text -->
            <div>
                <span class="section-label">Tentang Program</span>
                <span class="divider"></span>
                <h2 class="section-title" id="about-heading" style="font-size:1.9rem; margin-bottom:18px;">
                    Layanan Digital untuk<br>Warga Desa
                </h2>
                <p style="font-size:14px; color:#555; line-height:1.8; margin:0 0 14px;">
                    Web ini merupakan hasil program <strong>Kuliah Kerja Nyata (KKN)</strong> yang dihadirkan untuk memudahkan warga Desa Sungai Bakau Kecil dalam mengakses layanan hukum dan kesehatan secara digital.
                </p>
                <p style="font-size:14px; color:#555; line-height:1.8; margin:0 0 28px;">
                    Seluruh layanan bersifat <strong>gratis dan terbuka</strong> untuk semua warga. Laporan dan pengaduan yang masuk akan diteruskan langsung ke Kantor Desa dan Lembaga Bantuan Hukum (LBH) yang bermitra.
                </p>
                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <a href="tentang.php" class="btn-dark">
                        Selengkapnya
                    </a>
                    <a href="kontak.php" class="btn-dark-outline">
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- ======================================================
     CTA SECTION (Black Theme)
     ====================================================== -->
<section class="cta-section" style="padding:80px 0;" aria-label="Ajukan Layanan">
    <div class="container" style="position:relative; z-index:2;">
        <div style="max-width:620px; margin:0 auto; text-align:center;" class="reveal">
            <span class="section-label" style="color:rgba(255,255,255,0.60);">Butuh Bantuan?</span>
            <h2 style="font-family:'Playfair Display',serif; font-size:2rem; font-weight:700; color:#ffffff; margin:0 0 16px; line-height:1.2;">
                Ada yang perlu<br>kami bantu?
            </h2>
            <p style="font-size:14px; color:rgba(255,255,255,0.70); margin:0 0 32px; line-height:1.7;">
                Tim KKN kami siap membantu. Ajukan pengaduan kesehatan atau konsultasi hukum Anda sekarang — gratis dan mudah.
            </p>
            <div style="display:flex; justify-content:center; gap:14px; flex-wrap:wrap;">
                <a href="layanan-pengaduan.php" class="btn-primary">Pengaduan Kesehatan</a>
                <a href="layanan-hukum.php"     class="btn-outline">Bantuan Hukum</a>
            </div>
        </div>
    </div>
</section>


<?php require_once 'includes/footer.php'; ?>

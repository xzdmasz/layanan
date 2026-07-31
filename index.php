<?php $pageTitle = 'Beranda'; require_once 'includes/header.php'; ?>

<!-- ======================================================
     HERO SLIDER — Black & White Theme (3 Slides)
     ====================================================== -->
<section id="hero" aria-label="Beranda Slider">

    <!-- Slide 1 (Initial) -->
    <div class="slide active" style="background-image:url('assets/images/bg3.png'); background-position:center 65%;" role="img" aria-label="Pemandangan Desa Sungai Bakau Kecil"></div>

    <!-- Slide 2 -->
    <div class="slide" style="background-image:url('assets/images/bg1.jpg'); background-position:center 55%;" role="img" aria-label="Kawasan Mangrove Sungai Bakau Kecil saat senja"></div>

    <!-- Slide 3 -->
    <div class="slide" style="background-image:url('assets/images/bg2.png'); background-position:center 40%;" role="img" aria-label="Hutan Bakau Sungai Bakau Kecil golden hour"></div>

    <!-- Main content -->
    <div class="hero-content">
        <div class="hero-inner">
            <div class="hero-text-wrap" id="hero-text-wrap">
                <h1 class="hero-title">
                    <span class="hero-word">Sistem</span>
                    <span class="hero-word">Pusat</span>
                    <span class="hero-word">Layanan</span><br>
                    <span class="hero-word">Desa</span>
                    <span class="hero-word">Sungai</span>
                    <span class="hero-word">Bakau</span>
                    <span class="hero-word">Kecil</span>
                </h1>
                <p class="hero-quote hero-line">"Ajukan pengaduan, cek informasi, dan akses layanan desa dengan mudah melalui satu genggaman tangan."</p>
                <p class="hero-author hero-line">Desa Sungai Bakau Kecil — Kalimantan Barat</p>
            </div>
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
     ANNOUNCEMENT BAR (Solid Black)
     ====================================================== -->
<div class="announce-bar" aria-label="Pengumuman Desa">
    <div class="ticker-track" id="ticker">
        <span>Kantor Desa buka Senin–Jumat pukul 08.00–16.00 WIB</span>
        <span class="ticker-sep">|</span>
        <span>Posyandu Balita: 5 Agustus 2025, Pukul 09.00 WIB di Balai Desa</span>
        <span class="ticker-sep">|</span>
        <span>Program air bersih: Pendaftaran dibuka hingga 31 Juli 2025</span>
        <span class="ticker-sep">|</span>
        <span>Layanan Surat Keterangan kini tersedia secara online</span>
        <span class="ticker-sep">|</span>
        <!-- Duplicate for seamless loop -->
        <span>Kantor Desa buka Senin–Jumat pukul 08.00–16.00 WIB</span>
        <span class="ticker-sep">|</span>
        <span>Posyandu Balita: 5 Agustus 2025, Pukul 09.00 WIB di Balai Desa</span>
        <span class="ticker-sep">|</span>
        <span>Program air bersih: Pendaftaran dibuka hingga 31 Juli 2025</span>
        <span class="ticker-sep">|</span>
        <span>Layanan Surat Keterangan kini tersedia secara online</span>
        <span class="ticker-sep">|</span>
    </div>
</div>


<!-- ======================================================
     STATS
     ====================================================== -->
<section class="stats-section py-12 reveal" aria-label="Statistik Desa">
    <div class="container">
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:0; border:1px solid #e5e5e5;">
            <?php
            $stats = [
                ['num' => '3.847', 'label' => 'Penduduk',         'target' => 3847],
                ['num' => '1.124', 'label' => 'Kepala Keluarga',  'target' => 1124],
                ['num' => '247',   'label' => 'Layanan Bulan Ini','target' => 247],
                ['num' => '98%',   'label' => 'Kepuasan Warga',   'target' => 98],
            ];
            foreach ($stats as $i => $s): ?>
            <div class="stat-item" style="padding:28px 20px; <?= $i < 3 ? 'border-right:1px solid #e5e5e5;' : '' ?>">
                <span class="stat-num" data-target="<?= $s['target'] ?>"><?= $s['num'] ?></span>
                <span class="stat-label"><?= $s['label'] ?></span>
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
            <span class="section-label">Apa yang bisa kami bantu?</span>
            <span class="divider"></span>
            <h2 class="section-title" id="layanan-heading" style="font-size:2rem;">Layanan Desa</h2>
            <p style="font-size:14px; color:#666; max-width:500px; line-height:1.7; margin:0;">
                Akses berbagai layanan desa secara mudah, cepat, dan transparan — kapan saja dan di mana saja.
            </p>
        </div>

        <div class="service-img-grid reveal">
            <!-- Card 1: Kesehatan -->
            <a href="layanan-pengaduan.php" class="service-img-card">
                <div class="service-img-bg" style="background-image:url('assets/images/service-kesehatan.png');"></div>
                <div class="service-img-overlay"></div>
                <div class="service-img-content">
                    <span class="service-img-tag">Kesehatan</span>
                    <h3 class="service-img-title">Layanan & Pengaduan Penyakit</h3>
                    <p class="service-img-desc">Laporkan masalah kesehatan, penyakit menular, atau kondisi lingkungan yang membahayakan warga di lingkungan desa.</p>
                    <span class="service-img-cta">
                        Lihat Layanan
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>

            <!-- Card 2: Hukum -->
            <a href="layanan-hukum.php" class="service-img-card">
                <div class="service-img-bg" style="background-image:url('assets/images/service-hukum.png');"></div>
                <div class="service-img-overlay"></div>
                <div class="service-img-content">
                    <span class="service-img-tag">Hukum & Administrasi</span>
                    <h3 class="service-img-title">Layanan Hukum</h3>
                    <p class="service-img-desc">Dapatkan bantuan dan konsultasi hukum untuk permasalahan warga, sengketa tanah, administrasi kependudukan, dan kebutuhan hukum lainnya.</p>
                    <span class="service-img-cta">
                        Lihat Layanan
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>
        </div>
    </div>
</section>


<!-- ======================================================
     TENTANG DESA
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
                <span class="section-label">Tentang Desa</span>
                <span class="divider"></span>
                <h2 class="section-title" id="about-heading" style="font-size:1.9rem; margin-bottom:18px;">
                    Desa dengan Kawasan<br>Mangrove yang Kaya
                </h2>
                <p style="font-size:14px; color:#555; line-height:1.8; margin:0 0 14px;">
                    Desa Sungai Bakau Kecil terletak di Kabupaten Mempawah, Kalimantan Barat. Dikenal dengan kawasan hutan mangrove yang luas, ekosistem pesisir yang kaya, dan masyarakat yang guyub.
                </p>
                <p style="font-size:14px; color:#555; line-height:1.8; margin:0 0 28px;">
                    Melalui sistem pusat layanan digital ini, kami berkomitmen memberikan pelayanan publik yang mudah, transparan, dan akuntabel kepada seluruh warga.
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
<section class="cta-section" style="padding:80px 0;" aria-label="Hubungi Kami">
    <div class="container" style="position:relative; z-index:2;">
        <div style="max-width:620px; margin:0 auto; text-align:center;" class="reveal">
            <span class="section-label" style="color:rgba(255,255,255,0.60);">Butuh Bantuan?</span>
            <h2 style="font-family:'Playfair Display',serif; font-size:2rem; font-weight:700; color:#ffffff; margin:0 0 16px; line-height:1.2;">
                Ada yang perlu<br>kami bantu?
            </h2>
            <p style="font-size:14px; color:rgba(255,255,255,0.70); margin:0 0 32px; line-height:1.7;">
                Tim pelayanan desa siap membantu Anda. Sampaikan pengaduan atau pertanyaan Anda sekarang juga.
            </p>
            <div style="display:flex; justify-content:center; gap:14px; flex-wrap:wrap;">
                <a href="layanan-pengaduan.php" class="btn-primary">Buat Pengaduan</a>
                <a href="tel:05611234567"        class="btn-outline">Hubungi Kantor Desa</a>
            </div>
        </div>
    </div>
</section>


<?php require_once 'includes/footer.php'; ?>

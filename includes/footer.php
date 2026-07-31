<?php /* Footer — Desa Sungai Bakau Kecil */ ?>

<!-- ===== FOOTER ===== -->
<footer class="site-footer" role="contentinfo">
    <div class="container" style="padding-top:56px; padding-bottom:56px;">
        <div style="display:grid; grid-template-columns:1.6fr 1fr 1fr 1.4fr; gap:40px;">

            <!-- Brand -->
            <div>
                <h2 class="footer-brand-name">Desa Sungai Bakau Kecil</h2>
                <p style="font-size:11px; color:rgba(255,255,255,0.40); letter-spacing:0.12em; text-transform:uppercase; margin:0 0 14px;">Kab. Mempawah, Kalimantan Barat</p>
                <p style="font-size:13px; color:rgba(255,255,255,0.55); line-height:1.75; margin:0 0 20px; max-width:240px;">
                    Melayani masyarakat dengan sepenuh hati melalui sistem layanan digital yang mudah, cepat, dan transparan.
                </p>
                <div class="footer-social" style="display:flex; gap:6px;">
                    <a href="#" aria-label="Facebook">
                        <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a href="#" aria-label="YouTube">
                        <svg viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 001.46 6.42 29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.95C5.12 20 12 20 12 20s6.88 0 8.59-.47a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#111" stroke="none"/></svg>
                    </a>
                </div>
            </div>

            <!-- Layanan -->
            <div>
                <h3 class="footer-heading">Layanan</h3>
                <a href="layanan-pengaduan.php" class="footer-link">Pengaduan Penyakit</a>
                <a href="layanan-hukum.php"     class="footer-link">Layanan Hukum</a>
            </div>

            <!-- Navigasi -->
            <div>
                <h3 class="footer-heading">Navigasi</h3>
                <a href="index.php"              class="footer-link">Beranda</a>
                <a href="#layanan"               class="footer-link">Layanan Utama</a>
                <a href="kontak.php"             class="footer-link">Kontak</a>
            </div>

            <!-- Kontak -->
            <div>
                <h3 class="footer-heading">Kontak Desa</h3>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span>Desa Sungai Bakau Kecil, Kab. Mempawah, Kalimantan Barat</span>
                </div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8a19.79 19.79 0 01-3.07-8.63A2 2 0 012 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.91 9a16 16 0 006.08 6.08l.79-.79a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                    </svg>
                    <span>(0561) 123-4567</span>
                </div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <span>desa@sungaibakaukecil.id</span>
                </div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>Senin–Jumat, 08.00–16.00 WIB</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Bottom bar -->
    <div class="footer-bottom" style="padding:14px 0;">
        <div class="container" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
            <p style="margin:0;">&copy; <?= date('Y') ?> Desa Sungai Bakau Kecil. Seluruh hak dilindungi.</p>
            <p style="margin:0;">Sistem Pusat Layanan Digital Desa</p>
        </div>
    </div>
</footer>

<!-- ===== MODAL KONFIRMASI LOGOUT ===== -->
<div class="logout-modal-backdrop" id="logoutModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="logoutModalTitle">
    <div class="logout-modal-card">
        <div class="logout-modal-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </div>
        <h3 class="logout-modal-title" id="logoutModalTitle">Konfirmasi Keluar</h3>
        <p class="logout-modal-desc">Apakah Anda yakin ingin keluar dari akun Anda?</p>
        <div class="logout-modal-actions">
            <button type="button" class="logout-btn-cancel" id="btnCancelLogout">Batal</button>
            <a href="logout.php" class="logout-btn-confirm" id="btnConfirmLogout">Ya, Keluar</a>
        </div>
    </div>
</div>

<!-- ===== BACK TO TOP BUTTON ===== -->
<button id="back-to-top" aria-label="Kembali ke atas" title="Kembali ke atas">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="18 15 12 9 6 15"/>
    </svg>
</button>

<!-- ===== JAVASCRIPT ===== -->
<script src="assets/js/main.js"></script>

<script>
/* Back to top */
const btn = document.getElementById('back-to-top');
window.addEventListener('scroll', () => {
    btn.classList.toggle('visible', window.scrollY > 400);
});
btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>

<script>
/* ── PARALLAX HERO + NAVBAR SCROLL EFFECT ── */
(function () {
    const navbar  = document.getElementById('navbar');
    const hero    = document.getElementById('hero');
    const slides  = document.querySelectorAll('#hero .slide');

    // Parallax strength: 0 = no move, 0.5 = half speed, 1 = same speed
    const PARALLAX_STRENGTH = 0.38;

    let ticking = false;

    function onScroll() {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(update);
    }

    function update() {
        const scrollY = window.scrollY;
        const heroH   = hero ? hero.offsetHeight : window.innerHeight;

        /* ── NAVBAR: transparent on top, solid dark when scrolled ── */
        if (navbar) {
            if (scrollY > 60) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        /* ── PARALLAX on hero slides ── */
        if (scrollY < heroH) {
            const shift = scrollY * PARALLAX_STRENGTH;
            slides.forEach(slide => {
                slide.style.transform = `translateY(${shift}px)`;
            });
        }

        ticking = false;
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    update(); // run once on load
})();
</script>
</body>
</html>

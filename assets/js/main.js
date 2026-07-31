/* =============================================
   DESA SUNGAI BAKAU KECIL — Main JavaScript
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // ─────────────────────────────────────────────
    // 1. HERO SLIDER LOGIC
    // ─────────────────────────────────────────────
    const slides       = document.querySelectorAll('.slide');
    const dots         = document.querySelectorAll('.dot');
    const slideCurrent = document.getElementById('slide-current');
    const btnPrev      = document.getElementById('btn-prev');
    const btnNext      = document.getElementById('btn-next');
    const progressBar  = document.getElementById('hero-progress');
    const heroEl       = document.getElementById('hero');

    let currentSlide   = 0;
    let autoplayTimer  = null;
    const AUTOPLAY_MS  = 6000;

    function goToSlide(index) {
        if (slides.length === 0) return;

        // Remove active class from current slide & dot
        slides[currentSlide].classList.remove('active');
        if (dots[currentSlide]) {
            dots[currentSlide].classList.remove('active');
            dots[currentSlide].setAttribute('aria-selected', 'false');
        }

        // Calculate next slide index
        currentSlide = (index + slides.length) % slides.length;

        // Activate new slide & dot
        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) {
            dots[currentSlide].classList.add('active');
            dots[currentSlide].setAttribute('aria-selected', 'true');
        }

        // Update current slide number display
        if (slideCurrent) {
            slideCurrent.textContent = String(currentSlide + 1).padStart(2, '0');
        }

        // Reset and trigger progress bar animation
        if (progressBar) {
            progressBar.classList.remove('animating');
            void progressBar.offsetWidth; // Force reflow
            progressBar.classList.add('animating');
        }
    }

    function nextSlide() { goToSlide(currentSlide + 1); }
    function prevSlide() { goToSlide(currentSlide - 1); }

    function startAutoplay() {
        stopAutoplay();
        autoplayTimer = setInterval(nextSlide, AUTOPLAY_MS);
    }

    function stopAutoplay() {
        if (autoplayTimer) clearInterval(autoplayTimer);
    }

    // Button event listeners
    if (btnNext) btnNext.addEventListener('click', () => { nextSlide(); startAutoplay(); });
    if (btnPrev) btnPrev.addEventListener('click', () => { prevSlide(); startAutoplay(); });

    // Dot event listeners
    dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => {
            goToSlide(idx);
            startAutoplay();
        });
    });

    // Touch / Swipe support
    let touchStartX = 0;
    if (heroEl) {
        heroEl.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].clientX; }, { passive: true });
        heroEl.addEventListener('touchend', e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) {
                diff > 0 ? nextSlide() : prevSlide();
                startAutoplay();
            }
        }, { passive: true });
    }

    // Initialize slider
    if (slides.length > 0) {
        goToSlide(0);
        startAutoplay();
    }


    // ─────────────────────────────────────────────
    // 2. NAVBAR SCROLL EFFECT
    // ─────────────────────────────────────────────
    const navbar = document.getElementById('navbar');
    function updateNavbar() {
        if (window.scrollY > 40) {
            navbar?.classList.add('scrolled');
        } else {
            navbar?.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', updateNavbar, { passive: true });
    updateNavbar();


    // ─────────────────────────────────────────────
    // 3. MOBILE MENU TOGGLE
    // ─────────────────────────────────────────────
    const hamburger        = document.getElementById('hamburger');
    const mobileMenu       = document.getElementById('mobile-menu');
    const mobileLayananBtn = document.getElementById('mobile-layanan-btn');
    const mobileLayananSub = document.getElementById('mobile-layanan-sub');
    const mobileChevron    = document.getElementById('mobile-layanan-chevron');

    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', function () {
            const isOpen = mobileMenu.classList.toggle('open');
            hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            mobileMenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        });
    }

    if (mobileLayananBtn && mobileLayananSub) {
        mobileLayananBtn.addEventListener('click', function () {
            const isOpen = mobileLayananSub.classList.toggle('open');
            mobileLayananBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            mobileLayananSub.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            if (mobileChevron) {
                mobileChevron.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        });
    }


    // ─────────────────────────────────────────────
    // 4. ANIMATED COUNTER FOR STATS
    // ─────────────────────────────────────────────
    function animateCounter(el) {
        const target   = parseInt(el.dataset.target, 10);
        if (isNaN(target)) return;
        const duration = 1800;
        const steps    = 60;
        const stepTime = duration / steps;
        const stepVal  = target / steps;
        let current    = 0;

        const isPercent = el.textContent.includes('%');

        const timer = setInterval(() => {
            current += stepVal;
            if (current >= target) {
                el.textContent = target.toLocaleString('id-ID') + (isPercent ? '%' : '');
                clearInterval(timer);
            } else {
                el.textContent = Math.floor(current).toLocaleString('id-ID') + (isPercent ? '%' : '');
            }
        }, stepTime);
    }

    const counterEls = document.querySelectorAll('[data-target]');
    if (counterEls.length > 0) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.dataset.done) {
                    entry.target.dataset.done = 'true';
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        counterEls.forEach(el => counterObserver.observe(el));
    }


    // ─────────────────────────────────────────────
    // 5. SCROLL REVEAL ANIMATION
    // ─────────────────────────────────────────────
    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length > 0) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        revealEls.forEach(el => revealObserver.observe(el));
    }


    // ─────────────────────────────────────────────
    // 6. SIDEBAR ACCORDION TOGGLE
    // ─────────────────────────────────────────────
    document.querySelectorAll('.accordion-header').forEach(btn => {
        btn.addEventListener('click', function () {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            this.classList.toggle('active');

            const body = this.nextElementSibling;
            if (body && body.classList.contains('accordion-body')) {
                body.classList.toggle('open');
            }
        });
    });

    // ─────────────────────────────────────────────
    // 7. PROFILE DROPDOWN TOGGLE
    // ─────────────────────────────────────────────
    const profileBtn      = document.getElementById('profile-icon-btn');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = profileDropdown.classList.contains('open');
            profileDropdown.classList.toggle('open', !isOpen);
            profileBtn.setAttribute('aria-expanded', String(!isOpen));
        });

        // Close when clicking outside
        document.addEventListener('click', function (e) {
            if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('open');
                profileBtn.setAttribute('aria-expanded', 'false');
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                profileDropdown.classList.remove('open');
                profileBtn.setAttribute('aria-expanded', 'false');
                profileBtn.focus();
            }
        });
    }

    // ─────────────────────────────────────────────
    // 8. FLOATING SPEED DIAL (LAYANAN MOBILE)
    // ─────────────────────────────────────────────
    const btnLayanan   = document.getElementById('btn-bottom-layanan');
    const speedDial    = document.getElementById('fab-speed-dial');
    const dialBackdrop = document.getElementById('speed-dial-backdrop');
    const fabCircle    = btnLayanan ? btnLayanan.querySelector('.fab-circle') : null;

    if (btnLayanan && speedDial) {
        function openDial() {
            speedDial.classList.add('open');
            speedDial.setAttribute('aria-hidden', 'false');
            if (dialBackdrop) dialBackdrop.classList.add('open');
            if (fabCircle) fabCircle.classList.add('is-open');
        }

        function closeDial() {
            speedDial.classList.remove('open');
            speedDial.setAttribute('aria-hidden', 'true');
            if (dialBackdrop) dialBackdrop.classList.remove('open');
            if (fabCircle) fabCircle.classList.remove('is-open');
        }

        btnLayanan.addEventListener('click', function (e) {
            e.stopPropagation();
            speedDial.classList.contains('open') ? closeDial() : openDial();
        });

        // Close on backdrop click
        if (dialBackdrop) {
            dialBackdrop.addEventListener('click', closeDial);
        }

        // Close on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && speedDial.classList.contains('open')) {
                closeDial();
                btnLayanan.focus();
            }
        });
    }


    // ─────────────────────────────────────────────
    // 9. LOGOUT CONFIRMATION MODAL
    // ─────────────────────────────────────────────
    const logoutModal     = document.getElementById('logoutModal');
    const btnCancelLogout = document.getElementById('btnCancelLogout');

    function showLogoutModal(targetUrl) {
        if (!logoutModal) return;
        const confirmBtn = document.getElementById('btnConfirmLogout');
        if (confirmBtn && targetUrl) {
            confirmBtn.href = targetUrl;
        }
        logoutModal.classList.add('open');
        logoutModal.setAttribute('aria-hidden', 'false');
    }

    function closeLogoutModal() {
        if (!logoutModal) return;
        logoutModal.classList.remove('open');
        logoutModal.setAttribute('aria-hidden', 'true');
    }

    // Intercept all logout links
    document.addEventListener('click', function (e) {
        const logoutLink = e.target.closest('a[href*="logout.php"], .profile-dropdown-logout');
        if (logoutLink) {
            e.preventDefault();
            const href = logoutLink.getAttribute('href') || 'logout.php';
            showLogoutModal(href);
        }
    });

    if (btnCancelLogout) {
        btnCancelLogout.addEventListener('click', closeLogoutModal);
    }

    if (logoutModal) {
        logoutModal.addEventListener('click', function (e) {
            if (e.target === logoutModal) {
                closeLogoutModal();
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && logoutModal && logoutModal.classList.contains('open')) {
            closeLogoutModal();
        }
    });

});


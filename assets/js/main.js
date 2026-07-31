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

    let currentSlide   = 0;
    let autoplayTimer  = null;
    const AUTOPLAY_MS  = 6000;

    const heroTextWrap = document.getElementById('hero-text-wrap');

    function triggerHeroTextIn() {
        if (!heroTextWrap) return;
        heroTextWrap.classList.remove('exit', 'animating');
        // Force reflow so animation restarts cleanly
        void heroTextWrap.offsetWidth;
        heroTextWrap.classList.add('animating');
    }

    function goToSlide(index) {
        if (slides.length === 0) return;

        // 1. Play exit animation on current text
        if (heroTextWrap) {
            heroTextWrap.classList.remove('animating');
            heroTextWrap.classList.add('exit');
        }

        // 2. After exit completes (350ms), switch slide and animate text back in
        setTimeout(function () {
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

            // Update slide number display
            if (slideCurrent) {
                slideCurrent.textContent = String(currentSlide + 1).padStart(2, '0');
            }

            // Reset and trigger progress bar animation
            if (progressBar) {
                progressBar.classList.remove('animating');
                void progressBar.offsetWidth;
                progressBar.classList.add('animating');
            }

            // 3. Play entrance animation
            triggerHeroTextIn();

        }, 350);
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
    const heroEl = document.getElementById('hero');
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
        // Direct init: activate slide 0 without exit animation
        slides[0].classList.add('active');
        if (dots[0]) { dots[0].classList.add('active'); dots[0].setAttribute('aria-selected','true'); }
        if (slideCurrent) slideCurrent.textContent = '01';
        if (progressBar) {
            void progressBar.offsetWidth;
            progressBar.classList.add('animating');
        }
        // Trigger hero text entrance after a short delay
        setTimeout(triggerHeroTextIn, 300);
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
    updateNavbar(); // Check initial scroll position


    // ─────────────────────────────────────────────
    // 3. MOBILE MENU TOGGLE
    // ─────────────────────────────────────────────
    const hamburger       = document.getElementById('hamburger');
    const mobileMenu      = document.getElementById('mobile-menu');
    const mobileLayananBtn= document.getElementById('mobile-layanan-btn');
    const mobileLayananSub= document.getElementById('mobile-layanan-sub');
    const mobileChevron   = document.getElementById('mobile-layanan-chevron');

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
    // 7. GARAGE DOOR SCROLL LIFT EFFECT ON HERO
    // ─────────────────────────────────────────────
    const heroEl = document.getElementById('hero');
    const heroContentEl = document.querySelector('.hero-content');

    if (heroEl) {
        let isTicking = false;

        function updateGarageDoorEffect() {
            const scrollY = window.scrollY;
            const heroHeight = heroEl.offsetHeight || window.innerHeight;

            if (scrollY <= heroHeight * 1.2) {
                const progress = Math.min(scrollY / heroHeight, 1);
                
                // Lift hero upwards like a garage door opening into top ceiling
                const translateY = progress * 60; // Lifts up to 60vh
                const scale = 1 - (progress * 0.05); // Subtle 3D depth shrink
                const borderRadius = progress * 36; // Curving bottom edge like rolling shutter
                const contentOffset = progress * 100;
                const contentOpacity = Math.max(1 - (progress * 1.4), 0);

                heroEl.style.transform = `translate3d(0, -${translateY}vh, 0) scale(${scale})`;
                heroEl.style.borderRadius = `0 0 ${borderRadius}px ${borderRadius}px`;

                if (heroContentEl) {
                    heroContentEl.style.transform = `translate3d(0, -${contentOffset}px, 0)`;
                    heroContentEl.style.opacity = contentOpacity;
                }
            } else {
                heroEl.style.transform = `translate3d(0, -60vh, 0) scale(0.95)`;
                heroEl.style.borderRadius = `0 0 36px 36px`;
            }
        }

        window.addEventListener('scroll', function () {
            if (!isTicking) {
                window.requestAnimationFrame(function () {
                    updateGarageDoorEffect();
                    isTicking = false;
                });
                isTicking = true;
            }
        }, { passive: true });

        // Initial run
        updateGarageDoorEffect();
    }

});


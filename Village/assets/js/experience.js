(function () {
    'use strict';

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function getHeaderOffset() {
        const header = document.querySelector('[data-site-header]');
        return header ? header.offsetHeight + 16 : 80;
    }

    function scrollToElement(el) {
        if (!el) return;
        const top = el.getBoundingClientRect().top + window.scrollY - getHeaderOffset();
        window.scrollTo({
            top: Math.max(0, top),
            behavior: prefersReduced ? 'auto' : 'smooth',
        });
    }

    function initReveal() {
        const els = document.querySelectorAll('.xp-reveal');
        if (!els.length) return;

        if (prefersReduced) {
            els.forEach((el) => el.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { rootMargin: '0px 0px -8% 0px', threshold: 0.1 }
        );

        els.forEach((el) => observer.observe(el));

        els.forEach((el, i) => {
            if (i < 4) {
                setTimeout(() => el.classList.add('is-visible'), 100 + i * 80);
            }
        });
    }

    function initTilt() {
        if (prefersReduced) return;

        document.querySelectorAll('[data-tilt]').forEach((wrap) => {
            const stage = wrap.querySelector('.xp-3d-stage');
            if (!stage) return;

            wrap.addEventListener('mousemove', (e) => {
                const rect = wrap.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;
                stage.style.transform = `translateY(${Math.sin(Date.now() / 1000) * 6}px) rotateY(${x * 12}deg) rotateX(${-y * 8}deg)`;
            });

            wrap.addEventListener('mouseleave', () => {
                stage.style.transform = '';
            });
        });
    }

    function initSmoothAnchor() {
        document.querySelectorAll('a[href^="#"]').forEach((a) => {
            a.addEventListener('click', (e) => {
                const id = a.getAttribute('href');
                if (!id || id === '#') return;
                const target = document.querySelector(id);
                if (!target) return;
                e.preventDefault();
                scrollToElement(target);
                if (history.replaceState) {
                    history.replaceState(null, '', id);
                }
            });
        });
    }

    function initMenuNavHighlight() {
        const nav = document.querySelector('[data-menu-nav]');
        if (!nav) return;

        const links = nav.querySelectorAll('a[href^="#"]');
        const sections = [];
        links.forEach((link) => {
            const id = link.getAttribute('href');
            const el = id ? document.querySelector(id) : null;
            if (el) sections.push({ link, el });
        });

        if (!sections.length) return;

        const onScroll = () => {
            const scrollY = window.scrollY + getHeaderOffset() + 40;
            let current = sections[0];
            sections.forEach((s) => {
                if (s.el.offsetTop <= scrollY) current = s;
            });
            links.forEach((l) => l.classList.remove('menu-category-pill--active', 'xp-filter-pill--active'));
            current.link.classList.add('menu-category-pill--active', 'xp-filter-pill--active');
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    function initCinemaParallax() {
        const mediaCol = document.querySelector('.gallery-page [data-cinema-stage]');
        const stage = mediaCol && mediaCol.closest('.page-hero-media--gallery');
        const hero = document.querySelector('.gallery-page .page-hero');
        if (!stage || !hero || prefersReduced) return;

        let ticking = false;

        const update = () => {
            const rect = hero.getBoundingClientRect();
            const heroH = rect.height || 1;
            const progress = Math.min(1, Math.max(0, -rect.top / heroH));
            const y = progress * 28;
            const scale = 1 - progress * 0.03;
            stage.style.transform = `translate3d(0, ${y}px, 0) scale(${scale})`;
            ticking = false;
        };

        window.addEventListener(
            'scroll',
            () => {
                if (!ticking) {
                    ticking = true;
                    requestAnimationFrame(update);
                }
            },
            { passive: true }
        );
        update();
    }

    document.addEventListener('DOMContentLoaded', () => {
        initReveal();
        initTilt();
        initSmoothAnchor();
        initMenuNavHighlight();
        initCinemaParallax();
    });
})();

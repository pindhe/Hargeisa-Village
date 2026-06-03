(function () {
    'use strict';

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function getHeaderOffset() {
        const header = document.querySelector('[data-site-header]');
        return header ? header.offsetHeight + 16 : 80;
    }

    function scrollToElement(el, options) {
        if (!el) return;
        const focusField = options && options.focusField;
        const highlight = options && options.highlight;

        const top = el.getBoundingClientRect().top + window.scrollY - getHeaderOffset();
        window.scrollTo({
            top: Math.max(0, top),
            behavior: prefersReduced ? 'auto' : 'smooth',
        });

        if (highlight) {
            el.classList.add('contact-form--highlight');
            setTimeout(() => el.classList.remove('contact-form--highlight'), 1600);
        }

        if (focusField) {
            const runFocus = () => {
                const field = document.getElementById(focusField);
                if (field && typeof field.focus === 'function') {
                    field.focus({ preventScroll: true });
                }
            };
            if (prefersReduced) {
                runFocus();
            } else {
                setTimeout(runFocus, 450);
            }
        }
    }

    function initReveal() {
        const els = document.querySelectorAll('.contact-reveal');
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
    }

    function initTilt() {
        if (prefersReduced) return;

        document.querySelectorAll('[data-tilt]').forEach((wrap) => {
            const stage = wrap.querySelector('.contact-3d-stage');
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

        document.querySelectorAll('[data-tilt-subtle]').forEach((el) => {
            el.addEventListener('mousemove', (e) => {
                const rect = el.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width - 0.5) * 4;
                const y = ((e.clientY - rect.top) / rect.height - 0.5) * -4;
                el.style.transform = `perspective(1000px) rotateY(${x}deg) rotateX(${y}deg)`;
            });
            el.addEventListener('mouseleave', () => {
                el.style.transform = '';
            });
        });
    }

    function initForm() {
        const form = document.querySelector('[data-contact-form]');
        const btn = document.querySelector('[data-submit-btn]');
        if (!form || !btn) return;

        form.addEventListener('submit', () => {
            btn.classList.add('is-loading');
            btn.querySelector('.contact-submit-text').textContent = 'Sending…';
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
                const focusForm = id === '#contact-form' || a.hasAttribute('data-scroll-to-form');
                scrollToElement(target, {
                    focusField: focusForm ? 'full_name' : null,
                    highlight: focusForm,
                });
                if (history.replaceState) {
                    history.replaceState(null, '', id);
                }
            });
        });
    }

    function initScrollOnLoad() {
        const formSection = document.getElementById('contact-form');
        if (!formSection) return;

        const shouldScroll =
            formSection.hasAttribute('data-scroll-on-load') ||
            window.location.hash === '#contact-form';

        if (!shouldScroll) return;

        const run = () => {
            scrollToElement(formSection, {
                focusField: document.getElementById('full_name') ? 'full_name' : null,
                highlight: true,
            });
        };

        if (document.readyState === 'complete') {
            setTimeout(run, 80);
        } else {
            window.addEventListener('load', () => setTimeout(run, 80));
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initReveal();
        initTilt();
        initForm();
        initSmoothAnchor();
        initScrollOnLoad();
        document.querySelectorAll('.contact-reveal').forEach((el, i) => {
            if (i < 3) setTimeout(() => el.classList.add('is-visible'), 100 + i * 80);
        });
    });
})();

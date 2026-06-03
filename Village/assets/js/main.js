document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            const open = mobileMenu.classList.toggle('hidden');
            menuBtn.setAttribute('aria-expanded', open ? 'false' : 'true');
        });
    }

    initLightbox();
    initSiteMobileMenus();
    initNavDropdowns();
});

function initNavDropdowns() {
    document.querySelectorAll('[data-nav-dropdown]').forEach((wrap) => {
        const trigger = wrap.querySelector('.site-nav-dropdown-trigger');
        const menu = wrap.querySelector('.site-nav-dropdown-menu');
        if (!trigger || !menu) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const open = wrap.classList.toggle('is-open');
            trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        document.addEventListener('click', (e) => {
            if (!wrap.contains(e.target)) {
                wrap.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });

        trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                wrap.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    });

    document.querySelectorAll('[data-mobile-dropdown]').forEach((wrap) => {
        const trigger = wrap.querySelector('.site-mobile-dropdown-trigger');
        const panel = wrap.querySelector('.site-mobile-dropdown-panel');
        if (!trigger || !panel) return;

        trigger.addEventListener('click', () => {
            const hidden = panel.classList.toggle('hidden');
            wrap.classList.toggle('is-open', !hidden);
            trigger.setAttribute('aria-expanded', hidden ? 'false' : 'true');
        });
    });
}

function initSiteMobileMenus() {
    ['hero-mobile-menu-btn', 'site-mobile-menu-btn'].forEach((btnId) => {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        const menuId = btn.getAttribute('aria-controls');
        const menu = menuId ? document.getElementById(menuId) : null;
        if (!menu) return;
        btn.addEventListener('click', () => {
            const hidden = menu.classList.toggle('hidden');
            btn.setAttribute('aria-expanded', hidden ? 'false' : 'true');
        });
    });
}

function initLightbox() {
    const lightbox = document.getElementById('lightbox');
    if (!lightbox) return;
    const img = lightbox.querySelector('[data-lightbox-img]');
    const items = document.querySelectorAll('[data-gallery-src]');
    let current = 0;

    const show = (index) => {
        current = index;
        const el = items[current];
        if (!el || !img) return;
        img.src = el.dataset.gallerySrc;
        img.alt = el.dataset.galleryAlt || '';
        lightbox.classList.add('active');
        document.body.classList.add('lightbox-open');
    };

    const close = () => {
        lightbox.classList.remove('active');
        document.body.classList.remove('lightbox-open');
    };

    items.forEach((el, idx) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            show(idx);
        });
    });

    lightbox.querySelector('[data-lightbox-close]')?.addEventListener('click', close);
    lightbox.querySelector('[data-lightbox-prev]')?.addEventListener('click', () => show((current - 1 + items.length) % items.length));
    lightbox.querySelector('[data-lightbox-next]')?.addEventListener('click', () => show((current + 1) % items.length));
    lightbox.addEventListener('click', (e) => { if (e.target === lightbox) close(); });
    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('active')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') show((current - 1 + items.length) % items.length);
        if (e.key === 'ArrowRight') show((current + 1) % items.length);
    });
}

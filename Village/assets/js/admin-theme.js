(function () {
    'use strict';

    const STORAGE_KEY = 'hv-theme';
    const root = document.documentElement;
    const TRANSITION_MS = 380;

    function getStored() {
        try {
            return localStorage.getItem(STORAGE_KEY);
        } catch (_) {
            return null;
        }
    }

    function setStored(theme) {
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (_) { /* ignore */ }
    }

    function isDark() {
        return root.classList.contains('dark');
    }

    function updateMeta(dark) {
        let meta = document.querySelector('meta[name="theme-color"]');
        if (!meta) {
            meta = document.createElement('meta');
            meta.setAttribute('name', 'theme-color');
            document.head.appendChild(meta);
        }
        meta.setAttribute('content', dark ? '#09090b' : '#f5f5f4');
    }

    function flashTransition() {
        root.classList.add('admin-theme-transition');
        window.setTimeout(() => root.classList.remove('admin-theme-transition'), TRANSITION_MS);
    }

    function syncToggles(dark) {
        document.querySelectorAll('.admin-theme-toggle, .site-theme-toggle').forEach((btn) => {
            btn.classList.toggle('admin-theme-toggle--dark', dark);
            btn.classList.toggle('site-theme-toggle--dark', dark);
            btn.setAttribute('aria-checked', dark ? 'true' : 'false');
            btn.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
            btn.setAttribute('title', dark ? 'Light mode' : 'Dark mode');
            const label = btn.querySelector('.admin-theme-toggle-text, .site-theme-toggle-text');
            if (label) {
                label.textContent = dark ? 'Light' : 'Dark';
            }
        });
    }

    function apply(theme, animate) {
        const dark = theme === 'dark';
        root.classList.toggle('dark', dark);
        root.style.colorScheme = dark ? 'dark' : 'light';
        setStored(theme);
        updateMeta(dark);
        syncToggles(dark);
        if (animate) {
            flashTransition();
        }
    }

    function init() {
        const stored = getStored();
        if (stored === 'dark' || stored === 'light') {
            apply(stored, false);
        } else {
            updateMeta(isDark());
            syncToggles(isDark());
        }

        document.querySelectorAll('.admin-theme-toggle, .site-theme-toggle').forEach((btn) => {
            if (btn.dataset.adminThemeBound) return;
            btn.dataset.adminThemeBound = '1';
            btn.addEventListener('click', () => {
                apply(isDark() ? 'light' : 'dark', true);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

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

    function updateMetaTheme(dark) {
        let meta = document.querySelector('meta[name="theme-color"]');
        if (!meta) {
            meta = document.createElement('meta');
            meta.setAttribute('name', 'theme-color');
            document.head.appendChild(meta);
        }
        meta.setAttribute('content', dark ? '#09090b' : '#451a03');
    }

    function flashTransition() {
        root.classList.add('theme-transition');
        window.setTimeout(() => root.classList.remove('theme-transition'), TRANSITION_MS);
    }

    function syncToggles(dark) {
        document.querySelectorAll('.site-theme-toggle, .admin-theme-toggle').forEach((btn) => {
            btn.classList.toggle('site-theme-toggle--dark', dark);
            btn.classList.toggle('admin-theme-toggle--dark', dark);
            btn.setAttribute('aria-checked', dark ? 'true' : 'false');
            btn.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
            btn.setAttribute('title', dark ? 'Light mode' : 'Dark mode');
            const label = btn.querySelector('.site-theme-toggle-text');
            if (label) {
                label.textContent = dark ? 'Light' : 'Dark';
            }
        });
    }

    function apply(theme, animate) {
        const dark = theme === 'dark';
        root.classList.toggle('dark', dark);
        document.body.classList.toggle('site-dark', dark);
        root.style.colorScheme = dark ? 'dark' : 'light';
        setStored(theme);
        updateMetaTheme(dark);
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
            updateMetaTheme(isDark());
            syncToggles(isDark());
        }

        document.querySelectorAll('.site-theme-toggle').forEach((btn) => {
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

(function () {

    'use strict';



    const sidebar = document.getElementById('admin-sidebar');

    const backdrop = document.getElementById('admin-sidebar-backdrop');

    const toggle = document.getElementById('admin-menu-toggle');

    const closeBtn = document.getElementById('admin-sidebar-close');

    const collapseBtn = document.getElementById('admin-sidebar-collapse');

    const iconExpand = document.getElementById('admin-collapse-icon-expand');

    const iconCollapse = document.getElementById('admin-collapse-icon-collapse');



    const OPEN_CLASSES = ['translate-x-0'];

    const CLOSED_CLASSES = ['-translate-x-full'];

    const BACKDROP_OPEN = ['opacity-100', 'visible', 'pointer-events-auto'];

    const BACKDROP_CLOSED = ['opacity-0', 'invisible', 'pointer-events-none'];



    const STORAGE_KEY = 'hv-admin-sidebar-collapsed';

    const DESKTOP_MQ = window.matchMedia('(min-width: 1024px)');



    function isDesktop() {

        return DESKTOP_MQ.matches;

    }



    function openMobileSidebar() {

        if (!sidebar || isDesktop()) return;

        sidebar.classList.remove(...CLOSED_CLASSES);

        sidebar.classList.add(...OPEN_CLASSES);

        backdrop?.classList.remove(...BACKDROP_CLOSED);

        backdrop?.classList.add(...BACKDROP_OPEN);

        toggle?.setAttribute('aria-expanded', 'true');

        document.body.classList.add('overflow-hidden');

    }



    function closeMobileSidebar() {

        if (!sidebar || isDesktop()) return;

        sidebar.classList.remove(...OPEN_CLASSES);

        sidebar.classList.add(...CLOSED_CLASSES);

        backdrop?.classList.remove(...BACKDROP_OPEN);

        backdrop?.classList.add(...BACKDROP_CLOSED);

        toggle?.setAttribute('aria-expanded', 'false');

        document.body.classList.remove('overflow-hidden');

    }



    function setCollapsed(collapsed) {

        if (!sidebar) return;

        sidebar.dataset.collapsed = collapsed ? 'true' : 'false';

        collapseBtn?.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');

        iconExpand?.classList.toggle('hidden', collapsed);

        iconCollapse?.classList.toggle('hidden', !collapsed);

        if (isDesktop()) {

            try {

                localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');

            } catch (_) { /* ignore */ }

        }

    }



    function initDesktopCollapse() {

        if (!isDesktop()) {

            setCollapsed(false);

            return;

        }

        let collapsed = false;

        try {

            collapsed = localStorage.getItem(STORAGE_KEY) === '1';

        } catch (_) { /* ignore */ }

        setCollapsed(collapsed);

    }



    toggle?.addEventListener('click', () => {

        if (sidebar?.classList.contains('translate-x-0') && !isDesktop()) {

            closeMobileSidebar();

        } else {

            openMobileSidebar();

        }

    });



    closeBtn?.addEventListener('click', closeMobileSidebar);

    backdrop?.addEventListener('click', closeMobileSidebar);



    collapseBtn?.addEventListener('click', () => {

        if (!isDesktop()) return;

        const collapsed = sidebar?.dataset.collapsed === 'true';

        setCollapsed(!collapsed);

    });



    document.querySelectorAll('.admin-sidebar-link').forEach((link) => {

        link.addEventListener('click', () => {

            if (!isDesktop()) closeMobileSidebar();

        });

    });



    document.addEventListener('keydown', (e) => {

        if (e.key === 'Escape' && !isDesktop()) closeMobileSidebar();

    });



    DESKTOP_MQ.addEventListener('change', () => {

        closeMobileSidebar();

        initDesktopCollapse();

    });



    initDesktopCollapse();

})();


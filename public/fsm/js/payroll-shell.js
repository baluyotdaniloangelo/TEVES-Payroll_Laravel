document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const sidebar = document.getElementById('sidebar');
    const mobileButton = document.getElementById('mobile-menu');
    const collapseButton = document.getElementById('desktop-collapse');
    const backdrop = document.querySelector('[data-fsm-close]');
    if (!sidebar) return;

    const closeMobile = function () {
        body.classList.remove('fsm-mobile-open');
        mobileButton?.setAttribute('aria-expanded', 'false');
    };
    mobileButton?.addEventListener('click', function () {
        const opened = body.classList.toggle('fsm-mobile-open');
        mobileButton.setAttribute('aria-expanded', String(opened));
    });
    backdrop?.addEventListener('click', closeMobile);
    collapseButton?.addEventListener('click', function () {
        const collapsed = body.classList.toggle('fsm-collapsed');
        collapseButton.setAttribute('aria-expanded', String(!collapsed));
        collapseButton.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeMobile();
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) closeMobile();
    });
    sidebar.querySelectorAll('[data-nav-group]').forEach(function (group) {
        const button = group.querySelector('.sidebar-group-toggle');
        const items = group.querySelector('[data-nav-items]');
        if (!button || !items) return;
        button.addEventListener('click', function () {
            if (body.classList.contains('fsm-collapsed')) body.classList.remove('fsm-collapsed');
            items.hidden = !items.hidden;
            button.setAttribute('aria-expanded', String(!items.hidden));
        });
    });
});

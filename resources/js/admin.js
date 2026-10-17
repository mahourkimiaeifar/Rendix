(() => {
    const body = document.body;
    const toggle = document.getElementById('admin-sidebar-toggle');
    const sidebar = document.getElementById('admin-sidebar');

    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            body.classList.toggle('admin-sidebar-open');
        });

        sidebar.addEventListener('click', (event) => {
            if (event.target.closest('a')) {
                body.classList.remove('admin-sidebar-open');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                body.classList.remove('admin-sidebar-open');
            }
        });
    }

    document.addEventListener('click', (event) => {
        const menu = document.querySelector('.admin-user-menu');

        if (menu && menu.open && !menu.contains(event.target)) {
            menu.removeAttribute('open');
        }
    });
})();
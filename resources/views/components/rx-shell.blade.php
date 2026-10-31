{{-- =========================================================
     Rendix Shell Reviver — toast / theme / scroll / back-top
     هر جا include شود، حتی اگر layout سالم نباشد، کار می‌کند
========================================================= --}}
<script>
(function () {
    if (window.__rxShell) { return; }
    window.__rxShell = true;

    /* ---------- theme ---------- */
    if (!window.rxToggleTheme) {
        window.rxToggleTheme = function () {
            try {
                var r = document.documentElement;
                var cur = r.getAttribute('data-theme') || (r.classList.contains('light') ? 'light' : 'dark');
                var next = cur === 'light' ? 'dark' : 'light';
                r.classList.toggle('light', next === 'light');
                r.classList.toggle('dark', next !== 'light');
                r.setAttribute('data-theme', next);
                try { localStorage.setItem('rendix-theme', next); } catch (e) {}
                document.cookie = 'rendix-theme=' + next + '; path=/; max-age=31536000; SameSite=Lax';
            } catch (e) {}
        };
    }

    /* ---------- ensure elements ---------- */
    function ensure(id, tag, cls) {
        var el = document.getElementById(id);
        if (!el) {
            el = document.createElement(tag);
            el.id = id;
            if (cls) { el.className = cls; }
            document.body.appendChild(el);
        }
        return el;
    }

    var stack = ensure('rxds-toast-stack', 'div', 'rxds-toast-stack');
    stack.setAttribute('aria-live', 'polite');

    var progress = ensure('rx-scroll-progress', 'div');
    progress.setAttribute('aria-hidden', 'true');

    var backTop = ensure('rx-back-top', 'button');
    backTop.setAttribute('aria-label', 'بازگشت به بالا');
    if (!backTop.innerHTML) { backTop.innerHTML = '<i class="fa-solid fa-arrow-up"></i>'; }
    backTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    /* ---------- scroll ---------- */
    function onScroll() {
        var navbar = document.getElementById('navbar');
        var y = window.scrollY || document.documentElement.scrollTop || 0;

        if (navbar) {
            navbar.classList.toggle('nav-scrolled', y > 12);
            navbar.classList.toggle('is-scrolled', y > 12);
        }

        var h = document.documentElement.scrollHeight - window.innerHeight;
        progress.style.width = h > 0 ? ((y / h) * 100).toFixed(2) + '%' : '0%';
        backTop.classList.toggle('show', y > 420);
    }

    document.addEventListener('DOMContentLoaded', onScroll);
    document.addEventListener('livewire:navigated', onScroll);
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);

    /* ---------- toast ---------- */
    function rxToast(payload) {
        var data = payload && payload.detail ? payload.detail : payload;
        if (Array.isArray(data)) { data = data[0]; }

        var type = data && data.type ? data.type : 'info';
        var message = data && data.message ? data.message : (typeof data === 'string' ? data : '');
        if (!message) { return; }

        var icons = {
            success: 'fa-solid fa-circle-check',
            error: 'fa-solid fa-circle-exclamation',
            warning: 'fa-solid fa-triangle-exclamation',
            info: 'fa-solid fa-circle-info'
        };
        var titles = { success: 'موفق', error: 'خطا', warning: 'هشدار', info: 'اطلاع' };

        var el = document.createElement('div');
        el.className = 'rxds-toast ' + type;
        el.innerHTML =
            '<div class="rxds-toast-icon"><i class="' + (icons[type] || icons.info) + '"></i></div>' +
            '<div class="rxds-toast-body">' +
            '<div class="rxds-toast-title">' + (titles[type] || titles.info) + '</div>' +
            '<div class="rxds-toast-message"></div>' +
            '</div>';

        el.querySelector('.rxds-toast-message').textContent = message;
        stack.appendChild(el);

        setTimeout(function () {
            el.classList.add('is-leaving');
            setTimeout(function () { if (el.parentNode) { el.parentNode.removeChild(el); } }, 220);
        }, 3200);
    }

    window.rxToast = rxToast;

    if (!window.__rxToastBound) {
        window.__rxToastBound = true;

        window.addEventListener('rx-toast', function (e) { rxToast(e); });

        function bind() {
            if (window.Livewire && typeof window.Livewire.on === 'function') {
                window.Livewire.on('rx-toast', function (e) { rxToast(e); });
            }
        }

        document.addEventListener('DOMContentLoaded', bind);
        document.addEventListener('livewire:navigated', bind);
        if (window.Livewire) { bind(); }
    }
})();
</script>
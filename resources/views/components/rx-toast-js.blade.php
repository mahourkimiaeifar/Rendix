<script>
    (function () {
        function rxToast(payload) {
            var stack = document.getElementById('rxds-toast-stack');
            if (!stack) { return; }

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

            var titles = {
                success: 'موفق',
                error: 'خطا',
                warning: 'هشدار',
                info: 'اطلاع'
            };

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
                setTimeout(function () {
                    if (el.parentNode) { el.parentNode.removeChild(el); }
                }, 220);
            }, 3200);
        }

        window.rxToast = rxToast;

        function bindLivewireToasts() {
            if (window.Livewire && typeof window.Livewire.on === 'function') {
                window.Livewire.on('rx-toast', function (event) {
                    rxToast(event);
                });
            }
        }

        window.addEventListener('rx-toast', function (event) { rxToast(event); });
        document.addEventListener('DOMContentLoaded', bindLivewireToasts);
        document.addEventListener('livewire:navigated', bindLivewireToasts);
    })();
</script>
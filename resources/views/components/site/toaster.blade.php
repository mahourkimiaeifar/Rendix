<div id="rxds-toast-stack" class="rxds-toast-stack" aria-live="polite" aria-atomic="true"></div>

@once
<script>
    (function () {
        function showToast(payload) {
            var stack = document.getElementById('rxds-toast-stack');

            if (!stack) {
                return;
            }

            var type = payload && payload.type ? payload.type : 'info';
            var message = payload && payload.message ? payload.message : (typeof payload === 'string' ? payload : '');

            if (!message) {
                return;
            }

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
            el.innerHTML = ''
                + '<div class="rxds-toast-icon"><i class="' + (icons[type] || icons.info) + '"></i></div>'
                + '<div class="rxds-toast-body">'
                + '<div class="rxds-toast-title">' + (titles[type] || titles.info) + '</div>'
                + '<div class="rxds-toast-message"></div>'
                + '</div>';

            el.querySelector('.rxds-toast-message').textContent = message;
            stack.appendChild(el);

            setTimeout(function () {
                el.classList.add('is-leaving');

                setTimeout(function () {
                    if (el.parentNode) {
                        el.parentNode.removeChild(el);
                    }
                }, 220);
            }, 3200);
        }

        window.rxToast = showToast;

        document.addEventListener('DOMContentLoaded', function () {
            if (window.Livewire) {
                Livewire.on('rx-toast', function (event) {
                    var payload = Array.isArray(event) ? event[0] : event;
                    showToast(payload);
                });
            }

            window.addEventListener('rx-toast', function (event) {
                showToast(event.detail);
            });
        });
    })();
</script>
@endonce
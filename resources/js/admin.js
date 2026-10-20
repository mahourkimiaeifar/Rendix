/* =========================================================
   Rendix Admin JS — Stable Version
   ========================================================= */

(() => {
    const getSwal = () => window.Swal;

    const escapeHtml = (value) =>
        String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    const getPayload = (event) => {
        if (!event) return {};

        if (Array.isArray(event.detail)) {
            return event.detail[0] ?? {};
        }

        return event.detail ?? {};
    };

    const showRxToast = ({ type = 'success', title = '', message = '' } = {}) => {
        const Swal = getSwal();

        if (!Swal) {
            console.warn('SweetAlert2 is not loaded.');
            return;
        }

        const icon =
            type === 'error' ? 'error' :
                type === 'warning' ? 'warning' :
                    type === 'info' ? 'info' :
                        'success';

        Swal.fire({
            toast: true,
            position: 'top',
            icon,
            title: title || (message ? '' : 'انجام شد'),
            text: message,
            showConfirmButton: false,
            timer: 2400,
            timerProgressBar: true,
            backdrop: false,
            customClass: {
                popup: 'rx-swal-toast',
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
        });
    };

    window.addEventListener('rx-toast', (event) => {
        showRxToast(getPayload(event));
    });

    const bindLivewireErrorHook = () => {
        const Swal = getSwal();

        if (
            !window.Livewire ||
            typeof Livewire.hook !== 'function' ||
            !Swal ||
            window.__rxLivewireErrorBound
        ) {
            return;
        }

        window.__rxLivewireErrorBound = true;

        let lastSignature = '';
        let lastTime = 0;

        try {
            Livewire.hook('commit', ({ succeed }) => {
                succeed(({ effects }) => {
                    const errors = effects?.errors;

                    if (!errors) {
                        return;
                    }

                    const messages = Object.values(errors)
                        .flatMap((value) => Array.isArray(value) ? value : [value])
                        .filter(Boolean);

                    if (messages.length === 0) {
                        return;
                    }

                    const signature = messages.join('|');
                    const now = Date.now();

                    if (signature === lastSignature && now - lastTime < 1200) {
                        return;
                    }

                    lastSignature = signature;
                    lastTime = now;

                    if (Swal.isVisible()) {
                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'برای ادامه، این موارد رو تکمیل کن',
                        html: `
                            <ul class="rx-swal-error-list">
                                ${messages.map((message) => `<li>${escapeHtml(message)}</li>`).join('')}
                            </ul>
                        `,
                        confirmButtonText: 'باشه، می‌رم اصلاحش می‌کنم',
                        customClass: {
                            popup: 'rx-swal-popup',
                            confirmButton: 'rx-swal-confirm',
                        },
                        backdrop: 'rgba(7, 19, 41, 0.72)',
                    });
                });
            });
        } catch (error) {
            console.warn('Livewire error hook failed:', error);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindLivewireErrorHook);
    } else {
        bindLivewireErrorHook();
    }

    window.addEventListener('livewire:load', bindLivewireErrorHook);
})();

/* =========================================================
   Sidebar
   ========================================================= */

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

/* =========================================================
   Password Eye Toggle
   ========================================================= */

(() => {
    const togglePasswordVisibility = (button) => {
        const shell = button.closest('[data-rx-password-shell]');
        if (!shell) return;

        const input = shell.querySelector('input');
        if (!input) return;

        const isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';

        button.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
        button.setAttribute('title', isPassword ? 'پنهان کردن رمز' : 'نمایش رمز');
        button.setAttribute('aria-label', isPassword ? 'پنهان کردن رمز' : 'نمایش رمز');

        const icon = button.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        }
    };

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-rx-password-toggle]');
        if (!button) return;

        event.preventDefault();
        togglePasswordVisibility(button);
    });
})();

/* =========================================================
   Custom Select / Dropdown — Global Portal Version
   ========================================================= */

(() => {
    const MENU_ID = 'rx-select-global-menu';

    let activeField = null;
    let activeTrigger = null;
    let activeInput = null;
    let menu = null;
    let options = [];
    let highlightedIndex = -1;

    const updateHighlight = (buttons) => {
        buttons.forEach((button, index) => {
            button.classList.toggle('highlighted', index === highlightedIndex);
        });
    };

    const ensureMenu = () => {
        if (menu && document.body.contains(menu)) {
            return menu;
        }

        menu = document.getElementById(MENU_ID);

        if (!menu) {
            menu = document.createElement('div');
            menu.id = MENU_ID;
            menu.className = 'rx-select-global-menu';
            menu.setAttribute('role', 'listbox');
            menu.hidden = true;
            document.body.appendChild(menu);
        }

        return menu;
    };

    const closeSelect = (focusTrigger = false) => {
        if (activeField) {
            activeField.classList.remove('open');
        }

        if (activeTrigger) {
            activeTrigger.setAttribute('aria-expanded', 'false');

            if (focusTrigger && activeTrigger.isConnected) {
                activeTrigger.focus();
            }
        }

        if (menu) {
            menu.hidden = true;
        }

        activeField = null;
        activeTrigger = null;
        activeInput = null;
        options = [];
        highlightedIndex = -1;
    };

    const readOptions = (field) => {
        const script = field.querySelector('script[data-rx-select-options]');

        try {
            return JSON.parse(script ? script.textContent : '[]');
        } catch (error) {
            console.warn('Invalid select options JSON:', error);
            return [];
        }
    };

    const positionMenu = () => {
        if (!menu || !activeTrigger || menu.hidden) {
            return;
        }

        if (!activeField?.isConnected || !activeTrigger.isConnected) {
            closeSelect();
            return;
        }

        const rect = activeTrigger.getBoundingClientRect();
        const gap = 8;
        const vw = window.innerWidth;
        const vh = window.innerHeight;
        const width = Math.max(rect.width, 220);

        menu.style.width = `${width}px`;
        menu.style.maxHeight = '290px';

        const menuHeight = Math.min(menu.scrollHeight + 16, 290);

        let top = rect.bottom + gap;

        if (top + menuHeight > vh - gap && rect.top - gap - menuHeight > gap) {
            top = rect.top - gap - menuHeight;
        }

        top = Math.max(gap, Math.min(top, vh - menuHeight - gap));

        let left = rect.left;

        if (left + width > vw - gap) {
            left = vw - gap - width;
        }

        if (left < gap) {
            left = gap;
        }

        menu.style.position = 'fixed';
        menu.style.zIndex = '2147483647';
        menu.style.top = `${Math.round(top)}px`;
        menu.style.left = `${Math.round(left)}px`;
        menu.style.right = 'auto';
    };

    const chooseOption = (button) => {
        if (!activeField?.isConnected || !activeInput?.isConnected) {
            closeSelect();
            return;
        }

        const value = button.dataset.value ?? '';
        const label = button.querySelector('span')?.textContent?.trim() || value;

        activeInput.value = value;

        const labelEl = activeField.querySelector('[data-rx-select-label]');

        if (labelEl) {
            labelEl.textContent = label || activeInput.dataset.rxSelectPlaceholder || 'انتخاب کنید...';
        }

        const emptyAllowed = activeInput.dataset.rxSelectEmpty === '1';

        if (activeTrigger) {
            activeTrigger.classList.toggle('is-placeholder', value === '' && !emptyAllowed);
        }

        activeInput.dispatchEvent(new Event('input', { bubbles: true }));
        activeInput.dispatchEvent(new Event('change', { bubbles: true }));

        closeSelect(true);
    };

    const renderOptions = () => {
        if (!menu || !activeInput) {
            return;
        }

        menu.innerHTML = '';

        if (options.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'rx-select-empty-option';
            empty.textContent = 'گزینه‌ای نیست';
            menu.appendChild(empty);
            return;
        }

        const currentValue = String(activeInput.value ?? '');

        options.forEach((option, index) => {
            const optionValue = String(option.value ?? '');
            const isSelected = optionValue === currentValue;

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'rx-select-option';
            button.setAttribute('role', 'option');
            button.setAttribute('aria-selected', isSelected ? 'true' : 'false');
            button.dataset.value = optionValue;
            button.dataset.index = String(index);
            button.tabIndex = -1;

            if (isSelected) {
                button.classList.add('selected');
            }

            if (index === highlightedIndex) {
                button.classList.add('highlighted');
            }

            const span = document.createElement('span');
            span.textContent = option.label ?? optionValue;

            const check = document.createElement('i');
            check.className = 'fa-solid fa-check rx-select-check';
            check.hidden = !isSelected;

            button.appendChild(span);
            button.appendChild(check);

            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                chooseOption(button);
            });

            menu.appendChild(button);
        });
    };

    const openSelect = (field) => {
        if (!field) {
            return;
        }

        if (activeField === field) {
            closeSelect(true);
            return;
        }

        closeSelect();

        const trigger = field.querySelector('[data-rx-select-trigger]');
        const input = field.querySelector('[data-rx-select-input]');

        if (!trigger || !input) {
            return;
        }

        activeField = field;
        activeTrigger = trigger;
        activeInput = input;

        options = readOptions(field);

        highlightedIndex = options.findIndex(
            (option) => String(option.value ?? '') === String(input.value ?? '')
        );

        if (highlightedIndex < 0) {
            highlightedIndex = 0;
        }

        ensureMenu();
        renderOptions();

        field.classList.add('open');
        trigger.setAttribute('aria-expanded', 'true');

        menu.hidden = false;
        positionMenu();

        const firstOption = menu.querySelector('.rx-select-option');
        firstOption?.focus();
    };

    document.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target : null;

        if (!target) {
            return;
        }

        const trigger = target.closest('[data-rx-select-trigger]');

        if (trigger) {
            event.preventDefault();
            openSelect(trigger.closest('[data-rx-select]'));
            return;
        }

        const option = target.closest('.rx-select-option');

        if (option && menu && menu.contains(option)) {
            event.preventDefault();
            chooseOption(option);
            return;
        }

        if (!target.closest('[data-rx-select]') && (!menu || !menu.contains(target))) {
            closeSelect();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (!activeField?.isConnected) {
            if (activeField) {
                closeSelect();
            }

            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            closeSelect(true);
            return;
        }

        if (event.key === 'Tab') {
            closeSelect();
            return;
        }

        const optionButtons = menu
            ? Array.from(menu.querySelectorAll('.rx-select-option'))
            : [];

        if (optionButtons.length === 0) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            highlightedIndex = Math.min(highlightedIndex + 1, optionButtons.length - 1);
            updateHighlight(optionButtons);
            optionButtons[highlightedIndex]?.focus();
            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            highlightedIndex = Math.max(highlightedIndex - 1, 0);
            updateHighlight(optionButtons);
            optionButtons[highlightedIndex]?.focus();
            return;
        }

        if (event.key === 'Home') {
            event.preventDefault();
            highlightedIndex = 0;
            updateHighlight(optionButtons);
            optionButtons[0]?.focus();
            return;
        }

        if (event.key === 'End') {
            event.preventDefault();
            highlightedIndex = optionButtons.length - 1;
            updateHighlight(optionButtons);
            optionButtons[highlightedIndex]?.focus();
            return;
        }

        if (event.key === 'Enter' || event.key === ' ') {
            const active = document.activeElement;

            if (
                active instanceof Element &&
                active.matches('.rx-select-option') &&
                menu?.contains(active)
            ) {
                event.preventDefault();
                chooseOption(active);
            }
        }
    });

    window.addEventListener('scroll', () => {
        positionMenu();
    }, true);

    window.addEventListener('resize', () => {
        positionMenu();
    });

    document.addEventListener('livewire:navigated', () => {
        closeSelect();

        if (menu) {
            menu.innerHTML = '';
        }
    });
})();

/* =========================================================
   Rendix Choice Pills — Desktop Friendly Version
   ========================================================= */

(() => {
    const initChoicePills = (root = document) => {
        if (!root || typeof root.querySelectorAll !== 'function') {
            return;
        }

        root.querySelectorAll('[data-rx-choice-track]').forEach((track) => {
            if (track.dataset.rxChoiceBound === '1') {
                return;
            }

            track.dataset.rxChoiceBound = '1';

            const field = track.closest('[data-rx-choice-field]');
            const prev = field?.querySelector('[data-rx-choice-prev]');
            const next = field?.querySelector('[data-rx-choice-next]');

            const getPills = () => Array.from(track.querySelectorAll('.rx-choice-pill'));

            const getGap = () => {
                const styles = window.getComputedStyle(track);
                return parseFloat(styles.columnGap || styles.gap || '10') || 10;
            };

            const getMaxScroll = () => {
                return Math.max(0, Math.ceil(track.scrollWidth - track.clientWidth));
            };

            const hasOverflow = () => {
                return getMaxScroll() > 4;
            };

            const getCurrentIndex = () => {
                const pills = getPills();

                if (pills.length === 0) {
                    return 0;
                }

                const trackRect = track.getBoundingClientRect();
                const trackCenter = trackRect.left + trackRect.width / 2;

                let bestIndex = 0;
                let bestDistance = Infinity;

                pills.forEach((pill, index) => {
                    const rect = pill.getBoundingClientRect();
                    const pillCenter = rect.left + rect.width / 2;
                    const distance = Math.abs(pillCenter - trackCenter);

                    if (distance < bestDistance) {
                        bestDistance = distance;
                        bestIndex = index;
                    }
                });

                return bestIndex;
            };

            const updateState = () => {
                const overflow = hasOverflow();
                const pills = getPills();
                const index = getCurrentIndex();

                field?.classList.toggle('rx-choice-has-overflow', overflow);

                if (prev) {
                    prev.disabled = !overflow || index <= 0;
                }

                if (next) {
                    next.disabled = !overflow || index >= pills.length - 1;
                }
            };

            const centerPill = (pill, behavior = 'smooth') => {
                if (!pill) {
                    return;
                }

                const trackRect = track.getBoundingClientRect();
                const pillRect = pill.getBoundingClientRect();

                const delta =
                    (pillRect.left - trackRect.left) -
                    ((trackRect.width - pillRect.width) / 2);

                track.scrollBy({
                    left: delta,
                    behavior,
                });

                window.setTimeout(updateState, behavior === 'smooth' ? 320 : 20);
            };

            const move = (direction) => {
                const pills = getPills();

                if (pills.length === 0) {
                    return;
                }

                const currentIndex = getCurrentIndex();
                const targetIndex = Math.min(
                    Math.max(currentIndex + direction, 0),
                    pills.length - 1
                );

                centerPill(pills[targetIndex]);
            };

            prev?.addEventListener('click', (event) => {
                event.preventDefault();
                move(-1);
            });

            next?.addEventListener('click', (event) => {
                event.preventDefault();
                move(1);
            });

            track.addEventListener('scroll', updateState, { passive: true });

            track.addEventListener('wheel', (event) => {
                if (!hasOverflow()) {
                    return;
                }

                if (Math.abs(event.deltaY) <= Math.abs(event.deltaX)) {
                    return;
                }

                event.preventDefault();

                const direction =
                    (window.getComputedStyle(track).direction || document.dir || 'ltr') === 'rtl'
                        ? -1
                        : 1;

                track.scrollLeft += event.deltaY * 0.55 * direction;

                updateState();
            }, { passive: false });

            track.addEventListener('click', (event) => {
                const pill = event.target instanceof Element
                    ? event.target.closest('.rx-choice-pill')
                    : null;

                if (!pill) {
                    return;
                }

                window.setTimeout(() => {
                    centerPill(pill);
                }, 30);
            });

            const activePill = track.querySelector('input:checked')?.closest('.rx-choice-pill');

            if (activePill) {
                window.setTimeout(() => {
                    centerPill(activePill, 'auto');
                }, 60);
            }

            updateState();
        });
    };

    const observeNewChoicePills = () => {
        if (typeof MutationObserver === 'undefined') {
            return;
        }

        const observer = new MutationObserver(() => {
            initChoicePills(document);
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    };

    const start = () => {
        initChoicePills(document);
        observeNewChoicePills();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }

    document.addEventListener('livewire:navigated', () => {
        initChoicePills(document);
    });
})();
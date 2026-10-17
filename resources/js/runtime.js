(() => {
    if (window.__rendixRuntime) return;
    window.__rendixRuntime = true;

    const html = document.documentElement;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const THEME_KEY = 'rendix-theme';

    const safe = (fn, label = 'error') => {
        try {
            fn();
        } catch (error) {
            console.warn(`[Rendix] ${label}:`, error);
        }
    };

    const toFa = (value) => {
        return String(value).replace(/[0-9]/g, (d) => '۰۱۲۳۴۵۶۷۸۹'[d]);
    };

    const isElement = (target) => target instanceof Element;

    /* =========================
      THEME
      ========================= */
    /* =========================
       THEME
       ========================= */

    const getCookieTheme = () => {
        try {
            const cookie = document.cookie
                .split('; ')
                .find((row) => row.startsWith(`${THEME_KEY}=`));

            if (!cookie) return null;

            const value = decodeURIComponent(cookie.split('=')[1]);

            return value === 'light' || value === 'dark' ? value : null;
        } catch (error) {
            return null;
        }
    };

    const setCookieTheme = (theme) => {
        try {
            document.cookie = `${THEME_KEY}=${theme}; path=/; max-age=31536000; SameSite=Lax`;
        } catch (error) {
            console.warn('[Rendix] set cookie theme failed:', error);
        }
    };

    const getStoredTheme = () => {
        try {
            const local = localStorage.getItem(THEME_KEY);

            if (local === 'light' || local === 'dark') {
                return local;
            }
        } catch (error) { }

        const cookie = getCookieTheme();

        if (cookie === 'light' || cookie === 'dark') {
            return cookie;
        }

        return 'dark';
    };

    const setTheme = (theme, save = true) => {
        const isLight = theme === 'light';
        const normalizedTheme = isLight ? 'light' : 'dark';

        html.classList.toggle('light', isLight);
        html.classList.toggle('dark', !isLight);
        html.setAttribute('data-theme', normalizedTheme);

        // همیشه cookie را هماهنگ نگه دار
        setCookieTheme(normalizedTheme);

        if (save) {
            try {
                localStorage.setItem(THEME_KEY, normalizedTheme);
            } catch (error) {
                console.warn('[Rendix] localStorage theme failed:', error);
            }
        }

        window.dispatchEvent(
            new CustomEvent('rendix-theme-changed', {
                detail: {
                    theme: normalizedTheme,
                    isLight,
                },
            })
        );
    };

    const syncThemeFromStorage = () => {
        setTheme(getStoredTheme(), false);
    };

    const initTheme = () => {
        syncThemeFromStorage();

        document.addEventListener('click', (event) => {
            if (!isElement(event.target)) return;

            const button = event.target.closest('[data-theme-toggle], [data-rendix-theme-toggle]');
            if (!button) return;

            const currentTheme = getStoredTheme();
            const nextTheme = currentTheme === 'light' ? 'dark' : 'light';

            setTheme(nextTheme, true);
        });

        // هماهنگی بین تب‌ها
        window.addEventListener('storage', (event) => {
            if (event.key === THEME_KEY) {
                syncThemeFromStorage();
            }
        });
    };

    /* =========================
       MOBILE MENU
       ========================= */
    const closeMobileMenu = () => {
        const menu = document.getElementById('mobile-menu');
        const toggle = document.getElementById('mobile-menu-toggle');

        if (menu) menu.classList.add('hidden');

        if (toggle) {
            const icon = toggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        }
    };

    const initMobileMenu = () => {
        document.addEventListener('click', (event) => {
            if (!isElement(event.target)) return;

            const toggle = event.target.closest('#mobile-menu-toggle, [data-mobile-menu-toggle]');
            if (toggle) {
                const menu = document.getElementById('mobile-menu');
                if (!menu) return;

                const isHidden = menu.classList.contains('hidden');
                menu.classList.toggle('hidden', !isHidden);

                const icon = toggle.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-bars', isHidden);
                    icon.classList.toggle('fa-xmark', !isHidden);
                }

                return;
            }

            const menuLink = event.target.closest('#mobile-menu a');
            if (menuLink) {
                closeMobileMenu();
            }
        });
    };

    /* =========================
       NAVBAR / SCROLL UI
       ========================= */
    const updateScrollUI = () => {
        const navbar = document.getElementById('navbar');
        const backTop = document.getElementById('rx-back-top');
        const progress = document.getElementById('rx-scroll-progress');

        if (navbar) {
            navbar.classList.toggle('nav-scrolled', window.scrollY > 24);
        }

        if (backTop) {
            backTop.classList.toggle('show', window.scrollY > 500);
        }

        if (progress) {
            const doc = document.documentElement;
            const scrollTop = doc.scrollTop || document.body.scrollTop || 0;
            const scrollHeight = doc.scrollHeight - doc.clientHeight;
            const percent = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
            progress.style.width = `${percent}%`;
        }
    };

    const initScrollUI = () => {
        let ticking = false;

        const onScroll = () => {
            if (ticking) return;

            ticking = true;

            requestAnimationFrame(() => {
                updateScrollUI();
                ticking = false;
            });
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll, { passive: true });

        updateScrollUI();
    };

    const initBackToTop = () => {
        document.addEventListener('click', (event) => {
            if (!isElement(event.target)) return;

            const button = event.target.closest('#rx-back-top');
            if (!button) return;

            window.scrollTo({
                top: 0,
                behavior: reducedMotion ? 'auto' : 'smooth',
            });
        });
    };

    /* =========================
       SMOOTH ANCHOR LINKS
       ========================= */
    const initAnchors = () => {
        document.addEventListener('click', (event) => {
            if (!isElement(event.target)) return;

            const link = event.target.closest('a[href^="#"]');
            if (!link) return;

            const hash = link.getAttribute('href');
            if (!hash || hash === '#') return;

            const target = document.querySelector(hash);
            if (!target) return;

            event.preventDefault();

            target.scrollIntoView({
                behavior: reducedMotion ? 'auto' : 'smooth',
                block: 'start',
            });

            closeMobileMenu();
        });
    };

    /* =========================
       FAQ
       ========================= */
    const initFaq = () => {
        document.addEventListener('click', (event) => {
            if (!isElement(event.target)) return;

            const button = event.target.closest('[data-faq-toggle]');
            if (!button) return;

            const panel = button.nextElementSibling;
            const icon = button.querySelector('[data-faq-icon]');

            if (!panel) return;

            const wasOpen = !panel.classList.contains('hidden');

            document.querySelectorAll('[data-faq-panel]').forEach((item) => {
                item.classList.add('hidden');
            });

            document.querySelectorAll('[data-faq-icon]').forEach((item) => {
                item.style.transform = 'rotate(0deg)';
            });

            if (!wasOpen) {
                panel.classList.remove('hidden');

                if (icon) {
                    icon.style.transform = 'rotate(45deg)';
                }
            }
        });
    };

    /* =========================
       REVEAL ON SCROLL
       Fail-safe: بعد از چند ثانیه همه‌چیز اجباراً دیده میشه
       ========================= */
    let revealObserver = null;
    let revealFallbackTimer = null;

    const forceRevealAll = () => {
        if (revealFallbackTimer) {
            clearTimeout(revealFallbackTimer);
            revealFallbackTimer = null;
        }

        html.classList.remove('rx-reveal-ready');

        document.querySelectorAll('.rv').forEach((el) => {
            el.classList.add('rv-in');
        });
    };

    const initReveal = () => {
        const items = document.querySelectorAll('.rv:not(.rv-in)');

        if (!items.length) return;

        if (reducedMotion || !('IntersectionObserver' in window)) {
            forceRevealAll();
            return;
        }

        if (revealObserver) {
            revealObserver.disconnect();
        }

        html.classList.add('rx-reveal-ready');

        revealObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('rv-in');
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.08,
                rootMargin: '0px 0px -40px 0px',
            }
        );

        items.forEach((item) => {
            revealObserver.observe(item);
        });

        revealFallbackTimer = setTimeout(() => {
            forceRevealAll();
        }, 2800);
    };

    /* =========================
       COUNTERS
       ========================= */
    let counterObserver = null;

    const animateCounter = (element) => {
        const target = parseInt(element.dataset.count || '0', 10);

        if (Number.isNaN(target)) {
            element.textContent = '۰';
            return;
        }

        if (reducedMotion) {
            element.textContent = toFa(target);
            return;
        }

        const duration = 1600;
        const start = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const value = Math.floor(target * eased);

            element.textContent = toFa(value);

            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                element.textContent = toFa(target);
            }
        };

        requestAnimationFrame(tick);
    };

    const initCounters = () => {
        const counters = document.querySelectorAll('[data-count]:not([data-done])');

        if (!counters.length) return;

        if (counterObserver) {
            counterObserver.disconnect();
        }

        counterObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;

                    const element = entry.target;
                    element.setAttribute('data-done', '1');

                    animateCounter(element);

                    observer.unobserve(element);
                });
            },
            {
                threshold: 0.45,
            }
        );

        counters.forEach((counter) => {
            counterObserver.observe(counter);
        });
    };

    /* =========================
       GLOBAL BINDING
       فقط یک‌بار بستة listenerهای سراسری
       ========================= */
    if (!window.__rendixGlobalBound) {
        window.__rendixGlobalBound = true;

        safe(initTheme, 'theme');
        safe(initMobileMenu, 'mobile menu');
        safe(initScrollUI, 'scroll ui');
        safe(initBackToTop, 'back to top');
        safe(initAnchors, 'anchors');
        safe(initFaq, 'faq');
    }

    /* =========================
       PAGE-SPECIFIC BOOT
       بعد از هر navigate دوباره اجرا میشه
       ========================= */
    const bootPage = () => {
        safe(syncThemeFromStorage, 'theme sync');
        safe(initReveal, 'reveal');
        safe(initCounters, 'counters');
        safe(updateScrollUI, 'scroll ui update');
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootPage);
    } else {
        bootPage();
    }

    document.addEventListener('livewire:navigating', () => {
        closeMobileMenu();
    });

    document.addEventListener('livewire:navigated', () => {
        bootPage();
    });
})();
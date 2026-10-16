if (!window.__rendixRuntimeBootstrapped) {
    window.__rendixRuntimeBootstrapped = true;

    (function () {
        const INTRO_KEY = 'rendix-intro-played';
        const isReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const PRE_LOAD_MS = isReduced ? 900 : 2000;
        const POST_LOAD_MS = isReduced ? 1200 : 3000;
        const HOLD_MS = isReduced ? 160 : 320;
        const LEAVE_MS = 700;
        const FORCE_LOAD_AFTER_MS = 8000;
        const BACK_TO_TOP_THRESHOLD = 520;

        const state = {
            introFinished: false,
            pageLoaded: false,
            loadedAt: 0,
            lastLoaderValue: -1,
            revealObserver: null,
            sceneObserver: null,
            eventsBound: false,
            themeAnimating: false,
            scrollTick: false,
        };

        const loaderMessages = [
            'initializing_rendix',
            'loading_assets',
            'optimizing_interface',
            'preparing_digital_world',
            'welcome_traveler',
        ];

        function toFa(value) {
            return String(value).replace(/[0-9]/g, (d) => '۰۱۲۳۴۵۶۷۸۹'[d]);
        }

        function formatNumber(value, decimals = 0) {
            const fixed = decimals > 0
                ? Number(value).toFixed(decimals).replace('.', '٫')
                : String(Math.round(value));

            return toFa(fixed);
        }

        function playedIntro() {
            try {
                return sessionStorage.getItem(INTRO_KEY) === '1';
            } catch (e) {
                return false;
            }
        }

        function markIntroPlayed() {
            try {
                sessionStorage.setItem(INTRO_KEY, '1');
            } catch (e) {}
        }

        function getPreloader() {
            return document.getElementById('rendix-preloader');
        }

        function getCounter() {
            return document.getElementById('countdown');
        }

        function getProgressBar() {
            return document.getElementById('progress-bar');
        }

        function getStatusText() {
            return document.getElementById('loader-status');
        }

        function getScrollProgress() {
            return document.getElementById('rx-scroll-progress');
        }

        function getMobileMenu() {
            return document.getElementById('mobile-menu');
        }

        function getMobileButton() {
            return document.querySelector('[data-mobile-menu-button]');
        }

        function getBackToTop() {
            return document.getElementById('rx-back-to-top');
        }

        function setLoaderStatus(progress) {
            const statusEl = getStatusText();
            if (!statusEl) return;

            let index = 0;
            if (progress >= 99) index = 4;
            else if (progress >= 85) index = 3;
            else if (progress >= 55) index = 2;
            else if (progress >= 25) index = 1;

            statusEl.textContent = loaderMessages[index];
        }

        function updateLoader(value) {
            const clamped = Math.max(0, Math.min(100, Math.round(value)));

            if (clamped === state.lastLoaderValue) return;
            state.lastLoaderValue = clamped;

            const counterEl = getCounter();
            const barEl = getProgressBar();

            if (counterEl) counterEl.textContent = toFa(clamped);
            if (barEl) barEl.style.width = clamped + '%';

            setLoaderStatus(clamped);
        }

        function markLoaded() {
            if (state.pageLoaded) return;
            state.pageLoaded = true;
            state.loadedAt = performance.now();
        }

        function animateCount(el) {
            if (el.dataset.counted === '1') return;
            el.dataset.counted = '1';

            const target = parseFloat(el.dataset.count || '0');
            const decimals = parseInt(el.dataset.decimals || '0', 10);
            const suffix = el.dataset.suffix || '';
            const duration = 1200;
            const start = performance.now();

            function update(now) {
                const p = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - p, 3);
                const value = target * eased;

                el.textContent = formatNumber(value, decimals) + suffix;

                if (p < 1) {
                    requestAnimationFrame(update);
                }
            }

            requestAnimationFrame(update);
        }

        function showAllRevealsInstantly() {
            document.querySelectorAll('.rx-reveal').forEach((el) => {
                el.classList.remove('is-hidden');
                el.classList.add('is-visible');

                if (el.dataset.count) {
                    animateCount(el);
                }
            });
        }

        function prepareReveals() {
            const reveals = Array.from(document.querySelectorAll('.rx-reveal'));

            if (!reveals.length) return;

            if (isReduced || !('IntersectionObserver' in window)) {
                showAllRevealsInstantly();
                return;
            }

            reveals.forEach((el) => {
                if (!el.classList.contains('is-visible')) {
                    el.classList.add('is-hidden');
                }
            });

            if (state.revealObserver) {
                state.revealObserver.disconnect();
            }

            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;

                        const el = entry.target;
                        el.classList.remove('is-hidden');
                        el.classList.add('is-visible');

                        if (el.dataset.count) {
                            animateCount(el);
                        }

                        observer.unobserve(el);
                    });
                },
                {
                    threshold: 0.12,
                    rootMargin: '0px 0px -8% 0px',
                }
            );

            reveals.forEach((el) => observer.observe(el));
            state.revealObserver = observer;
        }

        function observeScenes() {
            const scenes = Array.from(document.querySelectorAll('[data-rx-scene]'));

            if (!scenes.length || !('IntersectionObserver' in window)) return;

            if (state.sceneObserver) {
                state.sceneObserver.disconnect();
            }

            let activeId = null;

            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;
                        if (entry.intersectionRatio < 0.28) return;

                        const el = entry.target;
                        const id = el.dataset.sceneId || el.id || 'section';

                        if (activeId === id) return;
                        activeId = id;

                        window.dispatchEvent(
                            new CustomEvent('rendix-scene', {
                                detail: {
                                    id: id,
                                    color: el.dataset.sceneColor || '#45C8FF',
                                    intensity: parseFloat(el.dataset.sceneIntensity || '1'),
                                    speed: parseFloat(el.dataset.sceneSpeed || '1'),
                                },
                            })
                        );
                    });
                },
                {
                    threshold: [0.28, 0.5, 0.72],
                    rootMargin: '-8% 0px -8% 0px',
                }
            );

            scenes.forEach((scene) => observer.observe(scene));
            state.sceneObserver = observer;
        }

        function updateScrollProgress() {
            const progressEl = getScrollProgress();
            if (!progressEl) return;

            const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const progress = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;

            progressEl.style.width = progress + '%';
        }

        function updateBackToTop() {
            const btn = getBackToTop();
            if (!btn) return;

            if (window.scrollY > BACK_TO_TOP_THRESHOLD) {
                btn.classList.add('is-visible');
            } else {
                btn.classList.remove('is-visible');
            }
        }

        function updateActiveNavLinks() {
            const path = window.location.pathname.replace(/\/+$/, '') || '/';

            document.querySelectorAll('[data-nav-link]').forEach((link) => {
                const href = link.getAttribute('href') || '';
                const normalizedHref = href.replace(/\/+$/, '') || '/';

                const isActive = normalizedHref === '/'
                    ? path === '/'
                    : path === normalizedHref || path.startsWith(normalizedHref + '/');

                link.classList.toggle('is-active', isActive);
            });
        }

        function closeMobileMenu() {
            const menu = getMobileMenu();
            const button = getMobileButton();

            if (!menu) return;

            menu.classList.remove('open');

            if (button) {
                button.setAttribute('aria-expanded', 'false');
            }

            document.body.classList.remove('overflow-hidden');
        }

        function toggleMobileMenu() {
            const menu = getMobileMenu();
            const button = getMobileButton();

            if (!menu || !button) return;

            const isOpen = menu.classList.toggle('open');
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.body.classList.toggle('overflow-hidden', isOpen);
        }

        function toggleFaq(trigger) {
            const item = trigger.closest('[data-faq]');
            if (!item) return;

            const panel = item.querySelector('[data-faq-panel]');
            const icon = trigger.querySelector('.rx-faq-icon');
            const willOpen = !panel?.classList.contains('is-open');

            const group = item.parentElement;
            if (group) {
                group.querySelectorAll('[data-faq]').forEach((other) => {
                    if (other === item) return;

                    other.querySelector('[data-faq-panel]')?.classList.remove('is-open');
                    other.querySelector('[data-faq-toggle]')?.setAttribute('aria-expanded', 'false');
                    other.querySelector('.rx-faq-icon')?.classList.remove('is-open');
                });
            }

            panel?.classList.toggle('is-open', willOpen);
            icon?.classList.toggle('is-open', willOpen);
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        }

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: isReduced ? 'auto' : 'smooth',
            });
        }

        function flashThemeToggle(x, y, next) {
            const flash = document.createElement('div');
            flash.className = 'rx-theme-flash';
            flash.dataset.next = next;
            flash.style.setProperty('--rx-flash-x', `${x}px`);
            flash.style.setProperty('--rx-flash-y', `${y}px`);

            document.body.appendChild(flash);

            requestAnimationFrame(() => {
                flash.classList.add('is-active');
            });

            setTimeout(() => {
                if (window.RendixTheme) {
                    window.RendixTheme.set(next);
                }
            }, 180);

            setTimeout(() => {
                flash.classList.add('is-out');
            }, 320);

            setTimeout(() => {
                flash.remove();
                state.themeAnimating = false;
            }, 820);
        }

        function toggleThemeWithAnimation(button) {
            if (!window.RendixTheme || state.themeAnimating) return;

            const current = window.RendixTheme.get();
            const next = current === 'light' ? 'dark' : 'light';

            const rect = button.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;
            const endRadius = Math.hypot(
                Math.max(x, window.innerWidth - x),
                Math.max(y, window.innerHeight - y)
            );

            state.themeAnimating = true;

            if (isReduced) {
                window.RendixTheme.set(next);
                state.themeAnimating = false;
                return;
            }

            if (typeof document.startViewTransition === 'function') {
                try {
                    const transition = document.startViewTransition(() => {
                        window.RendixTheme.set(next);
                    });

                    transition.ready.then(() => {
                        document.documentElement.animate(
                            {
                                clipPath: [
                                    `circle(0px at ${x}px ${y}px)`,
                                    `circle(${endRadius}px at ${x}px ${y}px)`,
                                ],
                            },
                            {
                                duration: 680,
                                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                                pseudoElement: '::view-transition-new(root)',
                            }
                        );
                    }).catch(() => {});

                    transition.finished.then(() => {
                        state.themeAnimating = false;
                    }).catch(() => {
                        state.themeAnimating = false;
                    });

                    return;
                } catch (e) {
                    // fallback
                }
            }

            flashThemeToggle(x, y, next);
        }

        function handleClick(event) {
            const themeButton = event.target.closest('[data-rendix-theme-toggle]');
            if (themeButton) {
                toggleThemeWithAnimation(themeButton);
                return;
            }

            const mobileButton = event.target.closest('[data-mobile-menu-button]');
            if (mobileButton) {
                toggleMobileMenu();
                return;
            }

            const mobileLink = event.target.closest('#mobile-menu a');
            if (mobileLink) {
                closeMobileMenu();
            }

            const faqTrigger = event.target.closest('[data-faq-toggle]');
            if (faqTrigger) {
                toggleFaq(faqTrigger);
                return;
            }

            const backToTopButton = event.target.closest('#rx-back-to-top');
            if (backToTopButton) {
                scrollToTop();
            }
        }

        function onScroll() {
            if (state.scrollTick) return;
            state.scrollTick = true;

            requestAnimationFrame(() => {
                updateScrollProgress();
                updateBackToTop();
                state.scrollTick = false;
            });
        }

        function bindGlobalEvents() {
            if (state.eventsBound) return;
            state.eventsBound = true;

            window.addEventListener('load', markLoaded, { once: true });
            setTimeout(markLoaded, FORCE_LOAD_AFTER_MS);

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onScroll, { passive: true });

            document.addEventListener('click', handleClick);

            document.addEventListener('livewire:navigating', () => {
                state.introFinished = true;
                markIntroPlayed();

                const preloader = getPreloader();
                if (preloader) preloader.remove();

                closeMobileMenu();
            });

            document.addEventListener('livewire:navigated', () => {
                initPage();
            });
        }

        function removeStalePreloader() {
            const preloader = getPreloader();

            if (preloader && (state.introFinished || playedIntro())) {
                preloader.remove();
            }
        }

        function completeIntro() {
            if (state.introFinished) return;
            state.introFinished = true;

            markIntroPlayed();
            updateLoader(100);

            const preloader = getPreloader();

            if (preloader) {
                preloader.classList.add('is-holding');

                setTimeout(() => {
                    preloader.classList.add('is-leaving');

                    setTimeout(() => {
                        preloader.remove();
                    }, LEAVE_MS);
                }, HOLD_MS);
            }

            prepareReveals();
            observeScenes();
        }

        function runIntro() {
            const preloader = getPreloader();

            if (!preloader || playedIntro()) {
                if (preloader) preloader.remove();
                state.introFinished = true;
                prepareReveals();
                observeScenes();
                return;
            }

            const start = performance.now();

            function tick(now) {
                let value = 0;

                if (!state.pageLoaded) {
                    const p = Math.min((now - start) / PRE_LOAD_MS, 1);
                    value = Math.round(p * 85);
                } else {
                    if (!state.loadedAt) state.loadedAt = now;

                    const sinceLoad = now - state.loadedAt;
                    const p = Math.min(sinceLoad / POST_LOAD_MS, 1);
                    value = 85 + Math.round(p * 15);

                    if (p >= 1) {
                        completeIntro();
                        return;
                    }
                }

                updateLoader(value);
                requestAnimationFrame(tick);
            }

            requestAnimationFrame(tick);

            setTimeout(completeIntro, FORCE_LOAD_AFTER_MS + POST_LOAD_MS + 1500);
        }

        function initPage() {
            removeStalePreloader();

            updateScrollProgress();
            updateBackToTop();
            updateActiveNavLinks();
            closeMobileMenu();

            if (window.RendixTheme) {
                window.RendixTheme.apply(window.RendixTheme.get());
            }

            const preloaderStillExists = Boolean(getPreloader());

            if (state.introFinished || playedIntro() || !preloaderStillExists) {
                prepareReveals();
                observeScenes();
            }
        }

        function onReady() {
            if (document.readyState === 'complete') {
                markLoaded();
            }

            runIntro();
            initPage();
        }

        function boot() {
            bindGlobalEvents();

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', onReady, { once: true });
            } else {
                onReady();
            }
        }

        boot();
    })();
}
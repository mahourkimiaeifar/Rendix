{{-- =========================================================
     Rendix Copy Guard — فقط صفحات عمومی / ناحیه مشتری
     ادمین‌ها معاف‌اند؛ پنل ادمین اصلاً این را include نمی‌کند
========================================================= --}}
@php
    $guardExempt = auth()->check()
        && method_exists(auth()->user(), 'isAdmin')
        && auth()->user()->isAdmin();
@endphp

@if (! $guardExempt)
<style>
body.rx-guard-on { -webkit-user-select: none; user-select: none; }
body.rx-guard-on input,
body.rx-guard-on textarea,
body.rx-guard-on select,
body.rx-guard-on [contenteditable="true"],
body.rx-guard-on .rx-guard-selectable { -webkit-user-select: text; user-select: text; }
body.rx-guard-on img { -webkit-user-drag: none; user-drag: none; }
body.rx-guard-locked { overflow: hidden !important; }

.rx-guard-overlay {
    position: fixed;
    inset: 0;
    z-index: 2147483647;
    display: none;
    place-items: center;
    background: rgba(7, 19, 41, 0.985);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    text-align: center;
    padding: 24px;
}

.rx-guard-overlay.is-on { display: grid; }

.rx-guard-box { max-width: 540px; display: grid; gap: 14px; justify-items: center; }
.rx-guard-box i { font-size: 54px; color: #fb7185; filter: drop-shadow(0 14px 30px rgba(244,63,94,.35)); }
.rx-guard-box h2 { margin: 0; font-size: 24px; font-weight: 950; color: #F7F9FF; }
.rx-guard-box p { margin: 0; color: rgba(247,249,255,.66); line-height: 2; font-size: 14px; }

@media print {
    body * { display: none !important; }
    body::after {
        content: '© رندیکس — چاپ و تکثیر محتوای این وبسایت غیرمجاز است.';
        display: block !important;
        font-size: 22px;
        padding: 60px;
        text-align: center;
    }
}
</style>

<div id="rx-guard-overlay" class="rx-guard-overlay" role="alertdialog" aria-modal="true">
    <div class="rx-guard-box">
        <i class="fa-solid fa-shield-halved"></i>
        <h2>دسترسی مسدود شد</h2>
        <p id="rx-guard-reason">
            این وبسایت تحت حفاظت محتوا است. ابزار توسعه‌دهنده یا افزونه‌ی استخراج کد تشخیص داده شد؛
            برای ادامه، آن‌ها را ببند و صفحه را دوباره بارگذاری کن.
        </p>
    </div>
</div>

<script>
(function () {
    if (window.__rxGuard) { return; }
    window.__rxGuard = true;

    document.body.classList.add('rx-guard-on');

    var locked = false;

    function lock(reason) {
        if (locked) { return; }
        locked = true;

        document.body.classList.add('rx-guard-locked');

        var ov = document.getElementById('rx-guard-overlay');
        if (ov) {
            ov.classList.add('is-on');
            var r = document.getElementById('rx-guard-reason');
            if (r && reason) { r.textContent = reason; }
        }

        if (window.rxToast) {
            rxToast({ type: 'error', message: 'حفاظت محتوا فعال شد.' });
        }
    }

    function editable(t) {
        return t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.tagName === 'SELECT' || t.isContentEditable);
    }

    /* راست‌کلیک، انتخاب متن، کشیدن، کپی/کات */
    document.addEventListener('contextmenu', function (e) { if (!editable(e.target)) { e.preventDefault(); } });
    document.addEventListener('selectstart', function (e) { if (!editable(e.target)) { e.preventDefault(); } });
    document.addEventListener('dragstart', function (e) { e.preventDefault(); });
    document.addEventListener('copy', function (e) { if (!editable(e.target)) { e.preventDefault(); } });
    document.addEventListener('cut', function (e) { if (!editable(e.target)) { e.preventDefault(); } });

    /* میان‌برهای خطرناک */
    document.addEventListener('keydown', function (e) {
        var k = (e.key || '').toLowerCase();
        var c = e.ctrlKey || e.metaKey;

        if (e.key === 'F12') { e.preventDefault(); lock('کلید F12 در این وبسایت غیرفعال است.'); return; }
        if (c && k === 'u') { e.preventDefault(); return; }
        if (c && k === 's') { e.preventDefault(); return; }
        if (c && k === 'p') { e.preventDefault(); return; }
        if (c && e.shiftKey && (k === 'i' || k === 'j' || k === 'c')) { e.preventDefault(); lock('میان‌برهای ابزار توسعه غیرفعال‌اند.'); return; }
        if (c && k === 'a' && !editable(e.target)) { e.preventDefault(); return; }
        if (c && k === 'c' && !editable(e.target)) { e.preventDefault(); return; }
    });

    /* تشخیص DevTools از طریق اندازه پنجره */
    function sizeDevtools() {
        return (window.outerWidth - window.innerWidth > 170) ||
               (window.outerHeight - window.innerHeight > 170);
    }

    setInterval(function () {
        if (sizeDevtools()) {
            lock('ابزارهای توسعه‌دهنده باز است؛ آن‌ها را ببند و صفحه را رفرش کن.');
        }
    }, 1200);

    /* تشخیص DevTools از طریق توقف debugger */
    setInterval(function () {
        var t = performance.now();
        try { (function () { debugger; })(); } catch (e) {}
        if (performance.now() - t > 90) {
            lock('ابزارهای توسعه‌دهنده باز است.');
        }
    }, 2500);

    /* تشخیص کنسول باز (ترفند getter) */
    var probe = {};
    Object.defineProperty(probe, 'toString', {
        get: function () {
            lock('تلاش برای خواندن کد از کنسول تشخیص داده شد.');
            return 'x';
        }
    });
    setInterval(function () {
        try { console.log('%s', probe); } catch (e) {}
    }, 3000);
})();
</script>
@endif
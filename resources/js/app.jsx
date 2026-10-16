import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import ThreeBackground from './components/ThreeBackground';
import gsap from 'gsap';

// ۱. رندر صحنه سه‌بعدی اسکرولی
const canvasContainer = document.getElementById('rendix-canvas-container');
if (canvasContainer && !canvasContainer.hasChildNodes()) {
    const root = createRoot(canvasContainer);
    root.render(<ThreeBackground />);
}

// ۲. تابع جهانی تغییر دارک مود / لایت مود
window.toggleRendixTheme = function () {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
};

// ۳. حذف لودینگ اولیه و اجرای انیمیشن‌های GSAP
function initPageExperience() {
    const preloader = document.getElementById('rendix-preloader');
    if (preloader) {
        preloader.style.opacity = '0';
        setTimeout(() => preloader.style.display = 'none', 700);
    }

    gsap.fromTo(
        '.gsap-reveal',
        { y: 35, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.8, stagger: 0.12, ease: 'power3.out' }
    );
}

window.addEventListener('load', initPageExperience);
document.addEventListener('livewire:navigated', initPageExperience);
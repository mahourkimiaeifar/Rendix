import '../css/app.css';
import './runtime';

let faLoaded = false;

async function loadFontAwesome() {
    if (faLoaded) return;

    try {
        await import('@fortawesome/fontawesome-free/css/all.min.css');
        faLoaded = true;
    } catch (error) {
        console.warn('[Rendix] FontAwesome skipped:', error);
    }
}

async function loadThreeBackground() {
    try {
        const module = await import('./three');

        if (typeof module.default === 'function') {
            module.default();
        }
    } catch (error) {
        console.warn('[Rendix] Three.js background skipped:', error);
    }
}

function bootOptionalAssets() {
    loadFontAwesome();
    loadThreeBackground();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootOptionalAssets);
} else {
    bootOptionalAssets();
}

document.addEventListener('livewire:navigated', () => {
    loadFontAwesome();
    loadThreeBackground();
});
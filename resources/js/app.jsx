import './bootstrap';

import '@fontsource/vazirmatn/400.css';
import '@fontsource/vazirmatn/500.css';
import '@fontsource/vazirmatn/700.css';
import '@fontsource/vazirmatn/900.css';
import '@fontsource/jetbrains-mono/400.css';
import '@fontsource/jetbrains-mono/600.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import './runtime';

import React, { Suspense, lazy } from 'react';
import { createRoot } from 'react-dom/client';
import ErrorBoundary from './components/ErrorBoundary';

const ThreeBackground = lazy(() => import('./components/ThreeBackground'));

function CanvasFallback() {
    return (
        <div
            className="absolute inset-0"
            style={{
                background:
                    'radial-gradient(circle at 50% 40%, rgba(23,105,255,0.08), transparent 55%), radial-gradient(circle at 70% 70%, rgba(118,83,246,0.06), transparent 50%)',
            }}
        />
    );
}

function isWebGLAvailable() {
    try {
        const canvas = document.createElement('canvas');
        return !!(
            window.WebGLRenderingContext &&
            (canvas.getContext('webgl') || canvas.getContext('experimental-webgl'))
        );
    } catch (e) {
        return false;
    }
}

let reactRoot = null;

function mountThreeBackground() {
    const container = document.getElementById('rendix-canvas-container');

    if (!container || reactRoot || !isWebGLAvailable()) {
        return;
    }

    reactRoot = createRoot(container);

    reactRoot.render(
        <ErrorBoundary>
            <Suspense fallback={<CanvasFallback />}>
                <ThreeBackground />
            </Suspense>
        </ErrorBoundary>
    );
}

window.addEventListener('load', mountThreeBackground);

document.addEventListener('livewire:navigated', () => {
    mountThreeBackground();
});
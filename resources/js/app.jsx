import '@fortawesome/fontawesome-free/css/all.min.css';
import * as THREE from 'three';

/* =========================================================
   Rendix Cosmic Plexus — SOFT EDITION
   Round gentle dots, calm blue family, subtle lines
   ========================================================= */

const STATE = { cleanup: null };

function currentTheme() {
    const root = document.documentElement;
    return root.classList.contains('light') ? 'light' : 'dark';
}

/* فقط خانواده آبی/بنفش — بدون رنگ‌های جیغ */
function palette(theme) {
    return theme === 'light'
        ? [0x0ea5e9, 0x155dfc, 0x6d28d9]
        : [0x45c8ff, 0x2e7bff, 0x7653f6];
}

/* ذره گرد و نرم به‌جای مربع */
function makeDotTexture() {
    const size = 64;
    const canvas = document.createElement('canvas');
    canvas.width = size;
    canvas.height = size;

    const ctx = canvas.getContext('2d');
    const g = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2);

    g.addColorStop(0.0, 'rgba(255,255,255,1)');
    g.addColorStop(0.35, 'rgba(255,255,255,0.8)');
    g.addColorStop(0.7, 'rgba(255,255,255,0.25)');
    g.addColorStop(1.0, 'rgba(255,255,255,0)');

    ctx.fillStyle = g;
    ctx.fillRect(0, 0, size, size);

    const tex = new THREE.CanvasTexture(canvas);
    tex.needsUpdate = true;
    return tex;
}

function clearCanvases() {
    ['rx-background', 'background'].forEach((id) => {
        const el = document.getElementById(id);
        if (el) el.querySelectorAll('canvas').forEach((c) => c.remove());
    });
}

function mountPlexus() {
    if (STATE.cleanup) {
        STATE.cleanup();
        STATE.cleanup = null;
    }

    clearCanvases();

    const container =
        document.getElementById('rx-background') ||
        document.getElementById('background');

    if (!container) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced || typeof THREE === 'undefined') return;

    const canvas = document.createElement('canvas');
    canvas.setAttribute('aria-hidden', 'true');
    container.appendChild(canvas);

    let renderer;

    try {
        renderer = new THREE.WebGLRenderer({
            canvas,
            alpha: true,
            antialias: false,
            powerPreference: 'low-power',
        });
    } catch (e) {
        canvas.remove();
        return;
    }

    renderer.setClearColor(0x000000, 0);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(60, 1, 0.1, 120);
    camera.position.z = 30;

    const group = new THREE.Group();
    scene.add(group);

    const isMobile = window.innerWidth < 768;
    const COUNT = isMobile ? 26 : 55;          /* کمتر = آرام‌تر */
    const LINK_DIST = isMobile ? 7 : 8.5;

    let theme = currentTheme();
    const cols = palette(theme).map((hex) => new THREE.Color(hex));
    const dotTex = makeDotTexture();

    const positions = new Float32Array(COUNT * 3);
    const colors = new Float32Array(COUNT * 3);
    const speeds = new Float32Array(COUNT);
    const phases = new Float32Array(COUNT);

    for (let i = 0; i < COUNT; i++) {
        positions[i * 3 + 0] = (Math.random() - 0.5) * 64;
        positions[i * 3 + 1] = (Math.random() - 0.5) * 36;
        positions[i * 3 + 2] = (Math.random() - 0.5) * 14;

        const c = cols[i % cols.length];
        colors[i * 3 + 0] = c.r;
        colors[i * 3 + 1] = c.g;
        colors[i * 3 + 2] = c.b;

        speeds[i] = 0.18 + Math.random() * 0.35;   /* حرکت آهسته‌تر */
        phases[i] = Math.random() * Math.PI * 2;
    }

    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    geo.setAttribute('color', new THREE.BufferAttribute(colors, 3));

    const pointsMat = new THREE.PointsMaterial({
        size: isMobile ? 0.55 : 0.7,
        map: dotTex,
        vertexColors: true,
        transparent: true,
        opacity: theme === 'light' ? 0.28 : 0.42,   /* ملایم */
        depthWrite: false,
        blending: THREE.AdditiveBlending,
        sizeAttenuation: true,
    });

    const points = new THREE.Points(geo, pointsMat);
    group.add(points);

    const MAX_SEG = COUNT * 2;
    const linePositions = new Float32Array(MAX_SEG * 6);
    const lineColors = new Float32Array(MAX_SEG * 6);

    const lineGeo = new THREE.BufferGeometry();
    lineGeo.setAttribute('position', new THREE.BufferAttribute(linePositions, 3));
    lineGeo.setAttribute('color', new THREE.BufferAttribute(lineColors, 3));
    lineGeo.setDrawRange(0, 0);

    const lineMat = new THREE.LineBasicMaterial({
        vertexColors: true,
        transparent: true,
        opacity: theme === 'light' ? 0.06 : 0.10,   /* خط‌های خیلی کم‌رنگ */
        depthWrite: false,
        blending: THREE.AdditiveBlending,
    });

    const lines = new THREE.LineSegments(lineGeo, lineMat);
    group.add(lines);

    /* ---------- interaction (gentle) ---------- */
    const mouse = { x: 0, y: 0, tx: 0, ty: 0 };

    function onPointerMove(e) {
        mouse.tx = (e.clientX / window.innerWidth - 0.5) * 2;
        mouse.ty = (e.clientY / window.innerHeight - 0.5) * 2;
    }

    let scrollY = window.scrollY || 0;

    function onScroll() {
        scrollY = window.scrollY || 0;
    }

    function resize() {
        const w = container.clientWidth || window.innerWidth;
        const h = container.clientHeight || window.innerHeight;

        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, isMobile ? 1.5 : 1.75));
        renderer.setSize(w, h, false);

        camera.aspect = w / h;
        camera.updateProjectionMatrix();
    }

    resize();

    window.addEventListener('pointermove', onPointerMove, { passive: true });
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', resize);

    /* ---------- loop ---------- */
    let raf = 0;
    let last = 0;
    const FRAME_MS = 1000 / 40;

    function tick(now) {
        raf = requestAnimationFrame(tick);

        if (now - last < FRAME_MS) return;
        last = now;

        const t = now * 0.001;

        const curTheme = currentTheme();

        if (curTheme !== theme) {
            theme = curTheme;
            const newCols = palette(theme).map((hex) => new THREE.Color(hex));

            for (let i = 0; i < COUNT; i++) {
                const c = newCols[i % newCols.length];
                colors[i * 3 + 0] = c.r;
                colors[i * 3 + 1] = c.g;
                colors[i * 3 + 2] = c.b;
            }

            geo.attributes.color.needsUpdate = true;
            pointsMat.opacity = theme === 'light' ? 0.28 : 0.42;
            lineMat.opacity = theme === 'light' ? 0.06 : 0.10;
        }

        /* drift خیلی آرام */
        for (let i = 0; i < COUNT; i++) {
            const ix = i * 3;

            positions[ix + 0] += Math.sin(t * speeds[i] + phases[i]) * 0.006;
            positions[ix + 1] += Math.cos(t * speeds[i] * 0.8 + phases[i]) * 0.005;
            positions[ix + 2] += Math.sin(t * speeds[i] * 0.5 + phases[i]) * 0.003;

            if (positions[ix + 0] > 36) positions[ix + 0] = -36;
            if (positions[ix + 0] < -36) positions[ix + 0] = 36;
            if (positions[ix + 1] > 22) positions[ix + 1] = -22;
            if (positions[ix + 1] < -22) positions[ix + 1] = 22;
        }

        geo.attributes.position.needsUpdate = true;

        /* خطوط محو و کم‌تعداد */
        let seg = 0;

        for (let i = 0; i < COUNT && seg < MAX_SEG; i++) {
            for (let j = i + 1; j < COUNT && seg < MAX_SEG; j++) {
                const ax = positions[i * 3], ay = positions[i * 3 + 1], az = positions[i * 3 + 2];
                const bx = positions[j * 3], by = positions[j * 3 + 1], bz = positions[j * 3 + 2];

                const dx = ax - bx, dy = ay - by, dz = az - bz;
                const dist = Math.sqrt(dx * dx + dy * dy + dz * dz);

                if (dist < LINK_DIST) {
                    const o = seg * 6;
                    const fade = Math.pow(1 - dist / LINK_DIST, 1.6) * 0.6;

                    linePositions[o + 0] = ax; linePositions[o + 1] = ay; linePositions[o + 2] = az;
                    linePositions[o + 3] = bx; linePositions[o + 4] = by; linePositions[o + 5] = bz;

                    lineColors[o + 0] = colors[i * 3] * fade;
                    lineColors[o + 1] = colors[i * 3 + 1] * fade;
                    lineColors[o + 2] = colors[i * 3 + 2] * fade;
                    lineColors[o + 3] = colors[j * 3] * fade;
                    lineColors[o + 4] = colors[j * 3 + 1] * fade;
                    lineColors[o + 5] = colors[j * 3 + 2] * fade;

                    seg++;
                }
            }
        }

        lineGeo.setDrawRange(0, seg * 2);
        lineGeo.attributes.position.needsUpdate = true;
        lineGeo.attributes.color.needsUpdate = true;

        /* پارالاکس ملایم */
        mouse.x += (mouse.tx - mouse.x) * 0.03;
        mouse.y += (mouse.ty - mouse.y) * 0.03;

        group.rotation.y = mouse.x * 0.06;
        group.rotation.x = mouse.y * 0.04;
        group.position.y = -(scrollY * 0.0022);

        renderer.render(scene, camera);
    }

    raf = requestAnimationFrame(tick);

    STATE.cleanup = function () {
        cancelAnimationFrame(raf);
        window.removeEventListener('pointermove', onPointerMove);
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('resize', resize);

        geo.dispose();
        lineGeo.dispose();
        pointsMat.dispose();
        lineMat.dispose();
        dotTex.dispose();
        renderer.dispose();

        canvas.remove();
    };
}

function boot() {
    mountPlexus();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}

document.addEventListener('livewire:navigated', () => {
    mountPlexus();
});

export default {};
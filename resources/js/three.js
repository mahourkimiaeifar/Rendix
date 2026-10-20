export default async function mountThreeBackground() {
    const container = document.getElementById('rendix-canvas');

    if (!container || container.dataset.rxMounted === '1') {
        return;
    }

    try {
        const THREE = await import('three');

        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const touch = window.matchMedia('(hover: none), (pointer: coarse)').matches;

        const scene = new THREE.Scene();
        scene.fog = new THREE.Fog(0x071329, 14, 34);

        const camera = new THREE.PerspectiveCamera(
            70,
            window.innerWidth / window.innerHeight,
            0.1,
            100
        );
        camera.position.z = 16;

        const renderer = new THREE.WebGLRenderer({
            alpha: true,
            antialias: !touch,
            powerPreference: 'high-performance',
        });

        renderer.setPixelRatio(Math.min(window.devicePixelRatio, touch ? 1.5 : 2));
        renderer.setSize(window.innerWidth, window.innerHeight);
        container.appendChild(renderer.domElement);
        container.dataset.rxMounted = '1';

        const count = touch ? 700 : 1500;
        const positions = new Float32Array(count * 3);

        for (let i = 0; i < count; i++) {
            positions[i * 3] = (Math.random() - 0.5) * 50;
            positions[i * 3 + 1] = (Math.random() - 0.5) * 50;
            positions[i * 3 + 2] = (Math.random() - 0.5) * 26;
        }

        const geometry = new THREE.BufferGeometry();
        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

        const material = new THREE.PointsMaterial({
            color: 0x45c8ff,
            size: 0.055,
            transparent: true,
            opacity: 0.45,
            sizeAttenuation: true,
            depthWrite: false,
        });

        const points = new THREE.Points(geometry, material);
        scene.add(points);

        const lineMaterial = new THREE.LineBasicMaterial({
            color: 0x1769ff,
            transparent: true,
            opacity: 0.08,
        });

        for (let i = -4; i <= 4; i += 2) {
            const lineGeometry = new THREE.BufferGeometry().setFromPoints([
                new THREE.Vector3(-30, i * 2, -8),
                new THREE.Vector3(30, i * 2, -8),
            ]);

            scene.add(new THREE.Line(lineGeometry, lineMaterial));
        }

        let mouseX = 0;

        if (!touch) {
            window.addEventListener(
                'mousemove',
                (event) => {
                    mouseX = (event.clientX / window.innerWidth - 0.5) * 2;
                },
                { passive: true }
            );
        }

        window.addEventListener('rendix-theme-changed', (event) => {
            const isLight = event.detail?.isLight;

            material.color.set(isLight ? 0x1769ff : 0x45c8ff);
            material.opacity = isLight ? 0.22 : 0.45;
            lineMaterial.opacity = isLight ? 0.05 : 0.08;
        });

        const clock = new THREE.Clock();

        const animate = () => {
            requestAnimationFrame(animate);

            const elapsed = clock.getElapsedTime();

            if (!reduced) {
                points.rotation.y = elapsed * 0.012;
                points.rotation.x = Math.sin(elapsed * 0.05) * 0.04;
                camera.position.x += (mouseX * 1.2 - camera.position.x) * 0.02;
                camera.lookAt(0, 0, 0);
            }

            renderer.render(scene, camera);
        };

        animate();

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    } catch (error) {
        console.warn('[Rendix] Three background failed:', error);
    }
}
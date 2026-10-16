import React, { useEffect, useMemo, useRef, useState, memo } from 'react';
import * as THREE from 'three';
import { Canvas, useFrame } from '@react-three/fiber';

const PALETTE = {
    navy: '#071329',
    primary: '#1769FF',
    light: '#45C8FF',
    purple: '#7653F6',
    white: '#F7F9FF',
};

function getParticleCount() {
    const width = window.innerWidth;
    const cores = navigator.hardwareConcurrency || 4;

    let count;

    if (width < 640) count = 34;
    else if (width < 1024) count = 58;
    else if (width < 1440) count = 82;
    else count = 105;

    if (cores <= 4) count = Math.round(count * 0.8);
    if (cores <= 2) count = Math.round(count * 0.65);

    return Math.max(28, count);
}

function getMaxLines() {
    return window.innerWidth < 768 ? 110 : 220;
}

function getSkipFrames() {
    return window.innerWidth < 1024 ? 4 : 3;
}

function useReducedMotion() {
    const [reduced, setReduced] = useState(() =>
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    );

    useEffect(() => {
        const mq = window.matchMedia('(prefers-reduced-motion: reduce)');
        const onChange = () => setReduced(mq.matches);
        mq.addEventListener('change', onChange);
        return () => mq.removeEventListener('change', onChange);
    }, []);

    return reduced;
}

function useTheme() {
    const [isLight, setIsLight] = useState(() =>
        document.documentElement.classList.contains('light')
    );

    useEffect(() => {
        const sync = () => {
            setIsLight(document.documentElement.classList.contains('light'));
        };

        const observer = new MutationObserver(sync);
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class'],
        });

        const onThemeChanged = (event) => {
            if (typeof event?.detail?.isLight === 'boolean') {
                setIsLight(event.detail.isLight);
            }
        };

        window.addEventListener('rendix-theme-changed', onThemeChanged);

        return () => {
            observer.disconnect();
            window.removeEventListener('rendix-theme-changed', onThemeChanged);
        };
    }, []);

    return isLight;
}

function ParticleNetwork({ isLight, particleCount }) {
    const groupRef = useRef();
    const linesGeometryRef = useRef();
    const pointsMaterialRef = useRef();
    const lineMaterialRef = useRef();

    const frameRef = useRef(0);
    const lineInitRef = useRef(false);
    const reducedMotion = useReducedMotion();
    const skipFrames = useRef(getSkipFrames());
    const maxLines = useRef(getMaxLines());

    const sceneRef = useRef({
        color: new THREE.Color(PALETTE.light),
        target: new THREE.Color(PALETTE.light),
        intensity: 1,
        speed: 1,
    });

    const tmpColor = useRef(new THREE.Color());
    const lightMixColor = useRef(new THREE.Color(PALETTE.primary));

    const maxDistance = 2.8;
    const maxDistSq = maxDistance * maxDistance;

    const { positions, velocities } = useMemo(() => {
        const pos = new Float32Array(particleCount * 3);
        const vel = [];

        for (let i = 0; i < particleCount; i++) {
            pos[i * 3] = (Math.random() - 0.5) * 18;
            pos[i * 3 + 1] = (Math.random() - 0.5) * 12;
            pos[i * 3 + 2] = (Math.random() - 0.5) * 6;

            vel.push({
                x: (Math.random() - 0.5) * 0.0075,
                y: (Math.random() - 0.5) * 0.0075,
                z: (Math.random() - 0.5) * 0.0038,
            });
        }

        return { positions: pos, velocities: vel };
    }, [particleCount]);

    const linePositions = useMemo(() => new Float32Array(maxLines.current * 6), []);

    useEffect(() => {
        const onScene = (event) => {
            const detail = event?.detail || {};
            const s = sceneRef.current;

            if (detail.color) {
                s.target.set(detail.color);
            }

            s.intensity = typeof detail.intensity === 'number' ? detail.intensity : 1;
            s.speed = typeof detail.speed === 'number' ? detail.speed : 1;
        };

        window.addEventListener('rendix-scene', onScene);

        return () => {
            window.removeEventListener('rendix-scene', onScene);
        };
    }, []);

    useFrame((state) => {
        const s = sceneRef.current;

        s.color.lerp(s.target, reducedMotion ? 0.02 : 0.035);
        tmpColor.current.copy(s.color);

        if (isLight) {
            tmpColor.current.lerp(lightMixColor.current, 0.28);
        }

        const clampedIntensity = Math.min(Math.max(s.intensity, 0.8), 1.25);
        const clampedSpeed = Math.min(Math.max(s.speed, 0.7), 1.2);

        if (pointsMaterialRef.current) {
            pointsMaterialRef.current.color.copy(tmpColor.current);

            const targetPointOpacity = (isLight ? 0.55 : 0.86) * clampedIntensity;
            const targetPointSize = (isLight ? 0.062 : 0.082) * (0.95 + clampedIntensity * 0.08);

            pointsMaterialRef.current.opacity +=
                (Math.min(targetPointOpacity, 1) - pointsMaterialRef.current.opacity) * 0.06;

            pointsMaterialRef.current.size +=
                (targetPointSize - pointsMaterialRef.current.size) * 0.06;
        }

        if (lineMaterialRef.current) {
            lineMaterialRef.current.color.copy(tmpColor.current);

            const targetLineOpacity = (isLight ? 0.16 : 0.28) * clampedIntensity;

            lineMaterialRef.current.opacity +=
                (Math.min(targetLineOpacity, 1) - lineMaterialRef.current.opacity) * 0.06;
        }

        const shouldMove = !reducedMotion && !document.hidden;

        if (shouldMove) {
            frameRef.current += 1;

            if (frameRef.current % skipFrames.current === 0) {
                for (let i = 0; i < particleCount; i++) {
                    positions[i * 3] += velocities[i].x * clampedSpeed;
                    positions[i * 3 + 1] += velocities[i].y * clampedSpeed;
                    positions[i * 3 + 2] += velocities[i].z * clampedSpeed;

                    if (Math.abs(positions[i * 3]) > 9) velocities[i].x *= -1;
                    if (Math.abs(positions[i * 3 + 1]) > 6) velocities[i].y *= -1;
                    if (Math.abs(positions[i * 3 + 2]) > 3) velocities[i].z *= -1;
                }

                let vertexPos = 0;
                let connectedLines = 0;

                for (let i = 0; i < particleCount && connectedLines < maxLines.current; i++) {
                    for (let j = i + 1; j < particleCount && connectedLines < maxLines.current; j++) {
                        const dx = positions[i * 3] - positions[j * 3];
                        const dy = positions[i * 3 + 1] - positions[j * 3 + 1];
                        const dz = positions[i * 3 + 2] - positions[j * 3 + 2];
                        const distSq = dx * dx + dy * dy + dz * dz;

                        if (distSq < maxDistSq) {
                            linePositions[vertexPos++] = positions[i * 3];
                            linePositions[vertexPos++] = positions[i * 3 + 1];
                            linePositions[vertexPos++] = positions[i * 3 + 2];

                            linePositions[vertexPos++] = positions[j * 3];
                            linePositions[vertexPos++] = positions[j * 3 + 1];
                            linePositions[vertexPos++] = positions[j * 3 + 2];

                            connectedLines += 1;
                        }
                    }
                }

                const pointsGeometry = groupRef.current?.children?.[0]?.geometry;
                if (pointsGeometry?.attributes?.position) {
                    pointsGeometry.attributes.position.needsUpdate = true;
                }

                if (linesGeometryRef.current) {
                    linesGeometryRef.current.setDrawRange(0, connectedLines * 2);

                    if (linesGeometryRef.current.attributes?.position) {
                        linesGeometryRef.current.attributes.position.needsUpdate = true;
                    }
                }

                lineInitRef.current = true;
            }
        } else if (!lineInitRef.current) {
            let vertexPos = 0;
            let connectedLines = 0;

            for (let i = 0; i < particleCount && connectedLines < maxLines.current; i++) {
                for (let j = i + 1; j < particleCount && connectedLines < maxLines.current; j++) {
                    const dx = positions[i * 3] - positions[j * 3];
                    const dy = positions[i * 3 + 1] - positions[j * 3 + 1];
                    const dz = positions[i * 3 + 2] - positions[j * 3 + 2];
                    const distSq = dx * dx + dy * dy + dz * dz;

                    if (distSq < maxDistSq) {
                        linePositions[vertexPos++] = positions[i * 3];
                        linePositions[vertexPos++] = positions[i * 3 + 1];
                        linePositions[vertexPos++] = positions[i * 3 + 2];

                        linePositions[vertexPos++] = positions[j * 3];
                        linePositions[vertexPos++] = positions[j * 3 + 1];
                        linePositions[vertexPos++] = positions[j * 3 + 2];

                        connectedLines += 1;
                    }
                }
            }

            if (linesGeometryRef.current) {
                linesGeometryRef.current.setDrawRange(0, connectedLines * 2);

                if (linesGeometryRef.current.attributes?.position) {
                    linesGeometryRef.current.attributes.position.needsUpdate = true;
                }
            }

            lineInitRef.current = true;
        }

        if (!shouldMove || !groupRef.current) return;

        const scrollY = window.scrollY || 0;
        const pointerInfluence = 0.28 * clampedIntensity;
        const targetX = state.pointer.x * pointerInfluence;
        const targetY = state.pointer.y * (0.18 * clampedIntensity) + scrollY * 0.0008;

        const rotationLerp = 0.028 * clampedSpeed;

        groupRef.current.rotation.y += (targetX - groupRef.current.rotation.y) * rotationLerp;
        groupRef.current.rotation.x += (-targetY - groupRef.current.rotation.x) * rotationLerp;

        const t = state.clock.getElapsedTime();
        const breathe = 1 + Math.sin(t * 0.18) * 0.012;
        groupRef.current.scale.setScalar(breathe);
    });

    return (
        <group ref={groupRef}>
            <points>
                <bufferGeometry>
                    <bufferAttribute attach="attributes-position" args={[positions, 3]} />
                </bufferGeometry>

                <pointsMaterial
                    ref={pointsMaterialRef}
                    size={isLight ? 0.062 : 0.082}
                    color={isLight ? PALETTE.primary : PALETTE.light}
                    transparent
                    opacity={isLight ? 0.55 : 0.86}
                    sizeAttenuation
                    depthWrite={false}
                    blending={isLight ? THREE.NormalBlending : THREE.AdditiveBlending}
                />
            </points>

            <lineSegments>
                <bufferGeometry ref={linesGeometryRef}>
                    <bufferAttribute attach="attributes-position" args={[linePositions, 3]} />
                </bufferGeometry>

                <lineBasicMaterial
                    ref={lineMaterialRef}
                    color={isLight ? PALETTE.purple : PALETTE.primary}
                    transparent
                    opacity={isLight ? 0.16 : 0.28}
                    depthWrite={false}
                    blending={isLight ? THREE.NormalBlending : THREE.AdditiveBlending}
                />
            </lineSegments>
        </group>
    );
}

function ThreeBackground() {
    const isLight = useTheme();
    const [particleCount] = useState(getParticleCount);

    const dpr = useMemo(() => {
        return window.innerWidth < 1024 ? [1, 1] : [1, 1.2];
    }, []);

    return (
        <>
            <div
                className="absolute inset-0 z-10 pointer-events-none"
                style={{
                    background: isLight
                        ? 'radial-gradient(circle at 50% 45%, transparent 0%, rgba(247,249,255,0.42) 52%, rgba(247,249,255,0.78) 100%)'
                        : 'radial-gradient(circle at 50% 45%, transparent 0%, rgba(7,19,41,0.46) 52%, rgba(7,19,41,0.84) 100%)',
                    transition: 'background 0.5s ease',
                }}
            />

            <Canvas
                className="absolute inset-0 z-0"
                camera={{ position: [0, 0, 7], fov: 58 }}
                dpr={dpr}
                gl={{
                    antialias: false,
                    alpha: true,
                    powerPreference: 'high-performance',
                    stencil: false,
                }}
            >
                <ParticleNetwork isLight={isLight} particleCount={particleCount} />
            </Canvas>
        </>
    );
}

export default memo(ThreeBackground);
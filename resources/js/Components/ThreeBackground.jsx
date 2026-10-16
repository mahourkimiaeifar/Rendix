import React, { useRef, useMemo, useEffect, useState } from 'react';
import { Canvas, useFrame } from '@react-three/fiber';
import * as THREE from 'three';

function ParticleNetwork({ isDark }) {
    const groupRef = useRef();
    const linesGeometryRef = useRef();
    
    const particleCount = 110;
    const maxDistance = 2.6;

    // تولید موقعیت اولیه و سرعت حرکت هر نقطه در فضای سه بعدی
    const { positions, velocities } = useMemo(() => {
        const pos = new Float32Array(particleCount * 3);
        const vel = [];
        for (let i = 0; i < particleCount; i++) {
            pos[i * 3] = (Math.random() - 0.5) * 18;
            pos[i * 3 + 1] = (Math.random() - 0.5) * 12;
            pos[i * 3 + 2] = (Math.random() - 0.5) * 6;
            vel.push({
                x: (Math.random() - 0.5) * 0.008,
                y: (Math.random() - 0.5) * 0.008,
                z: (Math.random() - 0.5) * 0.004,
            });
        }
        return { positions: pos, velocities: vel };
    }, []);

    // بافر مخصوص خطوط اتصال بین نقاط
    const maxLines = particleCount * particleCount;
    const linePositions = useMemo(() => new Float32Array(maxLines * 6), [maxLines]);

    useFrame((state) => {
        if (!groupRef.current) return;

        // ۱. بروزرسانی موقعیت نقاط و برگشت از لبه‌ها
        for (let i = 0; i < particleCount; i++) {
            positions[i * 3] += velocities[i].x;
            positions[i * 3 + 1] += velocities[i].y;
            positions[i * 3 + 2] += velocities[i].z;

            if (Math.abs(positions[i * 3]) > 9) velocities[i].x *= -1;
            if (Math.abs(positions[i * 3 + 1]) > 6) velocities[i].y *= -1;
            if (Math.abs(positions[i * 3 + 2]) > 3) velocities[i].z *= -1;
        }

        // ۲. محاسبه فاصله نقاط از هم؛ وصل شدن نقاط نزدیک و قطع شدن نقاط دور
        let vertexpos = 0;
        let connectedLines = 0;

        for (let i = 0; i < particleCount; i++) {
            for (let j = i + 1; j < particleCount; j++) {
                const dx = positions[i * 3] - positions[j * 3];
                const dy = positions[i * 3 + 1] - positions[j * 3 + 1];
                const dz = positions[i * 3 + 2] - positions[j * 3 + 2];
                const dist = Math.sqrt(dx * dx + dy * dy + dz * dz);

                if (dist < maxDistance) {
                    linePositions[vertexpos++] = positions[i * 3];
                    linePositions[vertexpos++] = positions[i * 3 + 1];
                    linePositions[vertexpos++] = positions[i * 3 + 2];

                    linePositions[vertexpos++] = positions[j * 3];
                    linePositions[vertexpos++] = positions[j * 3 + 1];
                    linePositions[vertexpos++] = positions[j * 3 + 2];
                    connectedLines++;
                }
            }
        }

        // بروزرسانی GPU
        const pointsAttr = groupRef.current.children[0].geometry.attributes.position;
        pointsAttr.needsUpdate = true;

        if (linesGeometryRef.current) {
            linesGeometryRef.current.setDrawRange(0, connectedLines * 2);
            linesGeometryRef.current.attributes.position.needsUpdate = true;
        }

        // ۳. حس شناوری و کنترل با موس و اسکرول
        const scrollY = window.scrollY || 0;
        const targetX = state.pointer.x * 0.35;
        const targetY = state.pointer.y * 0.25 + (scrollY * 0.0012);

        groupRef.current.rotation.y += (targetX - groupRef.current.rotation.y) * 0.04;
        groupRef.current.rotation.x += (-targetY - groupRef.current.rotation.x) * 0.04;
    });

    const pointColor = isDark ? '#22d3ee' : '#0284c7';
    const lineColor = isDark ? '#0891b2' : '#38bdf8';

    return (
        <group ref={groupRef}>
            {/* نقاط شبکه */}
            <points>
                <bufferGeometry>
                    <bufferAttribute
                        attach="attributes-position"
                        count={particleCount}
                        array={positions}
                        itemSize={3}
                    />
                </bufferGeometry>
                <pointsMaterial
                    size={0.07}
                    color={pointColor}
                    transparent
                    opacity={isDark ? 0.75 : 0.6}
                    sizeAttenuation
                />
            </points>

            {/* خطوط اتصال بین نقاط */}
            <lineSegments>
                <bufferGeometry ref={linesGeometryRef}>
                    <bufferAttribute
                        attach="attributes-position"
                        count={linePositions.length / 3}
                        array={linePositions}
                        itemSize={3}
                    />
                </bufferGeometry>
                <lineBasicMaterial
                    color={lineColor}
                    transparent
                    opacity={isDark ? 0.16 : 0.14}
                />
            </lineSegments>
        </group>
    );
}

export default function ThreeBackground() {
    const [isDark, setIsDark] = useState(true);

    useEffect(() => {
        const checkTheme = () => {
            setIsDark(document.documentElement.classList.contains('dark'));
        };
        checkTheme();

        const observer = new MutationObserver(checkTheme);
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        return () => observer.disconnect();
    }, []);

    return (
        <div className="fixed inset-0 z-0 pointer-events-none">
            {/* لایه گرادینت محو برای ایجاد کنتراست عالی زیر متن‌ها */}
            <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(248,250,252,0.85)_100%)] dark:bg-[radial-gradient(circle_at_center,transparent_0%,rgba(2,6,23,0.9)_100%)] z-10 transition-colors duration-500" />
            
            <Canvas camera={{ position: [0, 0, 7], fov: 60 }}>
                <ParticleNetwork isDark={isDark} />
            </Canvas>
        </div>
    );
}
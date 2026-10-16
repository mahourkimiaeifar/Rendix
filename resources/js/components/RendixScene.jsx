import React, { useRef, useEffect, useState } from 'react';
import { Canvas, useFrame } from '@react-three/fiber';
import { Float, MeshDistortMaterial, Sparkles, PerspectiveCamera } from '@react-three/drei';
import gsap from 'gsap';

function CyberCore({ activeColor }) {
    const meshRef = useRef();

    useFrame((state, delta) => {
        if (!meshRef.current) return;
        meshRef.current.rotation.x += delta * 0.25;
        meshRef.current.rotation.y += delta * 0.35;

        gsap.to(meshRef.current.position, {
            x: state.pointer.x * 1.2,
            y: state.pointer.y * 0.8,
            duration: 1.5,
            ease: 'power2.out',
        });
    });

    useEffect(() => {
        if (!meshRef.current) return;
        gsap.fromTo(
            meshRef.current.scale,
            { x: 1.4, y: 1.4, z: 1.4 },
            { x: 1, y: 1, z: 1, duration: 1.2, ease: 'elastic.out(1, 0.4)' }
        );
    }, [activeColor]);

    return (
        <Float speed={2.5} rotationIntensity={1.5} floatIntensity={2}>
            <mesh ref={meshRef}>
                <icosahedronGeometry args={[1.8, 12]} />
                <MeshDistortMaterial
                    color={activeColor}
                    emissive={activeColor}
                    emissiveIntensity={0.35}
                    roughness={0.15}
                    metalness={0.85}
                    distort={0.45}
                    speed={3}
                />
            </mesh>
        </Float>
    );
}

export default function RendixScene() {
    const [coreColor, setCoreColor] = useState('#06b6d4');

    useEffect(() => {
        const handleModeChange = (event) => {
            if (event.detail && event.detail.color) {
                setCoreColor(event.detail.color);
            }
        };
        window.addEventListener('rendix-mode-changed', handleModeChange);
        return () => window.removeEventListener('rendix-mode-changed', handleModeChange);
    }, []);

    return (
        <Canvas>
            <PerspectiveCamera makeDefault position={[0, 0, 6]} />
            <ambientLight intensity={0.6} />
            <directionalLight position={[5, 5, 5]} intensity={2} />
            <pointLight position={[-5, -5, -5]} intensity={3} color={coreColor} />
            <Sparkles count={120} scale={10} size={2} speed={0.6} color={coreColor} />
            <CyberCore activeColor={coreColor} />
        </Canvas>
    );
}
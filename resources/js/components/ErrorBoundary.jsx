import React from 'react';

export default class ErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false };
    }

    static getDerivedStateFromError() {
        return { hasError: true };
    }

    componentDidCatch(error, info) {
        console.warn('[Rendix 3D] Error:', error, info);
    }

    render() {
        if (this.state.hasError) {
            return (
                <div
                    className="absolute inset-0"
                    style={{
                        background:
                            'radial-gradient(circle at 30% 20%, rgba(23,105,255,0.10), transparent 45%), radial-gradient(circle at 70% 70%, rgba(118,83,246,0.08), transparent 50%)',
                    }}
                />
            );
        }

        return this.props.children;
    }
}
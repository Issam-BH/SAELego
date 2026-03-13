import React from 'react';

export default function Timer({ timeLeft }) {
    const isWarning = timeLeft <= 5; // Devient rouge quand il reste 5 secondes

    return (
        <div className="timer" style={{ color: isWarning ? '#e74c3c' : '#2c3e50', fontWeight: 'bold' }}>
            Temps restant pour ce tour : <span>{timeLeft} s</span>
        </div>
    );
}
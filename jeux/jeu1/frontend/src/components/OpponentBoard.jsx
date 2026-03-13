import React from 'react';
import GameBoard from './GameBoard';

export default function OpponentBoard({ grid, score }) {
    return (
        <div className="opponent-board" style={{ border: '2px dashed #95a5a6', padding: '10px', borderRadius: '8px' }}>
            <h3 style={{ color: '#7f8c8d' }}>Adversaire</h3>
            <p>Score actuel : {score}</p>
            <GameBoard grid={grid} disabled={true} />
        </div>
    );
}
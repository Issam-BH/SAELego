import React, { useState, useEffect } from 'react';
import { io } from 'socket.io-client';
import './App.css';

const params = new URLSearchParams(window.location.search);
const fidelityId = params.get('fidelityId') || 'guest_' + Math.floor(Math.random() * 10000);
const socket = io('http://localhost:3002', { query: { fidelityId } });

export default function App() {
    const [grid, setGrid] = useState([]);
    const [score, setScore] = useState(0);
    const [currentBrick, setCurrentBrick] = useState(null);
    const [isGameOver, setIsGameOver] = useState(false);
    const [abandoned, setAbandoned] = useState(false);

    useEffect(() => {
        const onGameState = (state) => {
            setGrid(state.grid);
            setScore(state.score);
            setCurrentBrick(state.currentBrick);
            setIsGameOver(state.isGameOver);
            setAbandoned(state.abandoned);
        };

        const onInvalidMove = (err) => {
            alert(err.message);
        };

        socket.on('game_state', onGameState);
        socket.on('invalid_move', onInvalidMove);

        return () => {
            socket.off('game_state', onGameState);
            socket.off('invalid_move', onInvalidMove);
        };
    }, []);

    const rotateBrick = () => {
        if (!currentBrick || isGameOver) return;
        const matrix = currentBrick.matrix;
        const rotated = matrix[0].map((val, index) => 
            matrix.map(row => row[index]).reverse()
        );
        setCurrentBrick({ ...currentBrick, matrix: rotated });
    };

    const handleCellClick = (x, y) => {
        if (!currentBrick || isGameOver) return;
        socket.emit('place_brick', {
            x,
            y,
            matrix: currentBrick.matrix,
            color: currentBrick.color
        });
    };

    const handleRestart = () => {
        socket.emit('restart_game');
    };

    const handleAbandon = () => {
        socket.emit('abandon_game');
    };

    if (grid.length === 0) return <div>Chargement du jeu...</div>;

    return (
        <div className="game-container">
            <h1>Lego : Casse-Briques de Lignes</h1>
            <div className="score-board">Score : {score} pts</div>

            {isGameOver && (
                <div className="game-over">
                    <h2>Partie terminee</h2>
                    <p>{abandoned ? "Vous avez abandonne la partie." : "Aucun emplacement disponible pour la brique."}</p>
                    <p>Score enregistre pour l'identifiant : {fidelityId}</p>
                    <button className="restart-btn" onClick={handleRestart}>
                        Rejouer
                    </button>
                </div>
            )}

            <div className="layout">
                <div className="board">
                    {grid.map((row, y) => (
                        <div key={y} className="row">
                            {row.map((cellColor, x) => (
                                <div
                                    key={`${x}-${y}`}
                                    className="cell"
                                    style={{ backgroundColor: cellColor || '#e0e0e0' }}
                                    onClick={() => handleCellClick(x, y)}
                                />
                            ))}
                        </div>
                    ))}
                </div>

                <div className="sidebar">
                    <h3>Prochaine Brique</h3>
                    <div className="preview">
                        {currentBrick && currentBrick.matrix.map((row, r) => (
                            <div key={r} className="row">
                                {row.map((isFilled, c) => (
                                    <div
                                        key={c}
                                        className="cell preview-cell"
                                        style={{
                                            backgroundColor: isFilled ? currentBrick.color : 'transparent',
                                            border: isFilled ? '2px solid #333' : 'none'
                                        }}
                                    />
                                ))}
                            </div>
                        ))}
                    </div>
                    <button className="action-btn" onClick={rotateBrick} disabled={isGameOver}>
                        Pivoter la brique
                    </button>
                    <button className="action-btn abandon-btn" onClick={handleAbandon} disabled={isGameOver}>
                        Abandonner
                    </button>
                </div>
            </div>
        </div>
    );
}
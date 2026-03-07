import React, { useState, useEffect } from 'react';
import io from 'socket.io-client';
import GameBoard from './components/GameBoard';

const socket = io('http://localhost:3001');

export default function App() {
    const [fidelityId, setFidelityId] = useState('');
    const [gameState, setGameState] = useState('menu');
    const [currentBrick, setCurrentBrick] = useState(null);
    const [myGrid, setMyGrid] = useState([]);
    const [targetGrid, setTargetGrid] = useState([]);
    const [score, setScore] = useState(0);
    const [timeLeft, setTimeLeft] = useState(0);

    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const fid = params.get('fid') || 'GUEST_' + Math.floor(Math.random() * 10000);
        setFidelityId(fid);

        socket.on('game_started', (data) => {
            setGameState('playing');
            setTargetGrid(data.targetGrid);
            setMyGrid(data.targetGrid.map(row => row.map(() => null)));
        });

        socket.on('new_turn', (data) => {
            setCurrentBrick(data.brick);
            setTimeLeft(data.timeLimit / 1000);
        });

        socket.on('grid_updated', (data) => {
            if (data.playerId === socket.id) {
                setMyGrid(data.grid);
                setScore(data.score);
            }
        });

        socket.on('game_over', () => setGameState('ended'));

        return () => {
            socket.off('game_started');
            socket.off('new_turn');
            socket.off('grid_updated');
            socket.off('game_over');
        };
    }, []);

    useEffect(() => {
        if (timeLeft > 0 && gameState === 'playing') {
            const timerId = setTimeout(() => setTimeLeft(timeLeft - 1), 1000);
            return () => clearTimeout(timerId);
        }
    }, [timeLeft, gameState]);

    const joinGame = () => socket.emit('join_game', { fidelityId, mode: 'solo' });

    const handleCellClick = (x, y) => {
        if (!currentBrick) return;

        // Le clic représente le coin "En haut à gauche" de la brique.
        // Le serveur fera les vérifications pour savoir si ça rentre
        socket.emit('place_brick', { x, y, brick: currentBrick });
        setCurrentBrick(null);
    };

    if (gameState === 'menu') {
        return (
            <div className="container">
                <h1>Fidélité Lego</h1>
                <p>Votre ID : {fidelityId}</p>
                <button className="btn" onClick={joinGame}>Lancer le Jeu 1 (Reproduction)</button>
            </div>
        );
    }

    if (gameState === 'ended') {
        return (
            <div className="container">
                <h1>Partie Terminée !</h1>
                <h2>Vous avez gagné {score} points de fidélité.</h2>
                <button className="btn" onClick={() => window.location.reload()}>Rejouer</button>
            </div>
        );
    }

    return (
        <div className="container">
            <header className="game-header">
                <div>Score : {score}</div>
                {currentBrick && <div className="timer">Temps restant : {timeLeft}s</div>}
            </header>

            <div className="boards-container">
                <div className="board-section">
                    <h3>Image Cible</h3>
                    <GameBoard grid={targetGrid} disabled={true} />
                </div>

                <div className="board-section">
                    <h3>Votre Tableau</h3>
                    
                    <div style={{ minHeight: '100px', marginBottom: '15px' }}>
                        {currentBrick ? (
                            <div style={{
                                backgroundColor: currentBrick.color,
                                padding: '15px',
                                border: '3px solid #2c3e50',
                                borderRadius: '8px',
                                color: ['yellow', 'white'].includes(currentBrick.color) ? 'black' : 'white',
                                fontWeight: 'bold',
                                fontSize: '18px',
                                textAlign: 'center',
                                boxShadow: '0 4px 6px rgba(0,0,0,0.3)',
                                display: 'inline-block'
                            }}>
                                Brique en main : {currentBrick.color} <br/>
                                <span style={{ fontSize: '22px' }}>Forme : {currentBrick.shape}</span> <br/>
                                <span style={{ fontSize: '12px', fontWeight: 'normal' }}>
                                    (Cliquez sur le coin haut-gauche de l'emplacement)
                                </span>
                            </div>
                        ) : (
                            <div style={{ color: '#7f8c8d', fontStyle: 'italic', padding: '15px', textAlign: 'center' }}>
                                En attente...
                            </div>
                        )}
                    </div>

                    <GameBoard grid={myGrid} onCellClick={handleCellClick} />
                </div>
            </div>
        </div>
    );
}
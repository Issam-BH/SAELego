import React, { useState, useEffect, useRef } from 'react';
import { io } from 'socket.io-client';
import './App.css';

const params = new URLSearchParams(window.location.search);
const fidelityId = params.get('fidelityId') || 'guest_' + Math.floor(Math.random() * 10000);
const socket = io('http://localhost:4002', { query: { fidelityId } });

export default function App() {
    const [view, setView] = useState('menu');
    const [pseudo, setPseudo] = useState('');
    const [roomCode, setRoomCode] = useState('');
    const [inputCode, setInputCode] = useState('');
    const [isAdmin, setIsAdmin] = useState(false);
    const [playersCount, setPlayersCount] = useState(1);
    const [gameMode, setGameMode] = useState('');
    const [gameState, setGameState] = useState(null);
    const [currentBrick, setCurrentBrick] = useState(null);
    const [timeLimit, setTimeLimit] = useState(0);
    const [isGameOver, setIsGameOver] = useState(false);
    const [winnerData, setWinnerData] = useState(null);
    const [chatMessages, setChatMessages] = useState([]);
    const [chatInput, setChatInput] = useState('');

    const timerRef = useRef(null);

    useEffect(() => {
        socket.on('room_created', (code) => { setRoomCode(code); setIsAdmin(true); setView('lobby'); });
        socket.on('room_joined', (code) => { setRoomCode(code); setView('lobby'); });
        socket.on('room_update', (data) => { setPlayersCount(data.players); });
        socket.on('receive_chat', (msg) => { setChatMessages(prev => [...prev, { fromMe: false, text: msg.text, sender: msg.senderPseudo }]); });
        socket.on('game_started', (data) => { setGameState(data.players); setGameMode(data.mode); setRoomCode(data.code); setView('playing'); });
        socket.on('personal_game_over', () => { alert("Plus de place ! En attente de l'adversaire..."); });
        socket.on('invalid_move', (data) => { alert(data.message); setGameState(data.players); });
        socket.on('place_success', () => { setCurrentBrick(null); });
        socket.on('state_update', (data) => { setGameState(data.players); });
        
        socket.on('room_game_over', (data) => {
            setIsGameOver(true);
            setWinnerData(data);
            if (timerRef.current) clearInterval(timerRef.current);
        });

        socket.on('turn_started', (data) => {
            setCurrentBrick(data.brick);
            if (data.timeLimit !== null) {
                setTimeLimit(data.timeLimit);
                if (timerRef.current) clearInterval(timerRef.current);
                timerRef.current = setInterval(() => { setTimeLimit(prev => prev > 0 ? prev - 1 : 0); }, 1000);
            }
        });

        return () => socket.off();
    }, []);

    const startSolo = () => { if (!pseudo.trim()) return alert('Pseudo requis'); socket.emit('start_solo', pseudo); };
    const createRoom = () => { if (!pseudo.trim()) return alert('Pseudo requis'); socket.emit('create_room', pseudo); };
    const joinRoom = () => { if (!pseudo.trim() || !inputCode) return alert('Champs requis'); socket.emit('join_room', { code: inputCode.toUpperCase(), pseudo }); };
    const startGame = () => socket.emit('start_game', roomCode);
    const handleAbandon = () => { if (window.confirm("Abandonner la partie ?")) socket.emit('abandon_game', roomCode); };

    const sendChat = (e) => {
        e.preventDefault();
        if (!chatInput.trim()) return;
        socket.emit('send_chat', { code: roomCode, message: chatInput });
        setChatMessages(prev => [...prev, { fromMe: true, text: chatInput, sender: pseudo }]);
        setChatInput('');
    };

    const rotateBrick = () => {
        if (!currentBrick || isGameOver) return;
        const matrix = currentBrick.matrix;
        const rotated = matrix[0].map((val, index) => matrix.map(row => row[index]).reverse());
        setCurrentBrick({ ...currentBrick, matrix: rotated });
    };

    const handleCellClick = (x, y) => {
        if (!currentBrick || isGameOver || !gameState || gameState[socket.id]?.isGameOver || gameState[socket.id]?.hasPlaced) return;
        socket.emit('place_brick', { code: roomCode, x, y, matrix: currentBrick.matrix, color: currentBrick.color });
    };

    if (view === 'menu') {
        return (
            <div className="container menu-container">
                <h1>Lego Casse-Briques</h1>
                <div className="join-section"><input type="text" placeholder="Pseudo" value={pseudo} onChange={e => setPseudo(e.target.value)} /></div>
                <div className="menu-buttons"><button className="btn solo-btn" onClick={startSolo}>Solo</button></div>
                <hr className="divider" />
                <h3>Multijoueur</h3>
                <button className="btn" onClick={createRoom}>Creer Salon</button>
                <div className="join-section">
                    <input type="text" placeholder="Code" value={inputCode} onChange={e => setInputCode(e.target.value)} />
                    <button className="btn" onClick={joinRoom}>Rejoindre</button>
                </div>
            </div>
        );
    }

    if (view === 'lobby') {
        return (
            <div className="container lobby-container">
                <h2>Salon - Code : {roomCode}</h2>
                <div className="chat-box">
                    <div className="messages">{chatMessages.map((m, i) => (<div key={i} className={`msg ${m.fromMe ? 'msg-me' : 'msg-opp'}`}><strong>{m.sender}:</strong> {m.text}</div>))}</div>
                    <form onSubmit={sendChat} className="chat-form"><input value={chatInput} onChange={e => setChatInput(e.target.value)} /><button type="submit">OK</button></form>
                </div>
                {isAdmin && playersCount === 2 && <button className="btn start-btn" onClick={startGame}>Lancer</button>}
            </div>
        );
    }

    const myData = gameState ? gameState[socket.id] : null;
    const opponentId = gameState ? Object.keys(gameState).find(id => id !== socket.id) : null;
    const oppData = opponentId ? gameState[opponentId] : null;

    return (
        <div className="game-container">
            <div className="header-bar">
                <h2>{gameMode.toUpperCase()}</h2>
                {gameMode === 'duplicate' && <div className="timer-display">Temps : {timeLimit}s</div>}
            </div>

            {isGameOver && (
                <div className="game-over">
                    <h2>Partie Terminee</h2>
                    {winnerData && (
                        <div className="winner-announcement">
                            {winnerData.isDraw ? (
                                <p>EGALITE ! Points divises par 2.</p>
                            ) : (
                                <p>VAINQUEUR : <strong>{winnerData.winners[0]}</strong></p>
                            )}
                            {!winnerData.pointsAwarded ? (
                                <p style={{color:'#ffcccc'}}>Score trop faible : aucun point de fidelite gagne.</p>
                            ) : (
                                <p>Points de fidelite mis a jour !</p>
                            )}
                        </div>
                    )}
                    <button className="btn" onClick={() => window.location.reload()}>Menu</button>
                </div>
            )}

            <div className="layout">
                <div className="player-area">
                    <h3>{myData?.pseudo} {myData?.isGameOver ? '(BLOQUE)' : ''}</h3>
                    <p>Score : {myData?.score}</p>
                    <div className="board">
                        {myData?.grid.map((row, y) => (
                            <div key={y} className="row">
                                {row.map((cellColor, x) => (<div key={`${x}-${y}`} className="cell" style={{ backgroundColor: cellColor || '#e0e0e0' }} onClick={() => handleCellClick(x, y)} />))}
                            </div>
                        ))}
                    </div>
                </div>

                <div className="center-area">
                    <div className="sidebar">
                        <h3>Suivant</h3>
                        <div className="preview">
                            {currentBrick && currentBrick.matrix.map((row, r) => (
                                <div key={r} className="row">
                                    {row.map((isFilled, c) => (<div key={c} className="cell preview-cell" style={{ backgroundColor: isFilled ? currentBrick.color : 'transparent', border: isFilled ? '2px solid #333' : 'none' }} />))}
                                </div>
                            ))}
                        </div>
                        <button className="btn" onClick={rotateBrick} disabled={myData?.isGameOver || myData?.hasPlaced}>Pivoter</button>
                        <button className="btn abandon-btn" onClick={handleAbandon} disabled={isGameOver}>Abandonner</button>
                    </div>
                    {gameMode === 'duplicate' && (
                        <div className="chat-box small-chat">
                            <div className="messages">{chatMessages.map((m, i) => (<div key={i} className={`msg ${m.fromMe ? 'msg-me' : 'msg-opp'}`}><strong>{m.sender}:</strong> {m.text}</div>))}</div>
                            <form onSubmit={sendChat} className="chat-form"><input value={chatInput} onChange={e => setChatInput(e.target.value)} /><button type="submit">OK</button></form>
                        </div>
                    )}
                </div>

                {gameMode === 'duplicate' && oppData && (
                    <div className="player-area opponent-area">
                        <h3>{oppData.pseudo} {oppData.isGameOver ? '(BLOQUE)' : ''}</h3>
                        <p>Score : {oppData.score}</p>
                        <div className="board mini-board">
                            {oppData.grid.map((row, y) => (
                                <div key={y} className="row">
                                    {row.map((cellColor, x) => (<div key={`${x}-${y}`} className="cell" style={{ backgroundColor: cellColor || '#e0e0e0' }} />))}
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
import React, { useState, useEffect, useRef } from 'react';
import io from 'socket.io-client';
import GameBoard from './components/GameBoard';

const params = new URLSearchParams(window.location.search);
const currentFidelityId = params.get('fidelityId');

export const fidelityId = currentFidelityId ? currentFidelityId : 'guest_' + Math.floor(Math.random() * 10000);

const socket = io('http://localhost:4001', { query: { fidelityId } });
// --- COMPOSANT CHAT SORTI DE APP ---
const ChatBox = ({ chatMessages, chatInput, setChatInput, sendChatMessage, messagesEndRef, currentSocketId }) => (
    <div style={{ border: '1px solid #ccc', borderRadius: '5px', width: '300px', display: 'flex', flexDirection: 'column', backgroundColor: '#f9f9f9', height: '300px', margin: '20px auto' }}>
        <div style={{ flex: 1, overflowY: 'auto', padding: '10px', textAlign: 'left' }}>
            {chatMessages.map((msg, idx) => (
                <div key={idx} style={{ marginBottom: '5px' }}>
                    <strong style={{ color: msg.senderId === currentSocketId ? '#0d6efd' : '#e67e22' }}>
                        {msg.senderId === currentSocketId ? 'Moi' : 'Adversaire'} :
                    </strong> {msg.message}
                </div>
            ))}
            <div ref={messagesEndRef} />
        </div>
        <form onSubmit={sendChatMessage} style={{ display: 'flex', borderTop: '1px solid #ccc' }}>
            <input 
                type="text" 
                value={chatInput} 
                onChange={e => setChatInput(e.target.value)} 
                placeholder="Message..." 
                style={{ flex: 1, padding: '10px', border: 'none', outline: 'none' }} 
                autoFocus // Optionnel : remet le focus automatiquement au montage
            />
            <button type="submit" style={{ padding: '10px', backgroundColor: '#0d6efd', color: 'white', border: 'none', cursor: 'pointer' }}>Envoyer</button>
        </form>
    </div>
);

export default function App() {
    const [fidelityId, setFidelityId] = useState('');
    const [gameState, setGameState] = useState('menu'); // menu, lobby, playing, ended
    const [roomCode, setRoomCode] = useState('');
    const [inputRoomCode, setInputRoomCode] = useState('');
    const [isLobbyAdmin, setIsLobbyAdmin] = useState(false);
    const [playerCount, setPlayerCount] = useState(0);
    
    // États en jeu
    const [currentBrick, setCurrentBrick] = useState(null);
    const [myGrid, setMyGrid] = useState([]);
    const [targetGrid, setTargetGrid] = useState([]);
    const [score, setScore] = useState(0);
    const [timeLeft, setTimeLeft] = useState(0);

    // États du chat
    const [chatMessages, setChatMessages] = useState([]);
    const [chatInput, setChatInput] = useState('');
    const messagesEndRef = useRef(null);

    useEffect(() => {
        // Extraction ID et URL pour invitation
        const params = new URLSearchParams(window.location.search);
        setFidelityId(fidelityId);
        
        const urlRoomCode = params.get('roomCode');
        if (urlRoomCode) {
            setInputRoomCode(urlRoomCode);
        }

        socket.on('lobby_created', (data) => {
            setRoomCode(data.roomCode);
            setIsLobbyAdmin(data.isLobbyAdmin);
            setGameState('lobby');
        });

        socket.on('lobby_update', (data) => setPlayerCount(data.playerCount));

        socket.on('game_error', (data) => alert(data.message));

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

        socket.on('receive_chat_message', (msg) => {
            setChatMessages((prev) => [...prev, msg]);
        });

        socket.on('game_over', () => setGameState('ended'));

        return () => {
            socket.off('lobby_created');
            socket.off('lobby_update');
            socket.off('game_error');
            socket.off('game_started');
            socket.off('new_turn');
            socket.off('grid_updated');
            socket.off('receive_chat_message');
            socket.off('game_over');
        };
    }, []);

    useEffect(() => {
        if (timeLeft > 0 && gameState === 'playing') {
            const timerId = setTimeout(() => setTimeLeft(timeLeft - 1), 1000);
            return () => clearTimeout(timerId);
        }
    }, [timeLeft, gameState]);

    // Scroll chat to bottom
    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
    }, [chatMessages]);

    const joinSoloGame = () => socket.emit('join_game', { fidelityId, mode: 'solo' });
    
    const createDuplicateLobby = () => socket.emit('create_duplicate', { fidelityId });
    
    const joinDuplicateLobby = () => {
        if(inputRoomCode) socket.emit('join_duplicate', { fidelityId, roomCode: inputRoomCode });
    };

    const startDuplicateGame = () => socket.emit('start_duplicate', { roomCode });

    const handleCellClick = (x, y) => {
        if (!currentBrick) return;
        socket.emit('place_brick', { x, y, brick: currentBrick });
        setCurrentBrick(null);
    };

    const sendChatMessage = (e) => {
        e.preventDefault();
        if (chatInput.trim() !== '') {
            socket.emit('send_chat_message', { roomCode, message: chatInput, senderName: fidelityId });
            setChatInput('');
        }
    };

    const copyInviteLink = () => {
        const link = `${window.location.origin}${window.location.pathname}?roomCode=${roomCode}&fidelityId=${fidelityId}`;
        navigator.clipboard.writeText(link);
        alert("Lien d'invitation copié !");
    };

    if (gameState === 'menu') {
        return (
            <div className="container" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                <h1>Fidélité Lego</h1>
                <p>Votre ID : {fidelityId}</p>
                
                <div style={{ border: '1px solid #ddd', padding: '20px', borderRadius: '8px', marginBottom: '20px', width: '100%', maxWidth: '400px' }}>
                    <h3>Mode Solo</h3>
                    <button className="btn" onClick={joinSoloGame} style={{ width: '100%' }}>Lancer le mode Solo</button>
                </div>

                <div style={{ border: '1px solid #ddd', padding: '20px', borderRadius: '8px', width: '100%', maxWidth: '400px' }}>
                    <h3>Mode Duplicate</h3>
                    <button className="btn" onClick={createDuplicateLobby} style={{ backgroundColor: '#2ecc71', width: '100%', marginBottom: '15px' }}>
                        Créer un salon
                    </button>
                    <div style={{ display: 'flex', gap: '10px' }}>
                        <input 
                            type="text" 
                            placeholder="Code du salon" 
                            value={inputRoomCode} 
                            onChange={(e) => setInputRoomCode(e.target.value)}
                            style={{ flex: 1, padding: '10px' }}
                        />
                        <button className="btn" onClick={joinDuplicateLobby} style={{ backgroundColor: '#f39c12' }}>Rejoindre</button>
                    </div>
                </div>

                <a href="http://localhost/PHP/SAELego/PHP/public/index.php?page=games" style={{ display: 'inline-block', padding: '10px 20px', backgroundColor: '#0d6efd', color: 'white', textDecoration: 'none', borderRadius: '5px', marginTop: '20px' }}>
                    Retour sur le site
                </a>
            </div>
        );
    }

    if (gameState === 'lobby') {
        return (
            <div className="container" style={{ textAlign: 'center' }}>
                <h1>Salon d'attente Duplicate</h1>
                <h2>Code du salon : <span style={{ color: '#e74c3c' }}>{roomCode}</span></h2>
                <button className="btn" onClick={copyInviteLink} style={{ backgroundColor: '#9b59b6', marginBottom: '20px' }}>Copier le lien d'invitation</button>
                
                <p>Joueurs connectés : {playerCount} / 2</p>

                <ChatBox 
                    chatMessages={chatMessages}
                    chatInput={chatInput}
                    setChatInput={setChatInput}
                    sendChatMessage={sendChatMessage}
                    messagesEndRef={messagesEndRef}
                    currentSocketId={socket.id}
                />

                {isLobbyAdmin ? (
                    <button className="btn" onClick={startDuplicateGame} disabled={playerCount < 2} style={{ backgroundColor: playerCount === 2 ? '#2ecc71' : '#bdc3c7', padding: '15px 30px', fontSize: '18px' }}>
                        {playerCount < 2 ? "En attente de l'adversaire..." : "Lancer la partie !"}
                    </button>
                ) : (
                    <p style={{ fontStyle: 'italic', color: '#7f8c8d' }}>En attente de l'administrateur pour lancer la partie...</p>
                )}
            </div>
        );
    }

    if (gameState === 'ended') {
        return (
            <div className="container">
                <h1>Partie Terminée !</h1>
                <h2>Vous avez gagné {score} points de fidélité.</h2>
                <button className="btn" onClick={() => window.location.reload()}>Retour au Menu</button>
            </div>
        );
    }

    return (
        <div className="container">
            <header className="game-header">
                <div>Score : {score}</div>
                {currentBrick && <div className="timer">Temps restant : {timeLeft}s</div>}
            </header>

            <div style={{ display: 'flex', gap: '20px', justifyContent: 'center', alignItems: 'flex-start' }}>
                {/* Zone de jeu principale */}
                <div className="boards-container" style={{ flex: 2 }}>
                    <div className="board-section">
                        <h3>Image Cible</h3>
                        <GameBoard grid={targetGrid} disabled={true} />
                    </div>

                    <div className="board-section">
                        <h3>Votre Tableau</h3>
                        <div style={{ minHeight: '100px', marginBottom: '15px' }}>
                            {currentBrick ? (
                                <div style={{ backgroundColor: currentBrick.color, padding: '15px', border: '3px solid #2c3e50', borderRadius: '8px', color: ['yellow', 'white'].includes(currentBrick.color) ? 'black' : 'white', fontWeight: 'bold', fontSize: '18px', textAlign: 'center', boxShadow: '0 4px 6px rgba(0,0,0,0.3)', display: 'inline-block' }}>
                                    Brique en main : {currentBrick.color} <br/>
                                    <span style={{ fontSize: '22px' }}>Forme : {currentBrick.shape}</span>
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

                {/* Zone de chat latérale en Duplicate */}
                {roomCode && (
                    <div style={{ flex: 1, minWidth: '250px' }}>
                        <h3>Chat</h3>
                        <ChatBox 
                            chatMessages={chatMessages}
                            chatInput={chatInput}
                            setChatInput={setChatInput}
                            sendChatMessage={sendChatMessage}
                            messagesEndRef={messagesEndRef}
                            currentSocketId={socket.id}
                        />
                    </div>
                )}
            </div>
        </div>
    );
}
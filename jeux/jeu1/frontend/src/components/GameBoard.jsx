import React from 'react';

export default function GameBoard({ grid, onCellClick, disabled }) {
    if (!grid || grid.length === 0) return null;

    return (
        <div className={`grid-wrapper ${disabled ? 'disabled' : ''}`}>
            {grid.map((row, y) => (
                <div key={`row-${y}`} className="row">
                    {row.map((cell, x) => (
                        <div
                            key={`cell-${x}-${y}`}
                            className="cell"
                            style={{ backgroundColor: cell ? cell.color : '#e0e0e0' }}
                            onClick={() => !disabled && onCellClick(x, y)}
                        >
                            {cell && <div className="lego-stud"></div>}
                        </div>
                    ))}
                </div>
            ))}
        </div>
    );
}
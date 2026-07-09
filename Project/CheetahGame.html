<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ultimate Tic Tac Toe - Cheetah vs Leopard</title>
  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      font-family: Arial, sans-serif;
      background: radial-gradient(circle at top, #f7e8c6, #8b9d6a);
      color: #222;
    }
    h1 {
      margin: 24px 0 8px;
      text-align: center;
    }
    .instructions {
      max-width: 700px;
      padding: 0 16px 16px;
      text-align: center;
    }
    .status-row {
      display: flex;
      gap: 16px;
      align-items: center;
      margin-bottom: 16px;
      flex-wrap: wrap;
      justify-content: center;
    }
    .status-row p {
      margin: 0;
      font-weight: 600;
    }
    button.restart {
      border: none;
      padding: 10px 18px;
      background: #2f4f4f;
      color: #fff;
      border-radius: 8px;
      cursor: pointer;
    }
    .board-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
      width: min(760px, 100%);
      max-width: 760px;
      padding: 10px;
      box-sizing: border-box;
    }
    .mini-board {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 6px;
      padding: 6px;
      background: rgba(255,255,255,0.9);
      border-radius: 14px;
      border: 3px solid rgba(31, 56, 30, 0.45);
      min-height: 190px;
      aspect-ratio: 1;
      position: relative;
    }
    .mini-board.active {
      border-color: #e76f51;
      box-shadow: 0 0 0 6px rgba(231,111,81,0.16);
    }
    .mini-board.won::after {
      content: attr(data-winner);
      position: absolute;
      inset: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 2rem;
      color: rgba(0,0,0,0.55);
      background: rgba(255,255,255,0.85);
      border-radius: 12px;
      pointer-events: none;
    }
    .cell {
      border: 2px solid #556b2f;
      border-radius: 10px;
      background: #f9f1e7;
      font-size: 1.7rem;
      font-weight: 700;
      color: #2a2a2a;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      user-select: none;
    }
    .cell:hover {
      background: #fcf3d4;
      transform: translateY(-1px);
    }
    .cell.disabled {
      cursor: default;
      background: #eee;
      color: #888;
    }
    .leopard {
      color: #5a3b0a;
    }
    .cheetah {
      color: #d35400;
    }
    .mini-board.won.leopard {
      border-color: #5a3b0a;
    }
    .mini-board.won.cheetah {
      border-color: #d35400;
    }
    .back-button {
        position: absolute;
        top: 20px;
        left: 20px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #b0ff8b 0%, #d1de70 100%);
        color: rgb(0, 1, 1);
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-weight: bold;
        transition: transform 0.2s;
        text-decoration: none;
        display: inline-block;
    }
        .back-button:hover {
            transform: scale(1.05);
    }
        .back-button:active {
            transform: scale(0.95);
    }
  </style>
</head>
<body>
  <a href="homepage.html" class="back-button">← Back</a>
  <h1>Ultimate Tic Tac Toe</h1>
  <div class="instructions">
    <p>Leopards are X and Cheetahs are O. Each move sends your opponent to the matching small board. Win three small boards in a row to win the big board.</p>
  </div>
  <div class="status-row">
    <p id="current-player">Current: Leopards <span>🐆</span></p>
    <p id="active-board">Active board: any</p>
    <button class="restart" id="restart">Restart</button>
  </div>
  <div class="board-grid" id="boardGrid"></div>
  <script>
    const boardGrid = document.getElementById('boardGrid');
    const currentPlayerText = document.getElementById('current-player');
    const activeBoardText = document.getElementById('active-board');
    const restartButton = document.getElementById('restart');

    const WIN_PATTERNS = [
      [0,1,2], [3,4,5], [6,7,8],
      [0,3,6], [1,4,7], [2,5,8],
      [0,4,8], [2,4,6]
    ];

    let boards = [];
    let boardOwners = [];
    let currentPlayer = 'leopard';
    let activeBoard = null;
    let gameOver = false;

    function createBoard() {
      const board = document.createElement('div');
      board.className = 'mini-board';
      board.dataset.index = boardGrid.children.length;
      for (let i = 0; i < 9; i++) {
        const cell = document.createElement('button');
        cell.className = 'cell';
        cell.dataset.board = board.dataset.index;
        cell.dataset.cell = i;
        cell.addEventListener('click', handleCellClick);
        board.appendChild(cell);
      }
      boardGrid.appendChild(board);
    }
    function resetGame() {
      boards = Array.from({ length: 9 }, () => Array(9).fill(''));
      boardOwners = Array(9).fill('');
      currentPlayer = 'leopard';
      activeBoard = null;
      gameOver = false;
      boardGrid.innerHTML = '';
      for (let i = 0; i < 9; i++) createBoard();
      updateStatus();
      updateBoards();
    }

    function updateStatus(message) {
      const playerLabel = currentPlayer === 'leopard' ? 'Leopards 🐆' : 'Cheetahs 🐾';
      currentPlayerText.textContent = message || `Current: ${playerLabel}`;
      activeBoardText.textContent = gameOver ? 'Game over' : activeBoard === null ? 'Active board: any' : `Active board: ${activeBoard + 1}`;
    }

    function getPiece(player) {
      return player === 'leopard' ? '🐆' : '🐾';
    }

    function applyBoardState() {
      Array.from(boardGrid.children).forEach((board, boardIndex) => {
        board.classList.toggle('active', activeBoard === null || activeBoard === boardIndex);
        board.classList.toggle('won', !!boardOwners[boardIndex]);
        board.classList.toggle('leopard', boardOwners[boardIndex] === 'leopard');
        board.classList.toggle('cheetah', boardOwners[boardIndex] === 'cheetah');
        board.dataset.winner = boardOwners[boardIndex] ? (boardOwners[boardIndex] === 'leopard' ? '🐆 Leopard' : '🐾 Cheetah') : '';
        Array.from(board.children).forEach((cell, cellIndex) => {
          const value = boards[boardIndex][cellIndex];
          cell.textContent = value ? getPiece(value) : '';
          cell.classList.toggle('leopard', value === 'leopard');
          cell.classList.toggle('cheetah', value === 'cheetah');
          const boardClosed = boardOwners[boardIndex] || gameOver;
          const cellEmpty = !value;
          const boardActive = activeBoard === null || activeBoard === boardIndex;
          cell.disabled = boardClosed || !boardActive || !cellEmpty;
          cell.classList.toggle('disabled', cell.disabled);
        });
      });
    }

    function checkWinner(cells) {
      for (const pattern of WIN_PATTERNS) {
        const [a,b,c] = pattern;
        if (cells[a] && cells[a] === cells[b] && cells[a] === cells[c]) {
          return cells[a];
        }
      }
      return '';
    }

    function handleCellClick(event) {
      if (gameOver) return;
      const boardIndex = Number(event.currentTarget.dataset.board);
      const cellIndex = Number(event.currentTarget.dataset.cell);
      if (activeBoard !== null && boardIndex !== activeBoard) return;
      if (boards[boardIndex][cellIndex]) return;

      boards[boardIndex][cellIndex] = currentPlayer;
      const boardWinner = checkWinner(boards[boardIndex]);
      if (boardWinner) boardOwners[boardIndex] = boardWinner;
      else if (boards[boardIndex].every(cell => cell)) boardOwners[boardIndex] = 'draw';

      const mainWinner = checkWinner(boardOwners.map(owner => owner === 'draw' ? '' : owner));
      if (mainWinner) {
        gameOver = true;
        updateStatus(`Winner: ${mainWinner === 'leopard' ? 'Leopards 🐆' : 'Cheetahs 🐾'}!`);
      } else if (boardOwners.every(owner => owner)) {
        gameOver = true;
        updateStatus('Draw: all boards finished');
      } else {
        activeBoard = boardOwners[cellIndex] ? null : cellIndex;
        currentPlayer = currentPlayer === 'leopard' ? 'cheetah' : 'leopard';
        updateStatus();
      }
      applyBoardState();
    }

    function updateBoards() {
      applyBoardState();
    }

    restartButton.addEventListener('click', resetGame);
    resetGame();
  </script>
</body>
</html>

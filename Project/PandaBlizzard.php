<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Panda Blizzard Pool</title>
<style>
  html, body { margin: 0; padding: 0; background: radial-gradient(circle at top, #4c8a35, #081b08 60%); min-height: 100%; color: #f1f0df; font-family: "Segoe UI", Arial, sans-serif; }
  body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
  .wrapper { display: flex; align-items: flex-start; gap: 24px; margin: 24px; }
  canvas { display: block; border-radius: 30px; box-shadow: 0 18px 60px rgba(0,0,0,0.45); background: transparent; }
  .ui-overlay { position: relative; top: auto; left: auto; right: auto; display: flex; flex-direction: column; gap: 14px; pointer-events: auto; z-index: 10; }
  .panel { background: rgba(10, 17, 9, 0.72); border: 1px solid rgba(255,255,255,0.12); border-radius: 18px; padding: 12px 14px; box-shadow: 0 12px 26px rgba(0,0,0,0.35); min-width: 180px; }
  .panel h1, .panel h2, .panel h3, .panel p { margin: 0; }
  .header-panel { flex: 1; min-width: 220px; }
  .title { font-size: 1rem; letter-spacing: 1px; color: #d4e7b3; text-shadow: 0 0 14px rgba(0,0,0,0.25); }
  .subtitle { margin-top: 4px; font-size: 0.9rem; color: #cddbb1; }
  .stats-panel { display: flex; gap: 12px; align-items: flex-start; }
  .team { margin-bottom: 6px; pointer-events: auto; }
  .team-name { font-size: 0.95rem; color: #e7f1cd; }
  .team-count { margin-top: 6px; font-size: 0.95rem; color: #cddbb1; }
  .status-panel { min-width: 200px; pointer-events: auto; }
  .status-panel .status-line { font-size: 0.95rem; margin-bottom: 8px; color: #e5f5c5; }
  .status-panel #status { color: #ffd970; font-size: 0.92rem; line-height: 1.4; min-height: 1.8em; }
  .button-row { display: flex; gap: 10px; margin-top: 14px; }
  button { border: none; border-radius: 12px; padding: 10px 14px; background: rgba(255,255,255,0.12); color: #f1f0df; cursor: pointer; font: inherit; transition: background 0.2s ease; }
  button:hover { background: rgba(255,255,255,0.22); }
</style>
</head>
<body>
<div class="wrapper">
  <div class="ui-overlay">
    <div class="header-panel panel">
      <div class="title">Panda Blizzard Pool</div>
      <div class="subtitle">Click and drag to shoot. Smooth bamboo pool action.</div>
      <div class="button-row">
        <button id="reset-btn" type="button">Reset</button>
        <button id="home-btn" type="button">Home</button>
      </div>
    </div>
    <div class="stats-panel">
      <div class="panel team" id="team1">
        <div class="team-name">Player 1</div>
        <div class="team-count">In: <span id="team1-count">0</span>/7</div>
      </div>
      <div class="panel team" id="team2">
        <div class="team-name">Player 2</div>
        <div class="team-count">In: <span id="team2-count">0</span>/7</div>
      </div>
    </div>
    <div class="panel status-panel">
      <div class="status-line">Current turn: <span id="current-turn">Player 1</span></div>
      <div class="status-line">Target: <span id="target-type">Any ball</span></div>
      <div id="status"></div>
    </div>
  </div>
  <canvas id="table" width="1000" height="580"></canvas>
<script>
const canvas = document.getElementById('table');
const ctx = canvas.getContext('2d');
const table = { x: 60, y: 70, w: 880, h: 460, pocketRadius: 34, rail: 12 };
const pockets = [
  { x: table.x + table.rail, y: table.y + table.rail },
  { x: table.x + table.w - table.rail, y: table.y + table.rail },
  { x: table.x + table.rail, y: table.y + table.h - table.rail },
  { x: table.x + table.w - table.rail, y: table.y + table.h - table.rail },
  { x: table.x + table.w / 2, y: table.y + table.rail },
  { x: table.x + table.w / 2, y: table.y + table.h - table.rail }
];
const balls = [];
const shot = { active: false, targetX: 0, targetY: 0, power: 0 };
const friction = 0.988;
const railRestitution = 0.88;
const ballRestitution = 0.98;
const minSpeed = 0.02;
const radius = 20;
const maxShotPower = 60;
const shotPowerScale = 0.6;
const shotVelocityFactor = 0.14;
const pandaImage = new Image();
// Embedded panda SVG image so no external file is required
pandaImage.src = 'data:image/svg+xml;utf8,' +
  '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">' +
  '<circle cx="100" cy="100" r="90" fill="%23ffffff" stroke="%23000000" stroke-width="2"/>' +
  '<ellipse cx="58" cy="54" rx="26" ry="30" fill="%23000"/>' +
  '<ellipse cx="142" cy="54" rx="26" ry="30" fill="%23000"/>' +
  '<ellipse cx="68" cy="88" rx="18" ry="22" fill="%23000"/>' +
  '<ellipse cx="132" cy="88" rx="18" ry="22" fill="%23000"/>' +
  '<circle cx="62" cy="86" r="6" fill="%23fff"/>' +
  '<circle cx="136" cy="86" r="6" fill="%23fff"/>' +
  '<circle cx="100" cy="122" r="16" fill="%23000"/>' +
  '<path d="M86 123 Q100 140 114 123" stroke="%23fff" stroke-width="6" fill="none" stroke-linecap="round"/>' +
  '<circle cx="62" cy="122" r="10" fill="%23ffb6c1" opacity="0.75"/>' +
  '<circle cx="138" cy="122" r="10" fill="%23ffb6c1" opacity="0.75"/>' +
  '</svg>';

function createBall(x, y, type, index, number) {
  return { x, y, vx: 0, vy: 0, r: radius, type, index, number, inPocket: false };
}

function createBalls() {
  balls.length = 0;
  balls.push(createBall(table.x + 120, table.y + table.h / 2, 'white', 0, 0));
  let baseX = table.x + 260;
  let baseY = table.y + table.h / 2;
  let ballNum = 1;
  for (let row = 0; row < 5; row++) {
    for (let col = 0; col <= row; col++) {
      let x = baseX + row * 42;
      let y = baseY - row * 22 + col * 44;
      let type = ballNum === 8 ? 'black' : (ballNum <= 7 ? 'solid' : 'stripe');
      balls.push(createBall(x, y, type, ballNum, ballNum));
      ballNum++;
    }
  }
}

function init() {
  resetGame();
  requestAnimationFrame(loop);
}

function resetGame() {
  currentPlayer = 1;
  awaitingPlacement = false;
  shotPlayer = null;
  shotStarted = false;
  shotHitBall = false;
  shotPocketedAnyBall = false;
  player1Type = null;
  player2Type = null;
  gameWon = false;
  winnerMessage = '';
  statusMessage = '';
  shot.active = false;
  shot.targetX = 0;
  shot.targetY = 0;
  shot.power = 0;
  createBalls();
  updateUI();
}

// UI: teams and turn
let currentPlayer = 1; // 1 or 2
let awaitingPlacement = false;
let shotPlayer = null;
let shotStarted = false;
let shotHitBall = false;
let shotPocketedAnyBall = false;
let player1Type = null; // 'solid' or 'stripe'
let player2Type = null;
let gameWon = false;
let winnerMessage = '';
let statusMessage = '';
function updateUI() {
  const t1 = player1Type ? balls.filter(b => b.type === player1Type && b.inPocket).length : 0;
  const t2 = player2Type ? balls.filter(b => b.type === player2Type && b.inPocket).length : 0;
  const team1Count = document.getElementById('team1-count');
  const team2Count = document.getElementById('team2-count');
  if (team1Count) team1Count.textContent = t1 + '/7';
  if (team2Count) team2Count.textContent = t2 + '/7';
  document.getElementById('current-turn').textContent = gameWon ? '' : 'Player ' + currentPlayer;
  const targetType = getPlayerBallType(currentPlayer);
  const targetLabel = targetType ? (targetType === 'solid' ? 'Solids' : 'Stripes') : 'Any ball';
  const targetEl = document.getElementById('target-type');
  if (targetEl) targetEl.textContent = gameWon ? '' : targetLabel;
  document.getElementById('status').textContent = gameWon ? winnerMessage : statusMessage;
}

function getPlayerBallType(player) {
  return player === 1 ? player1Type : player2Type;
}

function isValidCuePlacement(x, y) {
  const minX = table.x + table.rail + radius;
  const maxX = table.x + table.w - table.rail - radius;
  const minY = table.y + table.rail + radius;
  const maxY = table.y + table.h - table.rail - radius;
  return x >= minX && x <= maxX && y >= minY && y <= maxY;
}

setInterval(updateUI, 150);

function drawTable() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.fillStyle = '#17310f';
  ctx.fillRect(table.x, table.y, table.w, table.h);
  ctx.strokeStyle = '#5c4b2f';
  ctx.lineWidth = 24;
  ctx.strokeRect(table.x, table.y, table.w, table.h);
  ctx.fillStyle = '#0f1909';
  for (let i = 0; i < 6; i++) {
    let px = [table.x, table.x + table.w, table.x + table.w / 2][i % 3];
    let py = [table.y, table.y + table.h, table.y + table.h][Math.floor(i / 3)];
    ctx.beginPath();
    ctx.arc(px, py, table.pocketRadius, 0, Math.PI * 2);
    ctx.fill();
  }
  ctx.strokeStyle = '#9eb88a';
  ctx.lineWidth = 2;
  ctx.strokeRect(table.x + 12, table.y + 12, table.w - 24, table.h - 24);
}

function drawPanda(ball) {
  if (ball.inPocket) return;
  if (pandaImage.complete && pandaImage.naturalWidth) {
    ctx.drawImage(pandaImage, ball.x - ball.r, ball.y - ball.r, ball.r * 2, ball.r * 2);
  } else {
    ctx.save();
    ctx.translate(ball.x, ball.y);
    ctx.fillStyle = '#fff';
    ctx.beginPath();
    ctx.arc(0, 0, ball.r, 0, Math.PI * 2);
    ctx.fill();
    ctx.restore();
  }
}

function drawSolid(ball) {
  if (ball.inPocket) return;
  ctx.save();
  ctx.translate(ball.x, ball.y);
  ctx.fillStyle = '#5db85d';
  ctx.beginPath();
  ctx.ellipse(0, 0, ball.r, ball.r * 0.9, Math.PI / 8, 0, Math.PI * 2);
  ctx.fill();
  ctx.fillStyle = '#3d8937';
  ctx.beginPath();
  ctx.moveTo(-ball.r * 0.1, -ball.r * 0.85);
  ctx.quadraticCurveTo(ball.r * 0.85, 0, 0, ball.r * 0.9);
  ctx.quadraticCurveTo(-ball.r * 0.85, 0, -ball.r * 0.1, -ball.r * 0.85);
  ctx.fill();
  ctx.strokeStyle = '#e8f7df';
  ctx.lineWidth = 2;
  ctx.beginPath();
  ctx.moveTo(-ball.r * 0.22, -ball.r * 0.38);
  ctx.lineTo(ball.r * 0.22, ball.r * 0.42);
  ctx.stroke();
  ctx.beginPath();
  ctx.moveTo(-ball.r * 0.06, -ball.r * 0.7);
  ctx.quadraticCurveTo(ball.r * 0.12, -ball.r * 0.2, ball.r * 0.06, ball.r * 0.1);
  ctx.stroke();
  ctx.fillStyle = '#fff';
  ctx.font = 'bold 14px Arial';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText(ball.number, 0, ball.r * 0.5);
  ctx.restore();
}

function drawStripe(ball) {
  if (ball.inPocket) return;
  ctx.save();
  ctx.translate(ball.x, ball.y);
  ctx.fillStyle = '#ecf8db';
  ctx.beginPath();
  ctx.arc(0, 0, ball.r, 0, Math.PI * 2);
  ctx.fill();
  ctx.strokeStyle = '#4b7c34';
  ctx.lineWidth = 3;
  ctx.stroke();
  ctx.fillStyle = '#6bb24f';
  ctx.beginPath();
  ctx.rect(-ball.r * 0.18, -ball.r, ball.r * 0.36, ball.r * 2);
  ctx.fill();
  ctx.strokeStyle = '#3f7a2f';
  ctx.lineWidth = 2;
  for (let y = -ball.r * 0.7; y <= ball.r * 0.7; y += ball.r * 0.4) {
    ctx.beginPath();
    ctx.moveTo(-ball.r * 0.18, y);
    ctx.lineTo(ball.r * 0.18, y);
    ctx.stroke();
  }
  ctx.fillStyle = '#76c55b';
  for (let y = -ball.r * 0.7; y <= ball.r * 0.7; y += ball.r * 0.45) {
    ctx.beginPath();
    ctx.arc(-ball.r * 0.3, y, ball.r * 0.15, 0, Math.PI * 2);
    ctx.fill();
    ctx.beginPath();
    ctx.arc(ball.r * 0.3, y, ball.r * 0.15, 0, Math.PI * 2);
    ctx.fill();
  }
  ctx.fillStyle = '#fff';
  ctx.font = 'bold 16px Arial';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText(ball.number, 0, 0);
  ctx.restore();
}

function drawWhite(ball) {
  if (ball.inPocket) return;
  ctx.save();
  ctx.translate(ball.x, ball.y);
  ctx.fillStyle = '#fff';
  ctx.beginPath();
  ctx.arc(0, 0, ball.r, 0, Math.PI * 2);
  ctx.fill();
  ctx.strokeStyle = '#000';
  ctx.lineWidth = 2;
  ctx.stroke();
  ctx.restore();
}

function drawBlack(ball) {
  if (ball.inPocket) return;
  ctx.save();
  ctx.translate(ball.x, ball.y);
  ctx.fillStyle = '#000';
  ctx.beginPath();
  ctx.arc(0, 0, ball.r, 0, Math.PI * 2);
  ctx.fill();
  ctx.strokeStyle = '#fff';
  ctx.lineWidth = 2;
  ctx.stroke();
  ctx.fillStyle = '#fff';
  ctx.font = 'bold 16px Arial';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText('8', 0, 0);
  ctx.restore();
}

function drawVelocity(ball) {
  if (ball.inPocket) return;
  const speed = Math.hypot(ball.vx, ball.vy);
  if (speed < 0.3) return;
  const len = Math.min(50, speed * 20);
  const nx = ball.vx / speed;
  const ny = ball.vy / speed;
  const startX = ball.x + nx * ball.r;
  const startY = ball.y + ny * ball.r;
  const endX = ball.x + nx * (ball.r + len);
  const endY = ball.y + ny * (ball.r + len);
  ctx.strokeStyle = 'rgba(255,255,255,0.75)';
  ctx.lineWidth = 2;
  ctx.beginPath();
  ctx.moveTo(startX, startY);
  ctx.lineTo(endX, endY);
  ctx.stroke();
  const headSize = 6;
  ctx.fillStyle = 'rgba(255,255,255,0.85)';
  ctx.beginPath();
  ctx.moveTo(endX, endY);
  ctx.lineTo(endX - nx * headSize + ny * headSize * 0.5, endY - ny * headSize - nx * headSize * 0.5);
  ctx.lineTo(endX - nx * headSize - ny * headSize * 0.5, endY - ny * headSize + nx * headSize * 0.5);
  ctx.closePath();
  ctx.fill();
}

function drawBalls() {
  balls.forEach(ball => {
    if (ball.inPocket) return;
    if (ball.type === 'white') drawPanda(ball);
    else if (ball.type === 'black') drawBlack(ball);
    else if (ball.type === 'solid') drawSolid(ball);
    else if (ball.type === 'stripe') drawStripe(ball);
  });
}

function checkPockets(ball) {
  if (ball.inPocket) return false;
  for (const pocket of pockets) {
    const dx = ball.x - pocket.x;
    const dy = ball.y - pocket.y;
    if (Math.hypot(dx, dy) < table.pocketRadius) {
      ball.inPocket = true;
      ball.vx = 0;
      ball.vy = 0;
      return true;
    }
  }
  return false;
}

function updateBalls() {
  balls.forEach(ball => {
    if (ball.inPocket) return;
    ball.x += ball.vx;
    ball.y += ball.vy;
    const pocketed = checkPockets(ball);
    if (pocketed) {
      if (shotStarted && !awaitingPlacement) {
        shotPocketedAnyBall = true;
        if (ball.type === 'white') {
          // Scratch - cue ball pocketed
          shotHitBall = false;
          shotPocketedAnyBall = false;
        } else if (!player1Type && ball.type === 'solid') {
          player1Type = 'solid';
          player2Type = 'stripe';
        } else if (!player1Type && ball.type === 'stripe') {
          player1Type = 'stripe';
          player2Type = 'solid';
        }

        // Check if current player has pocketed all their assigned balls (7 balls)
        const playerType = getPlayerBallType(currentPlayer);
        if (playerType) {
          const remaining = balls.filter(b => b.type === playerType && !b.inPocket).length;
          if (remaining === 0) {
            gameWon = true;
            winnerMessage = 'Player ' + currentPlayer + ' Wins!';
          }
        }

        // If the black ball is pocketed, determine win/lose based on whether player had cleared their balls
        if (ball.type === 'black' && !gameWon) {
          // black pocketed prematurely: opponent wins
          gameWon = true;
          winnerMessage = 'Player ' + (currentPlayer === 1 ? 2 : 1) + ' Wins!';
        }
      }
    }
    const minX = table.x + table.rail + ball.r;
    const maxX = table.x + table.w - table.rail - ball.r;
    const minY = table.y + table.rail + ball.r;
    const maxY = table.y + table.h - table.rail - ball.r;
    if (ball.x < minX) { ball.x = minX; ball.vx *= -railRestitution; ball.vy *= 0.98; }
    if (ball.x > maxX) { ball.x = maxX; ball.vx *= -railRestitution; ball.vy *= 0.98; }
    if (ball.y < minY) { ball.y = minY; ball.vy *= -railRestitution; ball.vx *= 0.98; }
    if (ball.y > maxY) { ball.y = maxY; ball.vy *= -railRestitution; ball.vx *= 0.98; }
    ball.vx *= friction;
    ball.vy *= friction;
    if (Math.hypot(ball.vx, ball.vy) < minSpeed) {
      ball.vx = 0;
      ball.vy = 0;
    }
  });
  for (let i = 0; i < balls.length; i++) {
    for (let j = i + 1; j < balls.length; j++) {
      if (!balls[i].inPocket && !balls[j].inPocket) collide(balls[i], balls[j]);
    }
  }
  if (shotStarted && !awaitingPlacement && !balls.some(ball => !ball.inPocket && (ball.vx !== 0 || ball.vy !== 0))) {
    if (!gameWon) {
      const cue = balls[0];
      if (cue.inPocket) {
        currentPlayer = currentPlayer === 1 ? 2 : 1;
        awaitingPlacement = true;
        statusMessage = `Scratch! Player ${currentPlayer}, place the cue ball.`;
      } else if (!shotPocketedAnyBall) {
        currentPlayer = currentPlayer === 1 ? 2 : 1;
        statusMessage = '';
      } else {
        statusMessage = '';
      }
    }
    shotStarted = false;
    shotPlayer = null;
    shotHitBall = false;
    shotPocketedAnyBall = false;
    updateUI();
  }
}

function collide(a, b) {
  const dx = b.x - a.x;
  const dy = b.y - a.y;
  const dist = Math.hypot(dx, dy);
  const minDist = a.r + b.r;
  if (dist > 0 && dist < minDist) {
    if (shotStarted && (a.type === 'white' || b.type === 'white')) shotHitBall = true;
    const nx = dx / dist;
    const ny = dy / dist;
    const relVel = (b.vx - a.vx) * nx + (b.vy - a.vy) * ny;
    if (relVel > 0) return;
    const impulse = -(1 + ballRestitution) * relVel / 2;
    a.vx -= impulse * nx;
    a.vy -= impulse * ny;
    b.vx += impulse * nx;
    b.vy += impulse * ny;
    const overlap = (minDist - dist) / 2;
    a.x -= nx * overlap;
    a.y -= ny * overlap;
    b.x += nx * overlap;
    b.y += ny * overlap;
  }
}

function drawCue() {
  const cue = balls[0];
  if (!shot.active || !cue || cue.inPocket) return;
  const dx = shot.targetX - cue.x;
  const dy = shot.targetY - cue.y;
  const angle = Math.atan2(dy, dx);
  const nx = Math.cos(angle);
  const ny = Math.sin(angle);
  const cueEdgeX = cue.x + nx * cue.r;
  const cueEdgeY = cue.y + ny * cue.r;

  ctx.lineCap = 'round';
  ctx.strokeStyle = '#8b5a2b';
  ctx.lineWidth = 12;
  ctx.beginPath();
  ctx.moveTo(shot.targetX, shot.targetY);
  ctx.lineTo(cueEdgeX, cueEdgeY);
  ctx.stroke();

  ctx.strokeStyle = '#eee';
  ctx.lineWidth = 6;
  ctx.beginPath();
  ctx.moveTo(cueEdgeX - nx * 18, cueEdgeY - ny * 18);
  ctx.lineTo(cueEdgeX, cueEdgeY);
  ctx.stroke();

  ctx.fillStyle = '#6b3f17';
  ctx.beginPath();
  ctx.arc(cueEdgeX, cueEdgeY, 8, 0, Math.PI * 2);
  ctx.fill();
  ctx.strokeStyle = '#442b12';
  ctx.lineWidth = 2;
  ctx.stroke();
}

function drawPowerBar() {
  const barWidth = 360;
  const barHeight = 16;
  const x = canvas.width / 2 - barWidth / 2;
  const y = 20;
  ctx.save();
  ctx.fillStyle = 'rgba(0,0,0,0.45)';
  ctx.fillRect(x - 4, y - 4, barWidth + 8, barHeight + 28);
  ctx.fillStyle = '#244b1d';
  ctx.fillRect(x, y, barWidth, barHeight);
  const fill = Math.min(1, shot.power / maxShotPower);
  ctx.fillStyle = '#d6f4aa';
  ctx.fillRect(x, y, barWidth * fill, barHeight);
  ctx.strokeStyle = '#94b790';
  ctx.lineWidth = 2;
  ctx.strokeRect(x, y, barWidth, barHeight);
  ctx.fillStyle = '#f1f0df';
  ctx.font = '14px "Segoe UI", Arial, sans-serif';
  ctx.textAlign = 'center';
  const powerPercent = Math.round((shot.power / maxShotPower) * 100);
  ctx.fillText(`Power: ${powerPercent}%`, canvas.width / 2, y + barHeight + 20);
  ctx.restore();
}

function loop() {
  drawTable();
  updateBalls();
  drawBalls();
  drawCue();
  drawPowerBar();
  requestAnimationFrame(loop);
}

canvas.addEventListener('mousedown', (event) => {
  if (gameWon) return;
  const cue = balls[0];
  const rect = canvas.getBoundingClientRect();
  const x = event.clientX - rect.left;
  const y = event.clientY - rect.top;
  if (awaitingPlacement) {
    if (!isValidCuePlacement(x, y)) return;
    const overlap = balls.some(ball => ball !== cue && !ball.inPocket && Math.hypot(ball.x - x, ball.y - y) < ball.r * 2);
    if (overlap) return;
    cue.x = x;
    cue.y = y;
    cue.vx = 0;
    cue.vy = 0;
    cue.inPocket = false;
    awaitingPlacement = false;
    statusMessage = '';
    updateUI();
    return;
  }
  if (!cue || cue.inPocket) return;
  shot.active = true;
  shot.targetX = x;
  shot.targetY = y;
  const dx = cue.x - shot.targetX;
  const dy = cue.y - shot.targetY;
  shot.power = Math.min(maxShotPower, Math.hypot(dx, dy) * shotPowerScale);
});

canvas.addEventListener('mousemove', (event) => {
  if (!shot.active || gameWon) return;
  const cue = balls[0];
  if (!cue || cue.inPocket) return;
  const rect = canvas.getBoundingClientRect();
  shot.targetX = event.clientX - rect.left;
  shot.targetY = event.clientY - rect.top;
  const dx = cue.x - shot.targetX;
  const dy = cue.y - shot.targetY;
  shot.power = Math.min(maxShotPower, Math.hypot(dx, dy) * shotPowerScale);
});

canvas.addEventListener('mouseup', (event) => {
  if (!shot.active || gameWon) return;
  const cue = balls[0];
  if (!cue || cue.inPocket) return;
  const rect = canvas.getBoundingClientRect();
  const endX = event.clientX - rect.left;
  const endY = event.clientY - rect.top;
  const dx = cue.x - endX;
  const dy = cue.y - endY;
  const rawPull = Math.hypot(dx, dy);
  shot.power = Math.min(maxShotPower, rawPull * shotPowerScale);
  const powerRatio = rawPull ? shot.power / rawPull : 0;
  cue.vx = dx * shotVelocityFactor * powerRatio;
  cue.vy = dy * shotVelocityFactor * powerRatio;
  shotStarted = true;
  shotPlayer = currentPlayer;
  shotHitBall = false;
  shotPocketedAnyBall = false;
  shot.active = false;
});

canvas.addEventListener('mouseleave', () => { shot.active = false; });

document.getElementById('reset-btn').addEventListener('click', resetGame);
document.getElementById('home-btn').addEventListener('click', () => {
  window.location.href = 'homepage.php';
});

init();
</script>
</body>
</html>

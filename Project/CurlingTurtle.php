<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Curling Turtle</title>
  <style>
    html,body{height:100%;margin:0;background:#0b3d91;color:#fff;font-family:Segoe UI,Arial}
    #game{display:block;margin:20px auto;background:#0e6;border-radius:8px;box-shadow:0 6px 20px rgba(0,0,0,.5)}
    #info{width:900px;margin:6px auto;text-align:center}
    #bottomPanel{width:900px;margin:0 auto 20px;display:flex;gap:16px;flex-wrap:wrap;justify-content:space-between}
    #stats{flex:1 1 260px;background:rgba(0,0,0,0.4);border-radius:8px;padding:14px;min-width:260px}
    #leaderboard{flex:1 1 460px;background:rgba(0,0,0,0.4);border-radius:8px;padding:14px;min-width:260px}
    #leaderboard ol{margin:8px 0 0 18px;padding:0}
    #leaderboard li{margin-bottom:4px}
    #leaderboard input{width:140px;padding:6px;margin-right:6px;border-radius:4px;border:1px solid #999;background:#fff;color:#000}
    button{padding:6px 10px}
  </style>
</head>
<body>
  <div id="info">
    <h2>Curling Turtle</h2>
    <p>Left-drag the turtle (🐢) to set launch direction and power. Left-click and hold elsewhere to scrub the ice (creates low-friction path).</p>
    <button id="reset">Reset</button>
    <button id="home">Home</button>
  </div>
  <canvas id="game" width="900" height="820"></canvas>

  <div id="bottomPanel">
    <div id="stats">
      <h3>Game Stats</h3>
        <div id="statusText">Tries remaining: 3</div>
        <div id="scoreText">Score: 0</div>
        <div id="powerText">Power: 0%</div>
      <div style="margin:10px 0;">
        <div style="height:12px;width:100%;background:rgba(255,255,255,0.15);border-radius:8px;overflow:hidden;">
          <div id="powerBar" style="height:100%;width:0%;background:#76ff7f;transition:width 0.1s;"></div>
        </div>
      </div>
      <div id="lastShotText">Last shot: none</div>
      <div id="gameOverText" style="margin-top:8px;color:#ffcf6f;"></div>
    </div>
    <div id="leaderboard">
      <h3>Leaderboard</h3>
      <ol id="boardList"></ol>
      <div id="nameEntry">
        <input id="playerName" type="text" placeholder="Your name" maxlength="12">
        <button id="submitScore">Submit Score</button>
      </div>
      <div style="margin-top:8px;font-size:13px;color:#ccc;">Submit after your three tries to save the score.</div>
    </div>
  </div>

  <script>
  const canvas = document.getElementById('game');
  const ctx = canvas.getContext('2d');
  const W = canvas.width, H = canvas.height;

  const gridSize = 10;
  const cols = Math.ceil(W/gridSize), rows = Math.ceil(H/gridSize);
  const friction = new Float32Array(cols*rows).fill(1);
  const maxPower = 200;

  function idx(cx,cy){return cy*cols+cx}

  const target = {x: W/2, y: 100, r: 80};
  const startPos = {x: W/2, y: H - 140};

  const stone = {
    x: startPos.x, y: startPos.y, r: 18,
    vx: 0, vy: 0
  };

  let mouse = {x:0,y:0,down:false, draggingStone:false, dragStart:{} };
  let shotTaken = false;
  let score = 0;
  let tries = 3;
  let gameOver = false;
  let currentPower = 0;
  let lastShotScore = null;

  const boardListEl = document.getElementById('boardList');
  const statusText = document.getElementById('statusText');
  const scoreText = document.getElementById('scoreText');
  const powerText = document.getElementById('powerText');
  const powerBar = document.getElementById('powerBar');
  const lastShotText = document.getElementById('lastShotText');
  const gameOverText = document.getElementById('gameOverText');
  const playerNameInput = document.getElementById('playerName');
  const submitScoreButton = document.getElementById('submitScore');

  function loadLeaderboard(){
    try {
      return JSON.parse(localStorage.getItem('curlingTurtleLeaderboard') || '[]');
    } catch {
      return [];
    }
  }

  function saveLeaderboard(board){
    localStorage.setItem('curlingTurtleLeaderboard', JSON.stringify(board.slice(0,5)));
  }

  let leaderboard = loadLeaderboard();

  function updateLeaderboard(){
    leaderboard.sort((a,b)=>b.score-a.score);
    boardListEl.innerHTML = '';
    if(leaderboard.length === 0){
      boardListEl.innerHTML = '<li>No scores yet</li>';
      return;
    }
    leaderboard.slice(0,5).forEach(item=>{
      const li = document.createElement('li');
      li.textContent = `${item.name}: ${item.score}`;
      boardListEl.appendChild(li);
    });
  }

  function updateStats(){
    statusText.textContent = `Tries remaining: ${tries}`;
    scoreText.textContent = `Score: ${score}`;
    const percent = Math.round((currentPower / maxPower) * 100);
    powerText.textContent = `Power: ${percent}%`;
    powerBar.style.width = `${percent}%`;
    lastShotText.textContent = lastShotScore === null ? 'Last shot: none' : `Last shot: ${lastShotScore} points`;
    if(gameOver){
      gameOverText.textContent = 'Game over! Submit your score or reset to play again.';
    } else {
      gameOverText.textContent = '';
    }
  }

  function resetStonePosition(){
    friction.fill(1);
    stone.x = startPos.x;
    stone.y = startPos.y;
    stone.vx = 0;
    stone.vy = 0;
    shotTaken = false;
    currentPower = 0;
  }

  function addLeaderboardEntry(name, scoreValue){
    if(!name) name = 'Player';
    leaderboard.push({name, score: scoreValue});
    leaderboard.sort((a,b)=>b.score-a.score);
    leaderboard = leaderboard.slice(0,5);
    saveLeaderboard(leaderboard);
    updateLeaderboard();
  }

  canvas.addEventListener('mousemove', e=>{
    const rect = canvas.getBoundingClientRect();
    mouse.x = e.clientX - rect.left; mouse.y = e.clientY - rect.top;
    if(mouse.down && !mouse.draggingStone && !gameOver){
      scrubAt(mouse.x, mouse.y);
    }
    if(mouse.down && mouse.draggingStone){
      const dx = mouse.x - stone.x;
      const dy = mouse.y - stone.y;
      currentPower = Math.min(Math.hypot(dx,dy), maxPower);
      updateStats();
    }
  });

  canvas.addEventListener('mousedown', e=>{
    const rect = canvas.getBoundingClientRect();
    mouse.x = e.clientX - rect.left; mouse.y = e.clientY - rect.top;
    mouse.down = true;
    if(gameOver) return;
    const dx = mouse.x - stone.x, dy = mouse.y - stone.y;
    if(Math.hypot(dx,dy) <= stone.r+6 && !shotTaken && tries > 0){
      mouse.draggingStone = true;
      mouse.dragStart = {x:mouse.x,y:mouse.y};
    } else {
      mouse.draggingStone = false;
      scrubAt(mouse.x, mouse.y);
    }
  });

  canvas.addEventListener('mouseup', e=>{
    const rect = canvas.getBoundingClientRect();
    const mx = e.clientX - rect.left, my = e.clientY - rect.top;
    mouse.down = false;
    if(mouse.draggingStone && !gameOver){
      const dx = mouse.dragStart.x - mx, dy = mouse.dragStart.y - my;
      const power = 0.05;
      stone.vx = dx * power;
      stone.vy = dy * power;
      shotTaken = true;
      currentPower = Math.min(Math.hypot(dx,dy), maxPower);
      updateStats();
    }
    mouse.draggingStone = false;
  });

  function scrubAt(x,y){
    const cx = Math.floor(x / gridSize), cy = Math.floor(y / gridSize);
    for(let oy=-1; oy<=1; oy++) for(let ox=-1; ox<=1; ox++){
      const nx = cx+ox, ny = cy+oy;
      if(nx>=0 && ny>=0 && nx<cols && ny<rows){
        friction[idx(nx,ny)] = 0.12;
      }
    }
  }

  function scoreShot(){
    const d = Math.hypot(stone.x - target.x, stone.y - target.y);
    let pts = 0;
    if(d <= 10) pts = 10;
    else if(d <= 25) pts = 8;
    else if(d <= 40) pts = 5;
    else if(d <= target.r) pts = 2;
    else if(d <= target.r + 40) pts = 1;
    score += pts;
    tries--;
    lastShotScore = pts;
    if(tries <= 0){
      gameOver = true;
    } else {
      resetStonePosition();
    }
    updateStats();
  }

  function update(dt){
    const gx = Math.floor(stone.x / gridSize), gy = Math.floor(stone.y / gridSize);
    let f = 1;
    if(gx>=0 && gy>=0 && gx<cols && gy<rows) f = friction[idx(gx,gy)];
    const decay = 1 - (0.012 * f);
    stone.vx *= decay;
    stone.vy *= decay;
    stone.x += stone.vx * dt;
    stone.y += stone.vy * dt;
    if(stone.x < stone.r){ stone.x = stone.r; stone.vx *= -0.5; }
    if(stone.x > W - stone.r){ stone.x = W-stone.r; stone.vx *= -0.5; }
    if(stone.y < stone.r){ stone.y = stone.r; stone.vy *= -0.5; }
    if(stone.y > H - stone.r){ stone.y = H-stone.r; stone.vy *= -0.5; }
    if(shotTaken && !mouse.down){
      const speed = Math.hypot(stone.vx, stone.vy);
      if(speed < 0.03){
        scoreShot();
      }
    }
  }

  function drawGrid(){
    for(let y=0;y<rows;y++){
      for(let x=0;x<cols;x++){
        const v = friction[idx(x,y)];
        if(v<1){
          ctx.fillStyle = `rgba(255,255,255,${(1-v)*0.9})`;
          ctx.fillRect(x*gridSize,y*gridSize,gridSize,gridSize);
        }
      }
    }
  }

  function draw(){
    ctx.clearRect(0,0,W,H);
    ctx.fillStyle = '#70c8ff';
    ctx.fillRect(0,0,W,H);
    drawGrid();
    ctx.beginPath(); ctx.fillStyle='rgba(255,0,0,0.08)'; ctx.arc(target.x,target.y,target.r,0,Math.PI*2); ctx.fill();
    ctx.beginPath(); ctx.fillStyle='rgba(255,255,255,0.06)'; ctx.arc(target.x,target.y,40,0,Math.PI*2); ctx.fill();
    ctx.save();
    ctx.translate(stone.x,stone.y);
    ctx.beginPath(); ctx.fillStyle='#444'; ctx.arc(0,0,stone.r,0,Math.PI*2); ctx.fill();
    ctx.font = (stone.r*1.6) + 'px serif'; ctx.textAlign='center'; ctx.textBaseline='middle';
    ctx.fillText('🐢',0,0);
    ctx.restore();
    if(mouse.down && mouse.draggingStone && !gameOver){
      ctx.beginPath(); ctx.moveTo(stone.x,stone.y); ctx.lineTo(mouse.x,mouse.y); ctx.strokeStyle='rgba(0,0,0,0.7)'; ctx.lineWidth=2; ctx.stroke();
    }
    ctx.fillStyle='rgba(0,0,0,0.5)'; ctx.fillRect(10,10,260,70);
    ctx.fillStyle='#fff'; ctx.font='14px Segoe UI'; ctx.fillText('Velocity: '+(Math.hypot(stone.vx,stone.vy)).toFixed(2),20,33);
    ctx.fillText('Score: '+score,20,53);
    ctx.fillText('Tries: '+tries,120,53);
    ctx.fillText('Power: '+Math.round((currentPower/maxPower)*100)+'%',20,73);
  }

  let last = performance.now();
  function loop(t){
    const dt = (t-last)/16.67; last = t;
    if(!gameOver) update(dt);
    draw();
    requestAnimationFrame(loop);
  }
  requestAnimationFrame(loop);
  updateLeaderboard();
  updateStats();

  document.getElementById('reset').addEventListener('click', ()=>{
    score = 0;
    tries = 3;
    gameOver = false;
    lastShotScore = null;
    friction.fill(1);
    resetStonePosition();
    updateStats();
  });
  document.getElementById('home').addEventListener('click', ()=>{
    window.location.href = 'homepage.html';
  });
  document.getElementById('submitScore').addEventListener('click', ()=>{
    if(!gameOver) return;
    const name = playerNameInput.value.trim() || 'Player';
    addLeaderboardEntry(name, score);
    playerNameInput.value = '';
  });
    </script>
  </body>
</html>

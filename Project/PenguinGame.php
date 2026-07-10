<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Penguin Ice Jumper</title>
<style>
body {
  margin: 0;
  overflow: hidden;
  background: #0d1b33;
  color: #fff;
  font-family: Arial, sans-serif;
}
#gameCanvas {
  display: block;
  margin: 0 auto;
  background: #0c1d3d;
  border: 4px solid #8ec7ff;
  box-shadow: 0 0 30px rgba(60, 160, 255, 0.4);
}
#hud {
  position: absolute;
  top: 12px;
  left: 14px;
  font-size: 18px;
}
#hud button {
  margin-right: 10px;
  padding: 6px 12px;
  font-size: 16px;
  border: none;
  border-radius: 6px;
  background: #3d73c3;
  color: #ffffff;
  cursor: pointer;
}
#hud button:hover {
  background: #000000;
}
#status {
  margin-left: 10px;
}
#returnBtn {
  display: block;
  margin-top: 8px;
  padding: 8px 16px;
  font-size: 16px;
  border: 2px solid #ffd700;
  border-radius: 6px;
  background: #ff6b35;
  color: #ffffff;
  cursor: pointer;
  font-weight: bold;
  box-shadow: 0 0 15px rgba(255, 107, 53, 0.6);
  transition: all 0.3s ease;
}
#returnBtn:hover {
  background: #ff8555;
  box-shadow: 0 0 20px rgba(255, 107, 53, 0.9);
}
</style>
</head>
<body>
<canvas id="gameCanvas" width="400" height="600"></canvas>
<div id="hud"> 
  <button id="startBtn">Start</button>
  <button id="returnBtn">Return</button>
  <span id="status"></span>
</div>
<script>
const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");
const hud = document.getElementById("hud");
const startBtn = document.getElementById("startBtn");
const returnBtn = document.getElementById("returnBtn") 
const status = document.getElementById("status");
const width = canvas.width;
const height = canvas.height;
const keys = {};
let score = 0;
let gameOver = false;
let started = false;
let scrollOffset = 0;
const speedFactor = 0.1;
const penguin = {
  x: width / 2,
  y: height - 120,
  vx: 0,
  vy: 0,
  radius: 18
};
const iceTypes = [
  { color: "#bde9ff", pattern: "smooth" },
  { color: "#9adcf8", pattern: "cracked" },
  { color: "#d9f4ff", pattern: "spiky" },
  { color: "#c2e6ff", pattern: "frost" }
];
const platforms = [];
function createPlatform(x, y, typeIndex) {
  const p = { x, y, w: 80, h: 14, type: iceTypes[typeIndex] };
  if (iceTypes[typeIndex].pattern === "cracked") p.hitsLeft = 1; // cracked breaks after one jump
  return p;
}
function initPlatforms() {
  platforms.length = 0;
  for (let i = 0; i < 8; i++) {
    const x = Math.random() * (width - 90) + 5;
    const y = height - i * 80;
    platforms.push(createPlatform(x, y, i % iceTypes.length));
  }
}
initPlatforms();
window.addEventListener("keydown", e => keys[e.key] = true);
window.addEventListener("keyup", e => keys[e.key] = false);
startBtn.addEventListener("click", () => {
  if (!started || gameOver) reset();
  started = true;
});
returnBtn.addEventListener("click", () => {
  window.location.href = "homepage.html";
});
function drawPenguin() {
  ctx.save();
  ctx.translate(penguin.x, penguin.y);
  ctx.fillStyle = "#ffffff";
  ctx.beginPath();
  ctx.ellipse(0, 0, 14, 18, 0, 0, Math.PI * 2);
  ctx.fill();
  ctx.fillStyle = "#1d2a44";
  ctx.beginPath();
  ctx.ellipse(-4, 2, 11, 14, 0, 0, Math.PI * 2);
  ctx.fill();
  ctx.fillStyle = "#ffd16a";
  ctx.beginPath();
  ctx.moveTo(-8, 7);
  ctx.lineTo(4, 7);
  ctx.lineTo(-2, 16);
  ctx.closePath();
  ctx.fill();
  ctx.fillStyle = "#000000";
  ctx.beginPath();
  ctx.arc(-6, -4, 3, 0, Math.PI * 2);
  ctx.arc(4, -4, 3, 0, Math.PI * 2);
  ctx.fill();
  ctx.restore();
}
function drawBackground() {
  const skyGradient = ctx.createLinearGradient(0, 0, 0, height);
  skyGradient.addColorStop(0, "#07152c");
  skyGradient.addColorStop(0.55, "#164b74");
  skyGradient.addColorStop(1, "#7fd0ff");
  ctx.fillStyle = skyGradient;
  ctx.fillRect(0, 0, width, height);

  ctx.save();
  ctx.translate(0, -scrollOffset * 0.18);
  ctx.fillStyle = "rgba(255, 255, 255, 0.12)";
  for (let i = 0; i < 18; i++) {
    const x = (i * 73) % (width + 40) - 20;
    const y = 60 + (i % 5) * 70 + (i * 17) % 40;
    ctx.beginPath();
    ctx.ellipse(x, y, 18 + (i % 3) * 5, 10 + (i % 3) * 3, 0, 0, Math.PI * 2);
    ctx.fill();
  }
  ctx.fillStyle = "rgba(180, 235, 255, 0.28)";
  for (let i = 0; i < 5; i++) {
    const x = 40 + i * 70;
    const y = height - 80 - i * 60;
    ctx.beginPath();
    ctx.moveTo(x, y + 30);
    ctx.lineTo(x + 24, y);
    ctx.lineTo(x + 50, y + 30);
    ctx.lineTo(x + 42, y + 30);
    ctx.lineTo(x + 60, y + 70);
    ctx.lineTo(x + 18, y + 70);
    ctx.lineTo(x, y + 30);
    ctx.fill();
  }
  ctx.restore();
}
function drawPlatform(platform) {
  const { x, y, w, h, type } = platform;
  ctx.fillStyle = type.color;
  ctx.fillRect(x, y, w, h);
  ctx.strokeStyle = "#bbecff";
  ctx.lineWidth = 2;
  ctx.strokeRect(x, y, w, h);
  ctx.fillStyle = "#e8fbff";
  if (type.pattern === "cracked") {
    ctx.beginPath();
    ctx.moveTo(x + 10, y + 2);
    ctx.lineTo(x + 40, y + 10);
    ctx.lineTo(x + 70, y + 2);
    ctx.stroke();
  } else if (type.pattern === "spiky") {
    for (let i = 0; i < 5; i++) {
      ctx.beginPath();
      const px = x + 10 + i * 14;
      ctx.moveTo(px, y + 2);
      ctx.lineTo(px + 7, y - 6);
      ctx.lineTo(px + 14, y + 2);
      ctx.fill();
    }
  } else if (type.pattern === "frost") {
    ctx.fillRect(x + 5, y + 3, 10, 2);
    ctx.fillRect(x + 30, y + 5, 12, 2);
    ctx.fillRect(x + 55, y + 2, 8, 2);
  }
}
function update() {
  if (gameOver || !started) return;
  const prevY = penguin.y;
  if (keys.ArrowLeft || keys.a) penguin.vx = -4.2 * speedFactor;
  else if (keys.ArrowRight || keys.d) penguin.vx = 4.2 * speedFactor;
  else penguin.vx *= 0.92;
  penguin.x += penguin.vx;
  if (penguin.x < -penguin.radius) penguin.x = width + penguin.radius;
  if (penguin.x > width + penguin.radius) penguin.x = -penguin.radius;
  // reduced gravity so penguin can jump higher
  // lower gravity and stronger bounce for higher jumps; slightly faster horizontal movement
  penguin.vy += 0.06 * speedFactor;
  penguin.y += penguin.vy;
  if (penguin.y > height) {
    gameOver = true;
  }
  if (penguin.vy > 0) {
    platforms.forEach(platform => {
      const bottom = penguin.y + penguin.radius;
      const prevBottom = prevY + penguin.radius;
      const horizontalOverlap = penguin.x + penguin.radius > platform.x &&
        penguin.x - penguin.radius < platform.x + platform.w;
      if (horizontalOverlap &&
          prevBottom <= platform.y &&
          bottom >= platform.y &&
          bottom <= platform.y + platform.h) {
        if (platform.type.pattern === "spiky") {
          gameOver = true;
        } else {
          penguin.y = platform.y - penguin.radius;
          // stronger bounce so jumps feel better with reduced gravity
          penguin.vy = -18 * speedFactor;
          score += 10;
          // if cracked, consume hit and mark for removal
          if (platform.type.pattern === "cracked") {
            platform.hitsLeft = (platform.hitsLeft || 1) - 1;
            if (platform.hitsLeft <= 0) platform.toRemove = true;
          }
        }
      }
    });
    // remove any cracked platforms that broke
    for (let i = platforms.length - 1; i >= 0; i--) {
      if (platforms[i].toRemove) platforms.splice(i, 1);
    }
  }
  if (penguin.y < height / 3) {
    const dy = Math.min(8, (height / 3 - penguin.y) * 0.08);
    penguin.y += dy;
    scrollOffset += dy;
    platforms.forEach(p => p.y += dy);
    score += Math.floor(dy * 2);
    for (let i = platforms.length - 1; i >= 0; i--) {
      if (platforms[i].y > height) {
        platforms.splice(i, 1);
        const x = Math.random() * (width - 90) + 1;
        const y = -20;
        const typeIndex = Math.floor(Math.random() * iceTypes.length);
        platforms.push(createPlatform(x, y, typeIndex));
      }
    }
  }
  if (platforms[platforms.length - 1].y > height - 50) {
    const x = Math.random() * (width - 90) + 1;
    const y = height - 50;
    platforms.push(createPlatform(x, y, 0));
  }
}
function draw() {
  ctx.clearRect(0, 0, width, height);
  drawBackground();
  ctx.fillStyle = "#82c8ff";
  ctx.font = "24px Arial";
  ctx.fillText("Penguin Ice Jumper", 14, 32);
  platforms.forEach(drawPlatform);
  drawPenguin();
  status.textContent = "Score: " + score + (gameOver ? "   Press R to restart" : !started ? "   Press Start to begin" : "");
  if (gameOver) {
    ctx.fillStyle = "rgba(0, 0, 0, 0.65)";
    ctx.fillRect(0, height / 2 - 56, width, 112);
    ctx.fillStyle = "#ffffff";
    ctx.font = "28px Arial";
    ctx.textAlign = "center";
    ctx.fillText("Game Over", width / 2, height / 2 - 8);
    ctx.font = "18px Arial";
    ctx.fillText("Press R to restart", width / 2, height / 2 + 26);
    ctx.textAlign = "left";
  }
}
function loop() {
  update();
  draw();
  requestAnimationFrame(loop);
}
window.addEventListener("keydown", e => {
  if (e.key === "r" && gameOver) {
    reset();
  }
});
function reset() {
  score = 0;
  gameOver = false;
  paused = false;
  started = true;
  penguin.x = width / 2;
  penguin.y = height - 120;
  penguin.vx = 0;
  penguin.vy = 0;
  scrollOffset = 0;
  initPlatforms();
  platforms.unshift(createPlatform(penguin.x - 40, height - 50, 0));
}
loop();
</script>
</body>
</html>

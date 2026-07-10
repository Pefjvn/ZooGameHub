<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Crane Game</title>
	<style>
		/* bird / crane themed background */
		body { background: radial-gradient(circle at 20% 20%, #fffaf0, transparent 20%), linear-gradient(#cfe9ff, #e6f7ff); font-family: sans-serif; text-align:center; }
		canvas { background: linear-gradient(#aee, #6cf); display:block; margin:10px auto; border:2px solid #036; box-shadow: 0 6px 18px rgba(0,0,0,0.15); }
		#hud { margin:8px; }
		button { margin:6px; padding:8px 12px; }
		/* side info panel */
		#sideInfo { position: absolute; right: 18px; top: 110px; background: rgba(255,255,255,0.9); padding:8px 12px; border-radius:6px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); font-weight:600; }
		@media (max-width:700px){ #sideInfo{ display:none; } }
	</style>
</head>
<body>
	<h1>Crane Game</h1>
	<div id="hud">
		<button id="startBtn">Start</button>
		<button id="homeBtn">Return to homepage</button>
		<span id="score">Caught: 0</span>
		<span id="misses" style="margin-left:12px;">Missed: 0</span>
	</div>
	<canvas id="game" width="600" height="480"></canvas>
	<p>Use Left / Right arrows to move the bird crane at the bottom. Catch falling neck pieces to extend the neck. Missing 5 = game over.</p>

	<script>
	const canvas = document.getElementById('game');
	const ctx = canvas.getContext('2d');
	const startBtn = document.getElementById('startBtn');
	const homeBtn = document.getElementById('homeBtn');
	const scoreEl = document.getElementById('score');
	const missesEl = document.getElementById('misses');

	let running = false;
	let score = 0;
	let misses = 0;

	// bird crane at bottom
	const crane = { x: canvas.width/2, width: 80, neckLen: 20, bodyY: canvas.height - 50 };

	const objects = [];
	let spawnTimer = 0;

	function reset(){
		score = 0; misses = 0; crane.x = canvas.width/2; crane.neckLen = 40; objects.length = 0; spawnTimer = 0; updateHUD();
	}

	function updateHUD(){ scoreEl.textContent = 'Caught: '+score; missesEl.textContent = 'Missed: '+misses; }

	function spawn(){
		const size = 18 + Math.random()*18;
		objects.push({ x: Math.random()*(canvas.width-40)+20, y: -20, vy: 0.5+Math.random()*0.8, r: size/2 });
	}

	function step(){
		if(!running) return;
		// compute camera translate so spawning and physics use same coordinate space
		const desiredHeadY = 120;
		const bodyY = crane.bodyY;
		const neckTopY = bodyY - crane.neckLen;
		const translateY = Math.max(0, desiredHeadY - neckTopY);
		// spawn
		spawnTimer -= 1;
		if(spawnTimer<=0){
			// spawn so pieces appear at top of the screen regardless of camera translate
			const size = 18 + Math.random()*18;
			objects.push({ x: Math.random()*(canvas.width-40)+20, y: -20 - translateY, vy: 0.5+Math.random()*0.8, r: size/2 });
			spawnTimer = 50 + Math.random()*60;
		}

		// physics
		for(let i=objects.length-1;i>=0;i--){
			const o = objects[i]; o.y += o.vy;
			// hit ground (miss if reaches bottom)
			// ground check: account for camera translate (ground screen Y == canvas.height)
			if(o.y - o.r > canvas.height - translateY){ objects.splice(i,1); misses++; updateHUD(); if(misses>=5){ running=false; alert('Game Over'); } continue; }
			// check catch: bird at bottom extends neck upward; catch when piece intersects neck region
			const neckBottomY = crane.bodyY; // base of neck at bird body
			const neckTopY = neckBottomY - crane.neckLen;
			if(o.y + o.r >= neckTopY && o.y - o.r <= neckBottomY){
				if(Math.abs(o.x - crane.x) <= (crane.width/2)){
					// caught
					objects.splice(i,1); score++; crane.neckLen += 12; updateHUD();
				}
			}
		}

		draw();
		requestAnimationFrame(step);
	}

	function draw(){
		ctx.clearRect(0,0,canvas.width,canvas.height);
		// camera follow: keep head near desired screen Y
		// start the view lower so the ground and full bird are visible at game start
		const desiredHeadY = 120; // vertical screen position to keep the head near
		// improved bird sprite: smoother body, wing, head and shaded neck
		const bodyY = crane.bodyY;
		const neckTopY = bodyY - crane.neckLen;
		// Compute a translation so the neck top is drawn at desiredHeadY when it goes above that point
		const translateY = Math.max(0, desiredHeadY - neckTopY);
		ctx.save(); ctx.translate(0, translateY);

		// ground
		ctx.fillStyle = '#3b5'; ctx.fillRect(0, canvas.height-30, canvas.width, 30);

		// body base with gradient
		const bodyGrad = ctx.createLinearGradient(crane.x-60, bodyY-20, crane.x+60, bodyY+20);
		bodyGrad.addColorStop(0, '#fff'); bodyGrad.addColorStop(1, '#f6f6f6');
		ctx.fillStyle = bodyGrad;
		ctx.beginPath(); ctx.ellipse(crane.x, bodyY, crane.width/2, 28, 0, 0, Math.PI*2); ctx.fill();

		// wing
		ctx.save(); ctx.translate(crane.x-10, bodyY-6); ctx.rotate(-0.2);
		const wingGrad = ctx.createLinearGradient(-40, -10, 40, 20); wingGrad.addColorStop(0,'#fff'); wingGrad.addColorStop(1,'#f2f2f2');
		ctx.fillStyle = wingGrad; ctx.beginPath(); ctx.ellipse(0, 6, 40, 18, 0, 0, Math.PI*2); ctx.fill();
		ctx.restore();

		// tail feathers
		ctx.fillStyle = '#f6f6f6'; ctx.beginPath(); ctx.moveTo(crane.x- crane.width/2 - 6, bodyY); ctx.lineTo(crane.x- crane.width/2 - 28, bodyY-8); ctx.lineTo(crane.x- crane.width/2 - 18, bodyY+6); ctx.fill();

		// neck (shaded line)
		const neckTopX = crane.x;
		// shadow behind neck
		ctx.strokeStyle = 'rgba(255,255,255,0.6)'; ctx.lineWidth = 10; ctx.beginPath(); ctx.moveTo(neckTopX+3, bodyY+2); ctx.lineTo(neckTopX+3, neckTopY+2); ctx.stroke();
		// main neck
		ctx.strokeStyle = '#fafafa'; ctx.lineWidth = 6; ctx.beginPath(); ctx.moveTo(neckTopX, bodyY); ctx.lineTo(neckTopX, neckTopY); ctx.stroke();

		// head: small rounded head with gradient
		const headGrad = ctx.createRadialGradient(crane.x+6, neckTopY-8, 3, crane.x+6, neckTopY-8, 16);
		headGrad.addColorStop(0, '#fff'); headGrad.addColorStop(1, '#f5f5f5');
		ctx.fillStyle = headGrad; ctx.beginPath(); ctx.ellipse(crane.x+8, neckTopY-6, 14, 12, -0.3, 0, Math.PI*2); ctx.fill();

		// eye on head (white + pupil)
		ctx.fillStyle = '#fff'; ctx.beginPath(); ctx.arc(crane.x+16, neckTopY-10, 4, 0, Math.PI*2); ctx.fill();
		ctx.fillStyle = '#000'; ctx.beginPath(); ctx.arc(crane.x+18, neckTopY-11, 1.6, 0, Math.PI*2); ctx.fill();

		// beak (two-tone)
		ctx.fillStyle = '#fff'; ctx.beginPath(); ctx.moveTo(crane.x+20, neckTopY-2); ctx.lineTo(crane.x+36, neckTopY); ctx.lineTo(crane.x+20, neckTopY+6); ctx.fill();
		ctx.fillStyle = '#fafafa'; ctx.beginPath(); ctx.moveTo(crane.x+26, neckTopY); ctx.lineTo(crane.x+36, neckTopY); ctx.lineTo(crane.x+26, neckTopY+4); ctx.fill();

		// falling neck pieces
		for(const o of objects){ ctx.fillStyle='#c33'; ctx.beginPath(); ctx.arc(o.x, o.y, o.r,0,Math.PI*2); ctx.fill(); }

		// info

		ctx.restore();
	}

	// controls
	const keys = {};
	window.addEventListener('keydown', e=>{ keys[e.key]=true; if(e.key===' '){ e.preventDefault(); } });
	window.addEventListener('keyup', e=>{ keys[e.key]=false; });

	function controlLoop(){
		if(keys['ArrowLeft']) crane.x -= 4;
		if(keys['ArrowRight']) crane.x += 4;
		crane.x = Math.max(40, Math.min(canvas.width-40, crane.x));
		requestAnimationFrame(controlLoop);
	}

	startBtn.addEventListener('click', ()=>{
		if(!running){ running=true; reset(); requestAnimationFrame(step); }
	});
	homeBtn.addEventListener('click', ()=>{ location.href = 'homepage.html'; });

	// start control loop and initial draw
	controlLoop(); draw();
	</script>
</body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Parrot Jungle Run</title>
    <style>
        html,body{height:100%;margin:0;background:linear-gradient(#1e3d1f,#5b8a43);display:flex;align-items:center;justify-content:center;font-family:sans-serif}
        #game{background:linear-gradient(#2f5f2a,#6ca55a);box-shadow:0 6px 20px rgba(0,0,0,.4);border:6px solid rgba(0,0,0,.1)}
        canvas{display:block}
        .overlay{position:absolute;top:10px;left:10px;color:#fff;font-weight:700;text-shadow:0 1px 2px rgba(0,0,0,.5)}
        .return-button {
            display: block;
            text-align: center;
            margin: 24px auto 0 auto;
            padding: 12px 24px;
            background: #3b6f29;
            color: white;
            text-decoration: none;
            border-radius: 14px;
            transition: background 0.15s ease, transform 0.15s ease;
        }
        .return-button:hover {
            background: #2f5b21;
            transform: translateY(-2px);
        }
        .start-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 16px 32px;
            background: #d49f31;
            color: white;
            font-size: 20px;
            font-weight: bold;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease;
            z-index: 10;
        }
        .start-button:hover {
            background: #b87b1f;
            transform: translate(-50%, -50%) scale(1.05);
        }
    </style>
</head>
<body>
    <div style="position:relative">
        <canvas id="game" width="480" height="640"></canvas>
        <div class="overlay" id="score">Score: 0</div>
        <button id="startBtn" class="start-button">Start Game</button>
        <a href="Homepage.php" class="return-button">Return to Home</a>
    </div>

    <script>
    const canvas = document.getElementById('game');
    const ctx = canvas.getContext('2d');
    const scoreEl = document.getElementById('score');
    const startBtn = document.getElementById('startBtn');

    const W = canvas.width, H = canvas.height;

    const parrotImg = new Image();
    parrotImg.src = 'parrot.png';

    const player = {
        x: 90,
        y: H/2,
        radius: 18,
        vel: 0,
        rotation: 0
    };

    const gravity = 0.0099;
    const jump = 1;
    const gap = 270;
    const pipeWidth = 40;
    const pipeSpeed = 2;
    let pipes = [];
    const humans = [
        {x:-20,yOffset:0,scale:1},
        {x:-40,yOffset:-20,scale:0.8},
        {x:10,yOffset:-10,scale:0.9}
    ];
    let frame = 0;
    let score = 0;
    let best = 0;
    let running = false;
    let gameStarted = false;

    function reset(){
        pipes = [];
        frame = 0;
        score = 0;
        player.y = H/2;
        player.vel = 0;
        player.rotation = 0;
        running = true;
        gameStarted = true;
        scoreEl.textContent = 'Score: 0';
        startBtn.style.display = 'none';
        spawnPipe();
    }

    function spawnPipe(){
        const topHeight = 80 + Math.random() * (H - gap - 120);
        pipes.push({x: W, top: topHeight, passed: false});
    }

    function drawHuman(x,y,scale){
        ctx.save();
        ctx.translate(x,y);
        ctx.scale(scale,scale);
        ctx.fillStyle='#3a2210';
        ctx.beginPath();
        ctx.ellipse(0,0,6,14,0,0,Math.PI*2);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(0,-20,8,0,Math.PI*2);
        ctx.fill();
        ctx.strokeStyle='#3a2210';
        ctx.lineWidth=3;
        ctx.beginPath();
        ctx.moveTo(-8,-10);
        ctx.lineTo(-18,2);
        ctx.moveTo(8,-10);
        ctx.lineTo(18,2);
        ctx.moveTo(-4,14);
        ctx.lineTo(-10,30);
        ctx.moveTo(4,14);
        ctx.lineTo(10,30);
        ctx.stroke();
        ctx.fillStyle='#fff';
        ctx.beginPath();
        ctx.arc(-3,-22,1.5,0,Math.PI*2);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(3,-22,1.5,0,Math.PI*2);
        ctx.fill();
        ctx.restore();
    }

    function drawParrot(x,y,r,rot){
        ctx.save();
        ctx.translate(x,y);
        ctx.rotate(rot * 0.6);
        const width = r * 3;
        const height = r * 2;

        if(parrotImg.complete && parrotImg.naturalWidth){
            ctx.drawImage(parrotImg, -width/2, -height/2, width, height);
        } else {
            // body
            ctx.fillStyle = '#43a047';
            ctx.fillRect(-width * 0.35, -height * 0.2, width * 0.7, height * 0.4);

            // wing
            ctx.fillStyle = '#66bb6a';
            ctx.beginPath();
            ctx.ellipse(-width * 0.05, 0, width * 0.35, height * 0.22, Math.PI / 6, 0, Math.PI * 2);
            ctx.fill();

            // head
            ctx.fillStyle = '#2e7d32';
            ctx.beginPath();
            ctx.arc(width * 0.3, -height * 0.15, height * 0.25, 0, Math.PI * 2);
            ctx.fill();

            // eye
            ctx.fillStyle = '#fff';
            ctx.beginPath();
            ctx.arc(width * 0.35, -height * 0.22, height * 0.06, 0, Math.PI * 2);
            ctx.fill();
            ctx.fillStyle = '#000';
            ctx.beginPath();
            ctx.arc(width * 0.36, -height * 0.22, height * 0.03, 0, Math.PI * 2);
            ctx.fill();

            // beak
            ctx.fillStyle = '#ffb300';
            ctx.beginPath();
            ctx.moveTo(width * 0.43, -height * 0.13);
            ctx.lineTo(width * 0.55, -height * 0.1);
            ctx.lineTo(width * 0.43, -height * 0.07);
            ctx.closePath();
            ctx.fill();

            // tail feathers
            ctx.fillStyle = '#d32f2f';
            ctx.fillRect(-width * 0.45, height * 0.08, width * 0.12, height * 0.32);
            ctx.fillStyle = '#1976d2';
            ctx.fillRect(-width * 0.28, height * 0.12, width * 0.12, height * 0.28);
            ctx.fillStyle = '#fdd835';
            ctx.fillRect(-width * 0.12, height * 0.15, width * 0.12, height * 0.24);

            // feet
            ctx.strokeStyle = '#5d4037';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(width * 0.02, height * 0.14);
            ctx.lineTo(width * 0.02, height * 0.28);
            ctx.moveTo(width * 0.08, height * 0.14);
            ctx.lineTo(width * 0.08, height * 0.28);
            ctx.stroke();
        }

        ctx.restore();
    }

    function update(){
        if(!running) return;

        frame++;
        player.vel += gravity;
        player.y += player.vel;
        player.rotation = Math.max(-0.6, Math.min(0.8, player.vel / 10));

        if(frame % 90 === 0) spawnPipe();

        for(let i = pipes.length - 1; i >= 0; i--){
            const p = pipes[i];
            p.x -= pipeSpeed;

            const inX = player.x + player.radius > p.x && player.x - player.radius < p.x + pipeWidth;
            if(inX){
                if(player.y - player.radius < p.top || player.y + player.radius > p.top + gap){
                    gameOver();
                }
            }

            if(!p.passed && p.x + pipeWidth < player.x){
                p.passed = true;
                score++;
                scoreEl.textContent = 'Score: ' + score;
            }

            if(p.x + pipeWidth < -10) pipes.splice(i, 1);
        }

        const baseX = player.x - 80;
        humans.forEach((h,i)=>{
            const targetX = baseX + i * 18;
            h.x += (targetX - h.x) * 0.03;
        });

        if(player.y + player.radius > H - 10 || player.y - player.radius < 10){
            gameOver();
        }
    }

    function gameOver(){
        running = false;
        gameStarted = false;
        best = Math.max(best, score);
        startBtn.style.display = 'block';

        setTimeout(()=>{
            const again = confirm('Game over. Score: '+score+'\nBest: '+best+'\nPlay again?');
            if(again){
                reset();
            }
        }, 100);
    }

    function draw(){
        ctx.clearRect(0,0,W,H);

        const gradient = ctx.createLinearGradient(0,0,0,H);
        gradient.addColorStop(0,'#4a813c');
        gradient.addColorStop(1,'#1f3c1d');
        ctx.fillStyle = gradient;
        ctx.fillRect(0,0,W,H);

        ctx.strokeStyle = '#2d5a24';
        ctx.lineWidth = 6;
        for(let i = 60; i < W - 40; i += 80){
            ctx.beginPath();
            ctx.moveTo(i,0);
            ctx.quadraticCurveTo(i+10,90,i+20,170);
            ctx.stroke();
            ctx.fillStyle = '#3f6f2b';
            ctx.beginPath();
            ctx.arc(i+18,170,12,0,Math.PI*2);
            ctx.fill();
        }

        ctx.fillStyle = 'rgba(15,45,12,0.7)';
        ctx.fillRect(20,120,30,260);
        ctx.fillRect(W-60,150,40,300);
        ctx.fillRect(120,100,20,250);

        ctx.fillStyle = '#4d2f14';
        ctx.fillRect(0,H-40,W,40);
        ctx.fillStyle = '#28511f';
        for(let x=0; x<W; x+=20){
            ctx.fillRect(x,H-36,10,8);
        }

        for(const p of pipes){
            ctx.fillStyle = '#7c4f24';
            ctx.fillRect(p.x, 0, pipeWidth, p.top);
            ctx.fillRect(p.x, p.top + gap, pipeWidth, H - (p.top + gap) - 40);
            ctx.fillStyle = '#5a3a1a';
            ctx.fillRect(p.x, p.top - 8, pipeWidth, 8);
        }

        drawHuman(40, H-70, 1);
        drawHuman(20, H-90, 0.8);
        drawHuman(70, H-80, 0.9);

        drawParrot(player.x, player.y, player.radius, player.rotation);

        if(!running){
            ctx.fillStyle = 'rgba(0,0,0,0.4)';
            ctx.fillRect(40,140,W-80,H-280);
            ctx.fillStyle = '#fff';
            ctx.font = '18px sans-serif';
            if(!gameStarted){
                ctx.fillText('Press Start to begin. Use Up / Space to flap.', 60, 200);
            } else {
                ctx.fillText('Game over. Click Start to play again.', 60, 200);
            }
        }
    }

    function loop(){
        update();
        draw();
        requestAnimationFrame(loop);
    }

    function flap(){
        if(!running) return;
        player.vel = -jump;
    }

    window.addEventListener('keydown', e => {
        if(e.code === 'Space' || e.code === 'ArrowUp'){
            if(!gameStarted) return;
            flap();
            e.preventDefault();
        }
        if(e.code === 'ArrowDown'){
            if(!gameStarted) return;
            player.vel = jump;
            e.preventDefault();
        }
    });

    canvas.addEventListener('mousedown', e => {
        if(!gameStarted) return;
        if(e.button === 0){
            flap();
        } else if(e.button === 2){
            player.vel = jump;
        }
    });

    canvas.addEventListener('contextmenu', e => e.preventDefault());

    canvas.addEventListener('touchstart', e => {
        e.preventDefault();
        if(!gameStarted) return;
        flap();
    }, {passive:false});

    startBtn.addEventListener('click', reset);

    scoreEl.textContent = 'Score: 0';
    loop();
    </script>
</body>
</html>

'use client';

import { useEffect, useRef, useState } from 'react';

export default function ArrowGamePage() {
  const canvasRef = useRef(null);
  const gameRef = useRef(null);
  const [gameState, setGameState] = useState('menu'); // menu, playing, gameover
  const [score, setScore] = useState(0);
  const [highScore, setHighScore] = useState(0);
  const [level, setLevel] = useState(1);

  useEffect(() => {
    const saved = localStorage.getItem('arrowGameHighScore');
    if (saved) setHighScore(parseInt(saved));
  }, []);

  useEffect(() => {
    if (gameState !== 'playing') return;

    const canvas = canvasRef.current;
    const ctx = canvas.getContext('2d');

    const resize = () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    };
    resize();
    window.addEventListener('resize', resize);

    const game = new ArrowGame(ctx, canvas, (s) => {
      setScore(s);
    }, (finalScore, lvl) => {
      setGameState('gameover');
      setScore(finalScore);
      setLevel(lvl);
      setHighScore(prev => {
        const newHigh = Math.max(prev, finalScore);
        localStorage.setItem('arrowGameHighScore', newHigh);
        return newHigh;
      });
    }, (lvl) => setLevel(lvl));

    gameRef.current = game;
    game.start();

    return () => {
      game.destroy();
      window.removeEventListener('resize', resize);
    };
  }, [gameState]);

  const startGame = () => {
    setScore(0);
    setLevel(1);
    setGameState('playing');
  };

  return (
    <div className="game-wrapper">
      {gameState === 'menu' && (
        <div className="overlay menu-overlay">
          <div className="menu-card">
            <div className="arrow-icon">🏹</div>
            <h1 className="game-title">ARROW MASTER</h1>
            <p className="game-subtitle">3D Arrow Shooting Game</p>
            <div className="high-score-display">
              <span>Best Score</span>
              <span className="hs-val">{highScore}</span>
            </div>
            <button className="btn-play" onClick={startGame}>
              ▶ PLAY NOW
            </button>
            <div className="instructions">
              <p>📱 Tap to shoot arrows</p>
              <p>🎯 Hit the bullseye for bonus points</p>
              <p>⚡ Combo = More points!</p>
            </div>
          </div>
        </div>
      )}

      {gameState === 'playing' && (
        <>
          <canvas ref={canvasRef} className="game-canvas" />
          <div className="hud">
            <div className="hud-score">
              <span className="hud-label">SCORE</span>
              <span className="hud-value">{score}</span>
            </div>
            <div className="hud-level">
              <span className="hud-label">LEVEL</span>
              <span className="hud-value">{level}</span>
            </div>
            <div className="hud-best">
              <span className="hud-label">BEST</span>
              <span className="hud-value">{highScore}</span>
            </div>
          </div>
        </>
      )}

      {gameState === 'gameover' && (
        <div className="overlay gameover-overlay">
          <div className="menu-card">
            <div className="arrow-icon">💥</div>
            <h1 className="game-title">GAME OVER</h1>
            <div className="score-display">
              <div className="score-row">
                <span>Score</span>
                <span className="score-val">{score}</span>
              </div>
              <div className="score-row">
                <span>Level</span>
                <span className="score-val">{level}</span>
              </div>
              <div className="score-row highlight">
                <span>Best</span>
                <span className="score-val">{highScore}</span>
              </div>
            </div>
            <button className="btn-play" onClick={startGame}>
              🔄 PLAY AGAIN
            </button>
            <button className="btn-menu" onClick={() => setGameState('menu')}>
              🏠 MENU
            </button>
          </div>
        </div>
      )}

      <style jsx global>{`
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a1a; overflow: hidden; font-family: 'Arial', sans-serif; }
        .game-wrapper { position: fixed; inset: 0; background: #0a0a1a; }
        .game-canvas { display: block; width: 100%; height: 100%; touch-action: none; }

        .overlay {
          position: fixed; inset: 0;
          display: flex; align-items: center; justify-content: center;
          background: linear-gradient(135deg, #0a0a1a 0%, #1a0a2e 50%, #0a1a2e 100%);
          z-index: 10;
        }

        .menu-card {
          text-align: center;
          padding: 40px 30px;
          background: rgba(255,255,255,0.05);
          border: 1px solid rgba(255,200,0,0.3);
          border-radius: 20px;
          backdrop-filter: blur(10px);
          max-width: 340px;
          width: 90%;
          box-shadow: 0 0 40px rgba(255,200,0,0.15);
        }

        .arrow-icon {
          font-size: 64px;
          margin-bottom: 10px;
          animation: bounce 1s ease-in-out infinite alternate;
        }

        @keyframes bounce {
          from { transform: translateY(0); }
          to { transform: translateY(-10px); }
        }

        .game-title {
          font-size: 32px;
          font-weight: 900;
          color: #ffd700;
          letter-spacing: 3px;
          text-shadow: 0 0 20px rgba(255,215,0,0.5);
          margin-bottom: 5px;
        }

        .game-subtitle {
          color: rgba(255,255,255,0.6);
          font-size: 13px;
          letter-spacing: 2px;
          margin-bottom: 20px;
        }

        .high-score-display {
          display: flex;
          justify-content: space-between;
          align-items: center;
          background: rgba(255,215,0,0.1);
          border: 1px solid rgba(255,215,0,0.2);
          border-radius: 10px;
          padding: 10px 16px;
          margin-bottom: 24px;
          color: rgba(255,255,255,0.8);
          font-size: 14px;
        }

        .hs-val { color: #ffd700; font-size: 20px; font-weight: bold; }

        .btn-play {
          width: 100%;
          padding: 16px;
          background: linear-gradient(135deg, #ffd700, #ff8c00);
          border: none;
          border-radius: 12px;
          color: #000;
          font-size: 18px;
          font-weight: 900;
          letter-spacing: 2px;
          cursor: pointer;
          margin-bottom: 10px;
          transition: transform 0.1s, box-shadow 0.1s;
          box-shadow: 0 4px 20px rgba(255,140,0,0.4);
        }

        .btn-play:active { transform: scale(0.97); }

        .btn-menu {
          width: 100%;
          padding: 13px;
          background: rgba(255,255,255,0.1);
          border: 1px solid rgba(255,255,255,0.2);
          border-radius: 12px;
          color: rgba(255,255,255,0.8);
          font-size: 15px;
          font-weight: 600;
          cursor: pointer;
          transition: background 0.2s;
        }

        .btn-menu:active { background: rgba(255,255,255,0.2); }

        .instructions {
          margin-top: 20px;
          display: flex;
          flex-direction: column;
          gap: 6px;
        }

        .instructions p {
          color: rgba(255,255,255,0.5);
          font-size: 12px;
        }

        .score-display {
          margin: 16px 0 24px;
          display: flex;
          flex-direction: column;
          gap: 8px;
        }

        .score-row {
          display: flex;
          justify-content: space-between;
          padding: 10px 16px;
          background: rgba(255,255,255,0.05);
          border-radius: 8px;
          color: rgba(255,255,255,0.7);
          font-size: 15px;
        }

        .score-row.highlight {
          background: rgba(255,215,0,0.1);
          border: 1px solid rgba(255,215,0,0.2);
        }

        .score-val { font-weight: bold; color: #ffd700; font-size: 18px; }

        .hud {
          position: fixed;
          top: 0; left: 0; right: 0;
          display: flex;
          justify-content: space-around;
          padding: 16px 20px;
          background: linear-gradient(to bottom, rgba(0,0,0,0.7), transparent);
          z-index: 5;
          pointer-events: none;
        }

        .hud-score, .hud-level, .hud-best {
          display: flex;
          flex-direction: column;
          align-items: center;
        }

        .hud-label {
          font-size: 10px;
          color: rgba(255,255,255,0.5);
          letter-spacing: 2px;
        }

        .hud-value {
          font-size: 22px;
          font-weight: 900;
          color: #ffd700;
          text-shadow: 0 0 10px rgba(255,215,0,0.4);
        }
      `}</style>
    </div>
  );
}

// ====================== GAME ENGINE ======================
class ArrowGame {
  constructor(ctx, canvas, onScore, onGameOver, onLevel) {
    this.ctx = ctx;
    this.canvas = canvas;
    this.onScore = onScore;
    this.onGameOver = onGameOver;
    this.onLevel = onLevel;
    this.raf = null;
    this.running = false;
    this.score = 0;
    this.level = 1;
    this.combo = 0;
    this.lives = 3;
    this.lastTime = 0;
    this.targets = [];
    this.arrows = [];
    this.particles = [];
    this.stars = [];
    this.floatingTexts = [];
    this.spawnTimer = 0;
    this.spawnInterval = 2200;
    this.targetSpeed = 1.2;
    this.touchStart = null;
    this.camera = { z: 0, shake: 0 };
    this.bg = { grad: null };
    this.initStars();
    this.bindEvents();
  }

  initStars() {
    for (let i = 0; i < 80; i++) {
      this.stars.push({
        x: Math.random() * 2000 - 1000,
        y: Math.random() * 2000 - 1000,
        z: Math.random() * 1000,
        size: Math.random() * 1.5 + 0.5,
      });
    }
  }

  bindEvents() {
    this._onTouch = (e) => {
      e.preventDefault();
      const t = e.changedTouches[0];
      this.shoot(t.clientX, t.clientY);
    };
    this._onMouse = (e) => {
      if (e.button === 0) this.shoot(e.clientX, e.clientY);
    };
    this.canvas.addEventListener('touchend', this._onTouch, { passive: false });
    this.canvas.addEventListener('mouseup', this._onMouse);
  }

  start() {
    this.running = true;
    this.lastTime = performance.now();
    this.loop(this.lastTime);
  }

  destroy() {
    this.running = false;
    if (this.raf) cancelAnimationFrame(this.raf);
    this.canvas.removeEventListener('touchend', this._onTouch);
    this.canvas.removeEventListener('mouseup', this._onMouse);
  }

  shoot(x, y) {
    this.arrows.push({
      x: this.canvas.width / 2,
      y: this.canvas.height - 80,
      tx: x, ty: y,
      vx: 0, vy: 0,
      progress: 0,
      hit: false,
      trail: [],
    });

    const dx = x - this.canvas.width / 2;
    const dy = y - (this.canvas.height - 80);
    const dist = Math.sqrt(dx * dx + dy * dy);
    const speed = 18;
    const arrow = this.arrows[this.arrows.length - 1];
    arrow.vx = (dx / dist) * speed;
    arrow.vy = (dy / dist) * speed;

    this.spawnArrowParticles(this.canvas.width / 2, this.canvas.height - 80);
  }

  spawnArrowParticles(x, y) {
    for (let i = 0; i < 5; i++) {
      this.particles.push({
        x, y,
        vx: (Math.random() - 0.5) * 3,
        vy: -Math.random() * 3 - 1,
        life: 1,
        decay: 0.08,
        size: Math.random() * 3 + 1,
        color: `hsl(${40 + Math.random() * 20}, 100%, 60%)`,
        type: 'spark',
      });
    }
  }

  spawnHitParticles(x, y, isGold) {
    const count = isGold ? 20 : 12;
    for (let i = 0; i < count; i++) {
      const angle = (i / count) * Math.PI * 2;
      const speed = Math.random() * 5 + 2;
      this.particles.push({
        x, y,
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed,
        life: 1,
        decay: 0.04,
        size: Math.random() * 5 + 2,
        color: isGold ? `hsl(${45 + Math.random() * 20}, 100%, ${50 + Math.random() * 30}%)` : `hsl(${Math.random() * 360}, 80%, 60%)`,
        type: 'burst',
      });
    }
    // Rings
    this.particles.push({ x, y, life: 1, decay: 0.04, radius: 5, maxRadius: 80, type: 'ring', color: isGold ? '#ffd700' : '#fff' });
  }

  addFloatingText(x, y, text, color) {
    this.floatingTexts.push({ x, y, text, color, life: 1, decay: 0.025 });
  }

  spawnTarget() {
    const w = this.canvas.width;
    const h = this.canvas.height;
    const margin = 80;
    const side = Math.floor(Math.random() * 3); // top, left, right
    let x, y, vx, vy;
    const speed = this.targetSpeed + this.level * 0.15;

    if (side === 0) { // from top
      x = margin + Math.random() * (w - margin * 2);
      y = -60;
      vx = (Math.random() - 0.5) * speed * 1.5;
      vy = speed;
    } else if (side === 1) { // from left
      x = -60;
      y = margin + Math.random() * (h * 0.5);
      vx = speed;
      vy = (Math.random() - 0.5) * speed;
    } else { // from right
      x = w + 60;
      y = margin + Math.random() * (h * 0.5);
      vx = -speed;
      vy = (Math.random() - 0.5) * speed;
    }

    const isGold = Math.random() < 0.15;
    const isSmall = Math.random() < 0.25;
    const radius = isSmall ? 25 : (isGold ? 35 : 40);

    this.targets.push({
      x, y, vx, vy,
      radius,
      isGold, isSmall,
      rotation: 0,
      rotSpeed: (Math.random() - 0.5) * 0.08,
      pulsePhase: Math.random() * Math.PI * 2,
      hit: false,
      opacity: 1,
      scale: 0, scaleTarget: 1,
      z: Math.random() * 0.5 + 0.75, // depth simulation
    });
  }

  update(dt) {
    const w = this.canvas.width;
    const h = this.canvas.height;

    // Camera shake decay
    this.camera.shake *= 0.85;

    // Spawn targets
    this.spawnTimer += dt;
    if (this.spawnTimer >= this.spawnInterval) {
      this.spawnTimer = 0;
      this.spawnTarget();
      if (this.level > 2) this.spawnTarget(); // extra target at higher levels
      this.spawnInterval = Math.max(800, 2200 - this.level * 120);
    }

    // Update targets
    for (let i = this.targets.length - 1; i >= 0; i--) {
      const t = this.targets[i];
      if (t.hit) {
        t.opacity -= 0.05;
        t.scale += 0.06;
        if (t.opacity <= 0) { this.targets.splice(i, 1); }
        continue;
      }
      t.scale += (t.scaleTarget - t.scale) * 0.15;
      t.x += t.vx;
      t.y += t.vy;
      t.rotation += t.rotSpeed;
      t.pulsePhase += 0.05;

      // Bounce off walls
      if (t.x - t.radius < 0 || t.x + t.radius > w) t.vx *= -1;
      if (t.y - t.radius < 0) t.vy *= -1;

      // Missed - target goes out of bottom
      if (t.y - t.radius > h + 50) {
        this.targets.splice(i, 1);
        this.combo = 0;
        this.lives--;
        this.camera.shake = 12;
        if (this.lives <= 0) {
          this.running = false;
          this.onGameOver(this.score, this.level);
          return;
        }
      }
    }

    // Update arrows
    for (let i = this.arrows.length - 1; i >= 0; i--) {
      const a = this.arrows[i];
      a.trail.push({ x: a.x, y: a.y });
      if (a.trail.length > 8) a.trail.shift();
      a.x += a.vx;
      a.y += a.vy;
      a.vy += 0.4; // gravity

      // Check hit
      let hitTarget = false;
      for (let j = this.targets.length - 1; j >= 0; j--) {
        const t = this.targets[j];
        if (t.hit) continue;
        const dx = a.x - t.x;
        const dy = a.y - t.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < t.radius * t.scale) {
          t.hit = true;
          hitTarget = true;
          this.combo++;
          const bullseye = dist < t.radius * 0.35 * t.scale;
          const comboBonus = Math.min(this.combo, 10);
          let pts = bullseye ? 50 : (t.isSmall ? 30 : (t.isGold ? 25 : 15));
          pts *= comboBonus;
          if (t.isGold) pts *= 2;
          this.score += pts;
          this.onScore(this.score);

          // Level up
          const newLevel = Math.floor(this.score / 200) + 1;
          if (newLevel > this.level) {
            this.level = newLevel;
            this.targetSpeed = 1.2 + newLevel * 0.2;
            this.onLevel(newLevel);
          }

          this.spawnHitParticles(t.x, t.y, t.isGold || bullseye);
          this.camera.shake = bullseye ? 8 : 4;

          const label = bullseye ? `BULLSEYE! +${pts}` : (this.combo > 2 ? `x${this.combo} COMBO! +${pts}` : `+${pts}`);
          const color = bullseye ? '#ffd700' : (t.isGold ? '#ff8c00' : '#ffffff');
          this.addFloatingText(t.x, t.y, label, color);
          break;
        }
      }

      // Remove arrow if off screen or hit
      if (hitTarget || a.x < -50 || a.x > w + 50 || a.y < -100 || a.y > h + 100) {
        this.arrows.splice(i, 1);
      }
    }

    // Update particles
    for (let i = this.particles.length - 1; i >= 0; i--) {
      const p = this.particles[i];
      p.life -= p.decay;
      if (p.life <= 0) { this.particles.splice(i, 1); continue; }
      if (p.type !== 'ring') {
        p.x += p.vx;
        p.y += p.vy;
        p.vy += 0.15;
        p.vx *= 0.97;
      } else {
        p.radius += (p.maxRadius - p.radius) * 0.1;
      }
    }

    // Update floating texts
    for (let i = this.floatingTexts.length - 1; i >= 0; i--) {
      const ft = this.floatingTexts[i];
      ft.life -= ft.decay;
      ft.y -= 1.5;
      if (ft.life <= 0) this.floatingTexts.splice(i, 1);
    }
  }

  draw() {
    const ctx = this.ctx;
    const w = this.canvas.width;
    const h = this.canvas.height;

    // Camera shake
    const shakeX = (Math.random() - 0.5) * this.camera.shake;
    const shakeY = (Math.random() - 0.5) * this.camera.shake;
    ctx.save();
    ctx.translate(shakeX, shakeY);

    // Background gradient
    const grad = ctx.createLinearGradient(0, 0, 0, h);
    grad.addColorStop(0, '#050510');
    grad.addColorStop(0.5, '#0d0820');
    grad.addColorStop(1, '#0a1525');
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, w, h);

    // Draw 3D stars (perspective)
    this.drawStars(w, h);

    // Draw ground glow
    const groundGrad = ctx.createRadialGradient(w / 2, h, 0, w / 2, h, w * 0.6);
    groundGrad.addColorStop(0, 'rgba(100,50,200,0.15)');
    groundGrad.addColorStop(1, 'transparent');
    ctx.fillStyle = groundGrad;
    ctx.fillRect(0, h - 150, w, 150);

    // Grid lines (3D perspective ground)
    this.drawGrid(w, h);

    // Bow/shooter at bottom
    this.drawShooter(w, h);

    // Particles
    this.drawParticles();

    // Targets
    for (const t of this.targets) this.drawTarget(t);

    // Arrows
    for (const a of this.arrows) this.drawArrow(a);

    // Floating texts
    for (const ft of this.floatingTexts) {
      ctx.globalAlpha = ft.life;
      ctx.fillStyle = ft.color;
      ctx.font = `bold ${ft.life > 0.5 ? 18 : 14}px Arial`;
      ctx.textAlign = 'center';
      ctx.shadowColor = ft.color;
      ctx.shadowBlur = 10;
      ctx.fillText(ft.text, ft.x, ft.y);
      ctx.shadowBlur = 0;
    }
    ctx.globalAlpha = 1;

    // Lives hearts
    this.drawLives(w);

    ctx.restore();
  }

  drawStars(w, h) {
    const ctx = this.ctx;
    const cx = w / 2, cy = h / 2;
    const fov = 300;

    for (const star of this.stars) {
      star.z -= 1.5;
      if (star.z <= 0) star.z = 1000;

      const sx = cx + (star.x / star.z) * fov;
      const sy = cy + (star.y / star.z) * fov;
      const size = (1 - star.z / 1000) * star.size * 3;
      const brightness = 1 - star.z / 1000;

      if (sx < 0 || sx > w || sy < 0 || sy > h) continue;

      ctx.beginPath();
      ctx.arc(sx, sy, Math.max(0.3, size), 0, Math.PI * 2);
      ctx.fillStyle = `rgba(255,255,255,${brightness * 0.8})`;
      ctx.fill();
    }
  }

  drawGrid(w, h) {
    const ctx = this.ctx;
    const horizon = h * 0.55;
    ctx.strokeStyle = 'rgba(80,40,200,0.2)';
    ctx.lineWidth = 0.5;

    const lines = 10;
    for (let i = 0; i <= lines; i++) {
      const t = i / lines;
      const x = t * w;
      ctx.beginPath();
      ctx.moveTo(x, horizon);
      ctx.lineTo(w / 2 + (x - w / 2) * 3, h + 50);
      ctx.stroke();
    }

    for (let i = 0; i < 6; i++) {
      const t = Math.pow(i / 6, 1.5);
      const y = horizon + (h - horizon + 50) * t;
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(w, y);
      ctx.stroke();
    }
  }

  drawShooter(w, h) {
    const ctx = this.ctx;
    const cx = w / 2;
    const cy = h - 60;

    // Glow ring
    const glow = ctx.createRadialGradient(cx, cy, 5, cx, cy, 40);
    glow.addColorStop(0, 'rgba(255,200,0,0.3)');
    glow.addColorStop(1, 'transparent');
    ctx.fillStyle = glow;
    ctx.beginPath();
    ctx.arc(cx, cy, 40, 0, Math.PI * 2);
    ctx.fill();

    // Platform
    ctx.fillStyle = 'rgba(255,200,0,0.15)';
    ctx.beginPath();
    ctx.ellipse(cx, cy + 15, 35, 10, 0, 0, Math.PI * 2);
    ctx.fill();

    // Bow body (arc)
    ctx.strokeStyle = '#c8860a';
    ctx.lineWidth = 5;
    ctx.lineCap = 'round';
    ctx.beginPath();
    ctx.arc(cx, cy, 22, Math.PI * 1.1, Math.PI * 1.9);
    ctx.stroke();

    // Bow string
    ctx.strokeStyle = '#e8c060';
    ctx.lineWidth = 1.5;
    ctx.beginPath();
    ctx.moveTo(cx - 18, cy - 8);
    ctx.lineTo(cx, cy - 5);
    ctx.lineTo(cx + 18, cy - 8);
    ctx.stroke();

    // Arrow on bow
    ctx.strokeStyle = '#ffd700';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(cx - 5, cy - 5);
    ctx.lineTo(cx + 14, cy - 5);
    ctx.stroke();
    // Arrowhead
    ctx.fillStyle = '#ffd700';
    ctx.beginPath();
    ctx.moveTo(cx + 14, cy - 5);
    ctx.lineTo(cx + 8, cy - 9);
    ctx.lineTo(cx + 8, cy - 1);
    ctx.fill();
  }

  drawTarget(t) {
    const ctx = this.ctx;
    ctx.save();
    ctx.translate(t.x, t.y);
    ctx.rotate(t.rotation);
    ctx.scale(t.scale, t.scale);
    ctx.globalAlpha = t.opacity;

    const pulse = Math.sin(t.pulsePhase) * 0.06;
    const r = t.radius * (1 + pulse);

    // Shadow/glow
    const glowColor = t.isGold ? 'rgba(255,180,0,0.3)' : (t.isSmall ? 'rgba(0,200,255,0.25)' : 'rgba(255,50,50,0.2)');
    const glowGrad = ctx.createRadialGradient(0, 0, r * 0.5, 0, 0, r * 1.8);
    glowGrad.addColorStop(0, glowColor);
    glowGrad.addColorStop(1, 'transparent');
    ctx.fillStyle = glowGrad;
    ctx.beginPath();
    ctx.arc(0, 0, r * 1.8, 0, Math.PI * 2);
    ctx.fill();

    // Target rings (3D concentric)
    const rings = [
      { r: r, color: t.isGold ? '#ff4400' : '#cc0000' },
      { r: r * 0.78, color: t.isGold ? '#ff8800' : '#ffffff' },
      { r: r * 0.56, color: t.isGold ? '#ffaa00' : '#cc0000' },
      { r: r * 0.34, color: t.isGold ? '#ffcc00' : '#ffffff' },
      { r: r * 0.16, color: t.isGold ? '#ffd700' : '#ffcc00' },
    ];

    for (const ring of rings) {
      ctx.beginPath();
      ctx.arc(0, 0, ring.r, 0, Math.PI * 2);
      ctx.fillStyle = ring.color;
      ctx.fill();
      ctx.strokeStyle = 'rgba(0,0,0,0.2)';
      ctx.lineWidth = 1;
      ctx.stroke();
    }

    // 3D highlight
    const highlight = ctx.createRadialGradient(-r * 0.25, -r * 0.25, 0, 0, 0, r);
    highlight.addColorStop(0, 'rgba(255,255,255,0.35)');
    highlight.addColorStop(0.5, 'rgba(255,255,255,0.05)');
    highlight.addColorStop(1, 'rgba(0,0,0,0.2)');
    ctx.fillStyle = highlight;
    ctx.beginPath();
    ctx.arc(0, 0, r, 0, Math.PI * 2);
    ctx.fill();

    // Special indicators
    if (t.isGold) {
      ctx.strokeStyle = '#ffd700';
      ctx.lineWidth = 2;
      ctx.beginPath();
      ctx.arc(0, 0, r + 4 + Math.sin(t.pulsePhase * 2) * 2, 0, Math.PI * 2);
      ctx.stroke();
    }
    if (t.isSmall) {
      ctx.strokeStyle = '#00ccff';
      ctx.lineWidth = 1.5;
      ctx.setLineDash([4, 3]);
      ctx.beginPath();
      ctx.arc(0, 0, r + 5, 0, Math.PI * 2);
      ctx.stroke();
      ctx.setLineDash([]);
    }

    ctx.restore();
  }

  drawArrow(a) {
    const ctx = this.ctx;

    // Trail
    for (let i = 0; i < a.trail.length - 1; i++) {
      const t = i / a.trail.length;
      ctx.beginPath();
      ctx.moveTo(a.trail[i].x, a.trail[i].y);
      ctx.lineTo(a.trail[i + 1].x, a.trail[i + 1].y);
      ctx.strokeStyle = `rgba(255,200,50,${t * 0.4})`;
      ctx.lineWidth = t * 3;
      ctx.lineCap = 'round';
      ctx.stroke();
    }

    // Arrow direction
    const dx = a.vx, dy = a.vy;
    const angle = Math.atan2(dy, dx);

    ctx.save();
    ctx.translate(a.x, a.y);
    ctx.rotate(angle);

    // Shaft
    ctx.strokeStyle = '#c8a060';
    ctx.lineWidth = 3;
    ctx.lineCap = 'round';
    ctx.beginPath();
    ctx.moveTo(-18, 0);
    ctx.lineTo(8, 0);
    ctx.stroke();

    // Head
    ctx.fillStyle = '#e0e0e0';
    ctx.beginPath();
    ctx.moveTo(8, 0);
    ctx.lineTo(0, -4);
    ctx.lineTo(14, 0);
    ctx.lineTo(0, 4);
    ctx.fill();

    // Feathers
    ctx.fillStyle = '#ff6622';
    ctx.beginPath();
    ctx.moveTo(-14, 0);
    ctx.lineTo(-18, -5);
    ctx.lineTo(-12, -1);
    ctx.fill();
    ctx.beginPath();
    ctx.moveTo(-14, 0);
    ctx.lineTo(-18, 5);
    ctx.lineTo(-12, 1);
    ctx.fill();

    ctx.restore();
  }

  drawParticles() {
    const ctx = this.ctx;
    for (const p of this.particles) {
      ctx.globalAlpha = p.life;
      if (p.type === 'ring') {
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
        ctx.strokeStyle = p.color;
        ctx.lineWidth = 2 * p.life;
        ctx.stroke();
      } else {
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size * p.life, 0, Math.PI * 2);
        ctx.fillStyle = p.color;
        ctx.shadowColor = p.color;
        ctx.shadowBlur = 6;
        ctx.fill();
        ctx.shadowBlur = 0;
      }
    }
    ctx.globalAlpha = 1;
  }

  drawLives(w) {
    const ctx = this.ctx;
    const maxLives = 3;
    const iconSize = 22;
    const startX = w - 20 - maxLives * (iconSize + 6);
    const y = 50;

    for (let i = 0; i < maxLives; i++) {
      const x = startX + i * (iconSize + 6);
      ctx.globalAlpha = i < this.lives ? 1 : 0.2;
      ctx.font = `${iconSize}px Arial`;
      ctx.textAlign = 'left';
      ctx.fillText('❤️', x, y);
    }
    ctx.globalAlpha = 1;
  }

  loop(timestamp) {
    if (!this.running) return;
    const dt = Math.min(timestamp - this.lastTime, 32);
    this.lastTime = timestamp;
    this.update(dt);
    this.draw();
    this.raf = requestAnimationFrame((t) => this.loop(t));
  }
}

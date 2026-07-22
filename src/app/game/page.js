'use client';

import { useEffect, useRef, useState, useCallback } from 'react';

export default function ArrowGamePage() {
  const canvasRef = useRef(null);
  const gameRef = useRef(null);
  const [gameState, setGameState] = useState('menu');
  const [score, setScore] = useState(0);
  const [highScore, setHighScore] = useState(0);
  const [level, setLevel] = useState(1);
  const [combo, setCombo] = useState(0);
  const [powerUp, setPowerUp] = useState(null); // null | 'multishot' | 'slowmo' | 'shield'
  const [powerUpTime, setPowerUpTime] = useState(0);
  const [lives, setLives] = useState(3);

  useEffect(() => {
    const saved = localStorage.getItem('arrowGameHighScore');
    if (saved) setHighScore(parseInt(saved));
  }, []);

  const startGame = useCallback(() => {
    setScore(0);
    setLevel(1);
    setCombo(0);
    setLives(3);
    setPowerUp(null);
    setPowerUpTime(0);
    setGameState('playing');
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

    const game = new ArrowGame(ctx, canvas, {
      onScore: (s) => setScore(s),
      onGameOver: (finalScore, lvl) => {
        setGameState('gameover');
        setScore(finalScore);
        setLevel(lvl);
        setHighScore(prev => {
          const newHigh = Math.max(prev, finalScore);
          localStorage.setItem('arrowGameHighScore', newHigh);
          return newHigh;
        });
      },
      onLevel: (lvl) => setLevel(lvl),
      onCombo: (c) => setCombo(c),
      onLives: (l) => setLives(l),
      onPowerUp: (type, timeLeft) => {
        setPowerUp(type);
        setPowerUpTime(timeLeft);
      },
    });

    gameRef.current = game;
    game.start();

    return () => {
      game.destroy();
      window.removeEventListener('resize', resize);
    };
  }, [gameState]);

  return (
    <div className="game-wrapper">
      {gameState === 'menu' && <MenuScreen highScore={highScore} onPlay={startGame} />}

      {gameState === 'playing' && (
        <>
          <canvas ref={canvasRef} className="game-canvas" />
          <HUD score={score} level={level} highScore={highScore} lives={lives} combo={combo} powerUp={powerUp} powerUpTime={powerUpTime} />
        </>
      )}

      {gameState === 'gameover' && (
        <GameOverScreen score={score} level={level} highScore={highScore} onPlay={startGame} onMenu={() => setGameState('menu')} />
      )}

      <style jsx global>{`
        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { background: #050510; overflow: hidden; font-family: 'Arial', sans-serif; user-select: none; }
        .game-wrapper { position: fixed; inset: 0; background: #050510; }
        .game-canvas { display: block; width: 100%; height: 100%; touch-action: none; }
      `}</style>
    </div>
  );
}

// ── UI Components ───────────────────────────────────────────────────────────

function MenuScreen({ highScore, onPlay }) {
  return (
    <div style={styles.overlay}>
      <div style={styles.card}>
        <div style={styles.bigIcon}>🏹</div>
        <h1 style={styles.title}>ARROW MASTER</h1>
        <p style={styles.subtitle}>3D SHOOTING GAME</p>

        <div style={styles.infoRow}>
          <span style={styles.infoLabel}>BEST</span>
          <span style={styles.infoVal}>{highScore}</span>
        </div>

        <button style={styles.btnPrimary} onClick={onPlay}>▶ PLAY</button>

        <div style={styles.tipBox}>
          <TipRow icon="🎯" text="Bullseye = 50x combo points!" />
          <TipRow icon="⚡" text="Combo streak multiplies score" />
          <TipRow icon="🌟" text="Gold targets = 2x bonus" />
          <TipRow icon="💎" text="Catch power-ups for special skills" />
          <TipRow icon="🔵" text="Small blue targets = 30 pts bonus" />
        </div>
      </div>
    </div>
  );
}

function TipRow({ icon, text }) {
  return (
    <div style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '4px 0' }}>
      <span style={{ fontSize: 16 }}>{icon}</span>
      <span style={{ color: 'rgba(255,255,255,0.55)', fontSize: 12 }}>{text}</span>
    </div>
  );
}

function GameOverScreen({ score, level, highScore, onPlay, onMenu }) {
  const isNewBest = score >= highScore;
  return (
    <div style={styles.overlay}>
      <div style={styles.card}>
        <div style={styles.bigIcon}>{isNewBest ? '🏆' : '💥'}</div>
        <h1 style={styles.title}>{isNewBest ? 'NEW BEST!' : 'GAME OVER'}</h1>
        {isNewBest && <p style={{ color: '#ffd700', fontSize: 13, marginBottom: 8, letterSpacing: 2 }}>RECORD BROKEN!</p>}

        <div style={styles.scoreBoard}>
          <ScoreRow label="Score" value={score} gold={isNewBest} />
          <ScoreRow label="Level" value={level} />
          <ScoreRow label="Best" value={highScore} gold />
        </div>

        <button style={styles.btnPrimary} onClick={onPlay}>🔄 PLAY AGAIN</button>
        <button style={styles.btnSecondary} onClick={onMenu}>🏠 MENU</button>
      </div>
    </div>
  );
}

function ScoreRow({ label, value, gold }) {
  return (
    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '10px 16px', background: gold ? 'rgba(255,215,0,0.08)' : 'rgba(255,255,255,0.04)', borderRadius: 8, marginBottom: 6, border: gold ? '1px solid rgba(255,215,0,0.15)' : '1px solid transparent' }}>
      <span style={{ color: 'rgba(255,255,255,0.6)', fontSize: 14 }}>{label}</span>
      <span style={{ color: gold ? '#ffd700' : '#fff', fontSize: 20, fontWeight: 900 }}>{value}</span>
    </div>
  );
}

function HUD({ score, level, highScore, lives, combo, powerUp, powerUpTime }) {
  const powerColors = { multishot: '#00cfff', slowmo: '#aa44ff', shield: '#44ff88' };
  const powerIcons = { multishot: '⚡', slowmo: '🌀', shield: '🛡️' };

  return (
    <div style={{ position: 'fixed', top: 0, left: 0, right: 0, zIndex: 5, pointerEvents: 'none' }}>
      {/* Top bar */}
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', padding: '14px 18px', background: 'linear-gradient(to bottom, rgba(0,0,0,0.75), transparent)' }}>
        <HudStat label="SCORE" value={score} />
        <HudStat label="LVL" value={level} center />
        <HudStat label="BEST" value={highScore} />
      </div>

      {/* Lives */}
      <div style={{ position: 'absolute', top: 14, left: '50%', transform: 'translateX(-50%)', display: 'flex', gap: 4 }}>
        {[0, 1, 2].map(i => (
          <span key={i} style={{ fontSize: 18, opacity: i < lives ? 1 : 0.2, filter: i < lives ? 'drop-shadow(0 0 4px red)' : 'none' }}>❤️</span>
        ))}
      </div>

      {/* Combo */}
      {combo > 1 && (
        <div style={{ textAlign: 'center', marginTop: -8 }}>
          <span style={{ fontSize: combo > 5 ? 22 : 16, fontWeight: 900, color: combo > 5 ? '#ffd700' : '#ff8844', textShadow: '0 0 12px currentColor', letterSpacing: 2, animation: combo > 5 ? 'none' : 'none' }}>
            x{Math.min(combo, 10)} COMBO{combo > 5 ? ' 🔥' : ''}
          </span>
        </div>
      )}

      {/* Power-up bar */}
      {powerUp && (
        <div style={{ textAlign: 'center', marginTop: 6 }}>
          <div style={{ display: 'inline-flex', alignItems: 'center', gap: 6, background: `${powerColors[powerUp]}22`, border: `1px solid ${powerColors[powerUp]}55`, borderRadius: 20, padding: '4px 14px' }}>
            <span style={{ fontSize: 16 }}>{powerIcons[powerUp]}</span>
            <div style={{ width: 80, height: 4, background: 'rgba(255,255,255,0.15)', borderRadius: 2, overflow: 'hidden' }}>
              <div style={{ width: `${powerUpTime * 100}%`, height: '100%', background: powerColors[powerUp], borderRadius: 2, transition: 'width 0.1s' }} />
            </div>
            <span style={{ color: powerColors[powerUp], fontSize: 11, fontWeight: 700, letterSpacing: 1 }}>{powerUp.toUpperCase()}</span>
          </div>
        </div>
      )}
    </div>
  );
}

function HudStat({ label, value, center }) {
  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: center ? 'center' : undefined, minWidth: 60 }}>
      <span style={{ fontSize: 9, color: 'rgba(255,255,255,0.4)', letterSpacing: 2 }}>{label}</span>
      <span style={{ fontSize: 22, fontWeight: 900, color: '#ffd700', textShadow: '0 0 8px rgba(255,215,0,0.4)', lineHeight: 1.1 }}>{value}</span>
    </div>
  );
}

const styles = {
  overlay: { position: 'fixed', inset: 0, display: 'flex', alignItems: 'center', justifyContent: 'center', background: 'linear-gradient(135deg, #050510 0%, #1a0a2e 50%, #050e1a 100%)', zIndex: 10 },
  card: { textAlign: 'center', padding: '36px 28px', background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(255,200,0,0.2)', borderRadius: 24, backdropFilter: 'blur(12px)', maxWidth: 340, width: '90%', boxShadow: '0 0 60px rgba(255,200,0,0.1), inset 0 1px 0 rgba(255,255,255,0.05)' },
  bigIcon: { fontSize: 64, marginBottom: 8, display: 'block' },
  title: { fontSize: 30, fontWeight: 900, color: '#ffd700', letterSpacing: 4, textShadow: '0 0 30px rgba(255,215,0,0.5)', marginBottom: 4 },
  subtitle: { color: 'rgba(255,255,255,0.4)', fontSize: 11, letterSpacing: 3, marginBottom: 20 },
  infoRow: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: 'rgba(255,215,0,0.08)', border: '1px solid rgba(255,215,0,0.15)', borderRadius: 10, padding: '10px 16px', marginBottom: 20 },
  infoLabel: { color: 'rgba(255,255,255,0.5)', fontSize: 13, letterSpacing: 1 },
  infoVal: { color: '#ffd700', fontSize: 22, fontWeight: 900 },
  btnPrimary: { width: '100%', padding: '15px', background: 'linear-gradient(135deg, #ffd700, #ff8c00)', border: 'none', borderRadius: 14, color: '#000', fontSize: 17, fontWeight: 900, letterSpacing: 2, cursor: 'pointer', marginBottom: 10, boxShadow: '0 4px 24px rgba(255,140,0,0.35)' },
  btnSecondary: { width: '100%', padding: '12px', background: 'rgba(255,255,255,0.07)', border: '1px solid rgba(255,255,255,0.15)', borderRadius: 14, color: 'rgba(255,255,255,0.7)', fontSize: 14, fontWeight: 600, cursor: 'pointer' },
  scoreBoard: { margin: '16px 0 22px' },
  tipBox: { marginTop: 20, textAlign: 'left', borderTop: '1px solid rgba(255,255,255,0.06)', paddingTop: 14 },
};

// ═══════════════════════════════════════════════════════════════════════════════
//  GAME ENGINE
// ═══════════════════════════════════════════════════════════════════════════════

class ArrowGame {
  constructor(ctx, canvas, cbs) {
    this.ctx = ctx;
    this.canvas = canvas;
    this.cb = cbs;

    // State
    this.running = false;
    this.raf = null;
    this.lastTime = 0;
    this.score = 0;
    this.level = 1;
    this.combo = 0;
    this.comboTimer = 0;
    this.lives = 3;

    // Game objects
    this.targets = [];
    this.arrows = [];
    this.particles = [];
    this.stars = [];
    this.floatingTexts = [];
    this.powerUpDrops = [];
    this.shockwaves = [];

    // Timers
    this.spawnTimer = 0;
    this.spawnInterval = 2000;
    this.targetSpeed = 1.3;

    // Power-up state
    this.activePowerUp = null;
    this.powerUpDuration = 0;
    this.powerUpMax = 0;

    // Aiming
    this.aimX = canvas.width / 2;
    this.aimY = canvas.height / 2;
    this.aiming = false;
    this.bowX = canvas.width / 2;
    this.bowY = canvas.height - 65;
    this.bowTargetX = canvas.width / 2;

    // Camera
    this.shake = 0;

    // Sounds
    this.audioCtx = null;

    this.initStars();
    this.bindEvents();
  }

  // ── Audio ──────────────────────────────────────────────────────────────────

  getAudio() {
    if (!this.audioCtx) {
      try { this.audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch (_) {}
    }
    return this.audioCtx;
  }

  playTone(freq, type, duration, vol = 0.3, startFreq) {
    const ac = this.getAudio();
    if (!ac) return;
    try {
      const osc = ac.createOscillator();
      const gain = ac.createGain();
      osc.connect(gain);
      gain.connect(ac.destination);
      osc.type = type;
      osc.frequency.setValueAtTime(startFreq || freq, ac.currentTime);
      if (startFreq) osc.frequency.exponentialRampToValueAtTime(freq, ac.currentTime + duration * 0.5);
      gain.gain.setValueAtTime(vol, ac.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + duration);
      osc.start(ac.currentTime);
      osc.stop(ac.currentTime + duration);
    } catch (_) {}
  }

  soundShoot() { this.playTone(280, 'sawtooth', 0.12, 0.15, 600); }
  soundHit(isGold) { this.playTone(isGold ? 880 : 520, 'sine', 0.25, 0.25); }
  soundBullseye() {
    this.playTone(660, 'sine', 0.1, 0.3);
    setTimeout(() => this.playTone(880, 'sine', 0.15, 0.3), 80);
    setTimeout(() => this.playTone(1100, 'sine', 0.2, 0.3), 160);
  }
  soundMiss() { this.playTone(120, 'triangle', 0.3, 0.2, 200); }
  soundPowerUp() { this.playTone(1200, 'sine', 0.35, 0.2, 400); }
  soundLevelUp() {
    [400, 600, 800, 1000].forEach((f, i) => setTimeout(() => this.playTone(f, 'sine', 0.2, 0.25), i * 80));
  }
  soundCombo(n) { this.playTone(200 + n * 60, 'square', 0.08, 0.1); }

  // ── Stars ──────────────────────────────────────────────────────────────────

  initStars() {
    for (let i = 0; i < 100; i++) {
      this.stars.push({ x: (Math.random() - 0.5) * 2000, y: (Math.random() - 0.5) * 2000, z: Math.random() * 1000, size: Math.random() * 1.5 + 0.3 });
    }
  }

  // ── Input ──────────────────────────────────────────────────────────────────

  bindEvents() {
    this._onTouchStart = (e) => {
      e.preventDefault();
      const t = e.touches[0];
      this.aiming = true;
      this.aimX = t.clientX;
      this.aimY = t.clientY;
      this.bowTargetX = t.clientX;
    };
    this._onTouchMove = (e) => {
      e.preventDefault();
      const t = e.touches[0];
      this.aimX = t.clientX;
      this.aimY = t.clientY;
      this.bowTargetX = t.clientX;
    };
    this._onTouchEnd = (e) => {
      e.preventDefault();
      if (this.aiming) {
        const t = e.changedTouches[0];
        this.shoot(t.clientX, t.clientY);
        this.aiming = false;
      }
    };
    this._onMouseMove = (e) => {
      this.aimX = e.clientX;
      this.aimY = e.clientY;
      this.bowTargetX = e.clientX;
      if (e.buttons === 1) this.aiming = true;
    };
    this._onMouseDown = (e) => { if (e.button === 0) { this.aiming = true; } };
    this._onMouseUp = (e) => {
      if (e.button === 0 && this.aiming) {
        this.shoot(e.clientX, e.clientY);
        this.aiming = false;
      }
    };
    this.canvas.addEventListener('touchstart', this._onTouchStart, { passive: false });
    this.canvas.addEventListener('touchmove', this._onTouchMove, { passive: false });
    this.canvas.addEventListener('touchend', this._onTouchEnd, { passive: false });
    this.canvas.addEventListener('mousemove', this._onMouseMove);
    this.canvas.addEventListener('mousedown', this._onMouseDown);
    this.canvas.addEventListener('mouseup', this._onMouseUp);
  }

  // ── Shoot ──────────────────────────────────────────────────────────────────

  shoot(tx, ty) {
    const bx = this.bowX;
    const by = this.bowY;
    const dx = tx - bx;
    const dy = ty - by;
    const dist = Math.sqrt(dx * dx + dy * dy) || 1;
    const speed = 22;

    const makeArrow = (angleOffset = 0) => {
      const cos = Math.cos(angleOffset), sin = Math.sin(angleOffset);
      const vx = ((dx / dist) * speed) * cos - ((dy / dist) * speed) * sin;
      const vy = ((dx / dist) * speed) * sin + ((dy / dist) * speed) * cos;
      return { x: bx, y: by, vx, vy, tx, ty, trail: [], hit: false };
    };

    if (this.activePowerUp === 'multishot') {
      this.arrows.push(makeArrow(0), makeArrow(-0.15), makeArrow(0.15));
    } else {
      this.arrows.push(makeArrow(0));
    }

    this.soundShoot();
    this.spawnMuzzleParticles(bx, by);
  }

  spawnMuzzleParticles(x, y) {
    for (let i = 0; i < 8; i++) {
      this.particles.push({ x, y, vx: (Math.random() - 0.5) * 4, vy: -Math.random() * 4 - 1, life: 1, decay: 0.09, size: Math.random() * 3 + 1, color: `hsl(${35 + Math.random() * 25},100%,60%)`, type: 'spark' });
    }
  }

  // ── Spawn ──────────────────────────────────────────────────────────────────

  spawnTarget() {
    const w = this.canvas.width, h = this.canvas.height;
    const margin = 80;
    const speed = this.targetSpeed + this.level * 0.18;
    const side = Math.floor(Math.random() * 3);
    let x, y, vx, vy;

    if (side === 0) { x = margin + Math.random() * (w - margin * 2); y = -60; vx = (Math.random() - 0.5) * speed * 1.5; vy = speed; }
    else if (side === 1) { x = -60; y = 60 + Math.random() * (h * 0.55); vx = speed; vy = (Math.random() - 0.5) * speed * 0.8; }
    else { x = w + 60; y = 60 + Math.random() * (h * 0.55); vx = -speed; vy = (Math.random() - 0.5) * speed * 0.8; }

    const roll = Math.random();
    const isGold = roll < 0.12;
    const isSmall = roll < 0.30 && !isGold;
    const isBig = roll > 0.88;
    const isZigzag = this.level > 3 && Math.random() < 0.25;
    const radius = isSmall ? 22 : (isBig ? 52 : (isGold ? 36 : 40));
    const hp = isBig ? 3 : 1;

    this.targets.push({ x, y, vx, vy, radius, isGold, isSmall, isBig, isZigzag, zigTimer: 0, rotation: 0, rotSpeed: (Math.random() - 0.5) * 0.07, pulsePhase: Math.random() * Math.PI * 2, hit: false, opacity: 1, scale: 0, hp, maxHp: hp });
  }

  spawnPowerUp(x, y) {
    const types = ['multishot', 'slowmo', 'shield'];
    const type = types[Math.floor(Math.random() * types.length)];
    this.powerUpDrops.push({ x, y, vy: -2, type, bobPhase: 0, life: 1, scale: 0 });
  }

  // ── Update ─────────────────────────────────────────────────────────────────

  update(dt) {
    const w = this.canvas.width, h = this.canvas.height;

    this.shake *= 0.82;

    // Bow smooth follow
    this.bowX += (this.bowTargetX - this.bowX) * 0.12;

    // Combo decay
    if (this.combo > 0) {
      this.comboTimer += dt;
      if (this.comboTimer > 3000) { this.combo = 0; this.cb.onCombo(0); }
    }

    // Power-up timer
    if (this.activePowerUp) {
      this.powerUpDuration -= dt;
      this.cb.onPowerUp(this.activePowerUp, Math.max(0, this.powerUpDuration / this.powerUpMax));
      if (this.powerUpDuration <= 0) {
        this.activePowerUp = null;
        this.cb.onPowerUp(null, 0);
      }
    }

    // Slowmo
    const timeScale = (this.activePowerUp === 'slowmo') ? 0.35 : 1;

    // Spawn
    this.spawnTimer += dt;
    if (this.spawnTimer >= this.spawnInterval) {
      this.spawnTimer = 0;
      this.spawnTarget();
      if (this.level >= 3) this.spawnTarget();
      if (this.level >= 6) this.spawnTarget();
      this.spawnInterval = Math.max(700, 2000 - this.level * 100);
    }

    // Targets
    for (let i = this.targets.length - 1; i >= 0; i--) {
      const t = this.targets[i];
      if (t.hit) {
        t.opacity -= 0.06;
        t.scale += 0.08;
        if (t.opacity <= 0) this.targets.splice(i, 1);
        continue;
      }
      t.scale += (1 - t.scale) * 0.15;
      t.x += t.vx * timeScale;
      t.y += t.vy * timeScale;
      t.rotation += t.rotSpeed;
      t.pulsePhase += 0.05;

      if (t.isZigzag) {
        t.zigTimer += dt;
        t.vx += Math.sin(t.zigTimer * 0.004) * 0.25;
        t.vx = Math.max(-8, Math.min(8, t.vx));
      }

      if (t.x - t.radius < 0 || t.x + t.radius > w) t.vx *= -1;
      if (t.y - t.radius < 0) t.vy *= -1;

      if (t.y - t.radius > h + 60) {
        this.targets.splice(i, 1);
        if (this.activePowerUp !== 'shield') {
          this.combo = 0;
          this.comboTimer = 0;
          this.cb.onCombo(0);
          this.lives--;
          this.cb.onLives(this.lives);
          this.shake = 14;
          this.soundMiss();
          if (this.lives <= 0) { this.running = false; this.cb.onGameOver(this.score, this.level); return; }
        }
      }
    }

    // Power-up drops
    for (let i = this.powerUpDrops.length - 1; i >= 0; i--) {
      const p = this.powerUpDrops[i];
      p.scale += (1 - p.scale) * 0.1;
      p.bobPhase += 0.06;
      p.y += Math.sin(p.bobPhase) * 0.6 + (p.vy < 0 ? p.vy * 0.95 : 0);
      p.vy *= 0.95;

      // Check player collect (near bottom shooter)
      const dx = p.x - this.bowX, dy = p.y - this.bowY;
      if (Math.sqrt(dx * dx + dy * dy) < 60 || p.y > h + 50) {
        if (p.y < h + 20) {
          this.activatePowerUp(p.type);
          this.soundPowerUp();
          this.spawnPowerUpCollect(p.x, p.y);
        }
        this.powerUpDrops.splice(i, 1);
        continue;
      }
      p.life -= 0.001;
      if (p.life <= 0) this.powerUpDrops.splice(i, 1);
    }

    // Arrows
    for (let i = this.arrows.length - 1; i >= 0; i--) {
      const a = this.arrows[i];
      a.trail.push({ x: a.x, y: a.y });
      if (a.trail.length > 10) a.trail.shift();
      a.x += a.vx * timeScale;
      a.y += a.vy * timeScale;
      a.vy += 0.45 * timeScale;

      let hitSomething = false;
      for (const t of this.targets) {
        if (t.hit) continue;
        const dx = a.x - t.x, dy = a.y - t.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < t.radius * Math.max(0.5, t.scale)) {
          hitSomething = true;
          t.hp--;
          if (t.hp <= 0) {
            t.hit = true;
            this.combo++;
            this.comboTimer = 0;
            this.cb.onCombo(this.combo);
            this.soundCombo(this.combo);

            const bullseye = dist < t.radius * 0.3;
            const combo = Math.min(this.combo, 10);
            let pts = bullseye ? 50 : (t.isSmall ? 30 : (t.isBig ? 5 : (t.isGold ? 25 : 15)));
            pts *= combo;
            if (t.isGold) pts *= 2;
            if (this.activePowerUp === 'slowmo') pts = Math.floor(pts * 1.25);
            this.score += pts;
            this.cb.onScore(this.score);

            const newLevel = Math.floor(this.score / 300) + 1;
            if (newLevel > this.level) {
              this.level = newLevel;
              this.targetSpeed = 1.3 + newLevel * 0.2;
              this.cb.onLevel(newLevel);
              this.soundLevelUp();
              this.addFloatingText(w / 2, h * 0.4, `LEVEL ${newLevel}!`, '#ffd700', 28);
            }

            if (bullseye) { this.soundBullseye(); } else { this.soundHit(t.isGold); }
            this.spawnHitParticles(t.x, t.y, t.isGold || bullseye);
            this.addShockwave(t.x, t.y, t.isGold ? '#ffd700' : (bullseye ? '#fff' : '#ff4422'));
            this.shake = bullseye ? 9 : (t.isGold ? 6 : 3);

            const label = bullseye ? `BULLSEYE! +${pts}` : (this.combo > 2 ? `x${Math.min(this.combo, 10)} +${pts}` : `+${pts}`);
            this.addFloatingText(t.x, t.y - t.radius, label, bullseye ? '#ffd700' : (t.isGold ? '#ff8800' : '#fff'));

            // Power-up drop chance
            if (Math.random() < 0.12) this.spawnPowerUp(t.x, t.y);
          } else {
            // Damage flash
            this.addFloatingText(t.x, t.y, `💥`, '#ff4422', 18);
          }
          break;
        }
      }

      if (hitSomething || a.x < -60 || a.x > w + 60 || a.y < -120 || a.y > h + 120) {
        this.arrows.splice(i, 1);
      }
    }

    // Shockwaves
    for (let i = this.shockwaves.length - 1; i >= 0; i--) {
      const s = this.shockwaves[i];
      s.life -= 0.04;
      s.radius += (s.maxRadius - s.radius) * 0.12;
      if (s.life <= 0) this.shockwaves.splice(i, 1);
    }

    // Particles
    for (let i = this.particles.length - 1; i >= 0; i--) {
      const p = this.particles[i];
      p.life -= p.decay;
      if (p.life <= 0) { this.particles.splice(i, 1); continue; }
      if (p.type !== 'ring') { p.x += p.vx; p.y += p.vy; p.vy += 0.18; p.vx *= 0.97; }
    }

    // Floating texts
    for (let i = this.floatingTexts.length - 1; i >= 0; i--) {
      const f = this.floatingTexts[i];
      f.life -= f.decay;
      f.y -= 1.8;
      if (f.life <= 0) this.floatingTexts.splice(i, 1);
    }
  }

  activatePowerUp(type) {
    this.activePowerUp = type;
    this.powerUpDuration = type === 'shield' ? 8000 : 5000;
    this.powerUpMax = this.powerUpDuration;
    this.cb.onPowerUp(type, 1);
    const labels = { multishot: '⚡ MULTI-SHOT!', slowmo: '🌀 SLOW TIME!', shield: '🛡️ SHIELD!' };
    this.addFloatingText(this.canvas.width / 2, this.canvas.height * 0.35, labels[type], '#00ffcc', 22);
  }

  spawnPowerUpCollect(x, y) {
    for (let i = 0; i < 20; i++) {
      const angle = (i / 20) * Math.PI * 2;
      const speed = Math.random() * 4 + 2;
      this.particles.push({ x, y, vx: Math.cos(angle) * speed, vy: Math.sin(angle) * speed, life: 1, decay: 0.03, size: 4, color: '#00ffcc', type: 'burst' });
    }
  }

  spawnHitParticles(x, y, gold) {
    for (let i = 0; i < (gold ? 22 : 14); i++) {
      const angle = (i / (gold ? 22 : 14)) * Math.PI * 2;
      const speed = Math.random() * 5 + 2;
      this.particles.push({ x, y, vx: Math.cos(angle) * speed, vy: Math.sin(angle) * speed, life: 1, decay: 0.035, size: Math.random() * 5 + 2, color: gold ? `hsl(${40 + Math.random() * 20},100%,${55 + Math.random() * 25}%)` : `hsl(${Math.random() * 360},80%,65%)`, type: 'burst' });
    }
  }

  addShockwave(x, y, color) {
    this.shockwaves.push({ x, y, radius: 10, maxRadius: 90, life: 1, color });
  }

  addFloatingText(x, y, text, color, size = 17) {
    this.floatingTexts.push({ x, y, text, color, size, life: 1, decay: 0.022 });
  }

  // ── Draw ───────────────────────────────────────────────────────────────────

  draw() {
    const ctx = this.ctx;
    const w = this.canvas.width, h = this.canvas.height;

    ctx.save();
    ctx.translate((Math.random() - 0.5) * this.shake, (Math.random() - 0.5) * this.shake);

    // BG
    const bg = ctx.createLinearGradient(0, 0, 0, h);
    bg.addColorStop(0, '#030308');
    bg.addColorStop(0.6, '#0d0820');
    bg.addColorStop(1, '#050e18');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, w, h);

    // Slowmo overlay
    if (this.activePowerUp === 'slowmo') {
      ctx.fillStyle = 'rgba(160,40,255,0.06)';
      ctx.fillRect(0, 0, w, h);
    }
    // Shield overlay
    if (this.activePowerUp === 'shield') {
      ctx.fillStyle = 'rgba(40,255,120,0.04)';
      ctx.fillRect(0, 0, w, h);
    }

    this.drawStars(w, h);
    this.drawGrid(w, h);

    // Particles behind targets
    this.drawParticles();

    // Shockwaves
    for (const s of this.shockwaves) {
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.radius, 0, Math.PI * 2);
      ctx.strokeStyle = s.color;
      ctx.lineWidth = 3 * s.life;
      ctx.globalAlpha = s.life * 0.7;
      ctx.stroke();
      ctx.globalAlpha = 1;
    }

    // Power-up drops
    for (const p of this.powerUpDrops) {
      this.drawPowerUpDrop(p);
    }

    // Targets
    for (const t of this.targets) this.drawTarget(t);

    // Arrows
    for (const a of this.arrows) this.drawArrow(a);

    // Aim line
    if (this.aiming) this.drawAimLine(w, h);

    // Shooter/Bow
    this.drawBow(h);

    // Floating texts
    ctx.textAlign = 'center';
    for (const f of this.floatingTexts) {
      ctx.globalAlpha = f.life;
      ctx.font = `bold ${f.size || 17}px Arial`;
      ctx.fillStyle = f.color;
      ctx.shadowColor = f.color;
      ctx.shadowBlur = 12;
      ctx.fillText(f.text, f.x, f.y);
      ctx.shadowBlur = 0;
    }
    ctx.globalAlpha = 1;

    ctx.restore();
  }

  drawStars(w, h) {
    const ctx = this.ctx;
    const cx = w / 2, cy = h / 2, fov = 300;
    for (const s of this.stars) {
      s.z -= this.activePowerUp === 'slowmo' ? 0.5 : 1.8;
      if (s.z <= 0) s.z = 1000;
      const sx = cx + (s.x / s.z) * fov;
      const sy = cy + (s.y / s.z) * fov;
      if (sx < 0 || sx > w || sy < 0 || sy > h) continue;
      const brightness = 1 - s.z / 1000;
      const size = brightness * s.size * 3;
      ctx.beginPath();
      ctx.arc(sx, sy, Math.max(0.2, size), 0, Math.PI * 2);
      ctx.fillStyle = `rgba(255,255,255,${brightness * 0.85})`;
      ctx.fill();
    }
  }

  drawGrid(w, h) {
    const ctx = this.ctx;
    const horizon = h * 0.56;
    const color = this.activePowerUp === 'slowmo' ? 'rgba(120,40,255,0.18)' : 'rgba(60,30,160,0.15)';
    ctx.strokeStyle = color;
    ctx.lineWidth = 0.5;
    for (let i = 0; i <= 10; i++) {
      const x = (i / 10) * w;
      ctx.beginPath(); ctx.moveTo(x, horizon); ctx.lineTo(w / 2 + (x - w / 2) * 3.5, h + 60); ctx.stroke();
    }
    for (let i = 0; i < 7; i++) {
      const t = Math.pow(i / 7, 1.5);
      const y = horizon + (h - horizon + 60) * t;
      ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(w, y); ctx.stroke();
    }
    // Horizon glow
    const hg = ctx.createRadialGradient(w / 2, h, 0, w / 2, h, w * 0.65);
    hg.addColorStop(0, 'rgba(80,30,200,0.12)');
    hg.addColorStop(1, 'transparent');
    ctx.fillStyle = hg;
    ctx.fillRect(0, h - 160, w, 160);
  }

  drawAimLine(w, h) {
    const ctx = this.ctx;
    const bx = this.bowX, by = this.bowY;
    const dx = this.aimX - bx, dy = this.aimY - by;
    const dist = Math.sqrt(dx * dx + dy * dy) || 1;
    const vx = (dx / dist) * 22, vy = (dy / dist) * 22;

    let x = bx, y = by, px = vx, py = vy;
    ctx.beginPath();
    for (let i = 0; i < 18; i++) {
      py += 0.45;
      x += px; y += py;
      if (i === 0) ctx.moveTo(x, y);
      else ctx.lineTo(x, y);
      if (x < 0 || x > w || y > h + 50) break;
    }
    ctx.strokeStyle = 'rgba(255,220,80,0.35)';
    ctx.lineWidth = 2;
    ctx.setLineDash([6, 6]);
    ctx.stroke();
    ctx.setLineDash([]);
    // Dot at aim
    ctx.beginPath();
    ctx.arc(this.aimX, this.aimY, 6, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(255,220,80,0.4)';
    ctx.fill();
  }

  drawBow(h) {
    const ctx = this.ctx;
    const cx = this.bowX, cy = h - 65;

    // Glow platform
    const glow = ctx.createRadialGradient(cx, cy, 5, cx, cy, 45);
    glow.addColorStop(0, this.activePowerUp === 'multishot' ? 'rgba(0,200,255,0.3)' : this.activePowerUp === 'shield' ? 'rgba(40,255,120,0.3)' : 'rgba(255,180,0,0.25)');
    glow.addColorStop(1, 'transparent');
    ctx.fillStyle = glow;
    ctx.beginPath(); ctx.arc(cx, cy, 45, 0, Math.PI * 2); ctx.fill();

    // Platform ellipse
    ctx.fillStyle = this.activePowerUp === 'shield' ? 'rgba(40,255,120,0.12)' : 'rgba(255,200,0,0.12)';
    ctx.beginPath(); ctx.ellipse(cx, cy + 16, 38, 10, 0, 0, Math.PI * 2); ctx.fill();

    // Aim angle
    const dx = this.aimX - cx, dy = this.aimY - cy;
    const angle = Math.atan2(dy, dx);

    ctx.save();
    ctx.translate(cx, cy);
    ctx.rotate(angle - Math.PI / 2);

    // Bow arc
    ctx.strokeStyle = '#c8860a';
    ctx.lineWidth = 5;
    ctx.lineCap = 'round';
    ctx.beginPath();
    ctx.arc(0, 0, 22, -Math.PI * 0.7, Math.PI * 0.7);
    ctx.stroke();

    // String
    ctx.strokeStyle = '#e8c060';
    ctx.lineWidth = 1.5;
    ctx.beginPath();
    ctx.moveTo(-18 * Math.sin(0.7 * Math.PI), -18 * Math.cos(0.7 * Math.PI));
    ctx.lineTo(0, 5);
    ctx.lineTo(18 * Math.sin(0.7 * Math.PI), -18 * Math.cos(0.7 * Math.PI));
    ctx.stroke();

    // Arrow on bow
    ctx.strokeStyle = '#ffd700';
    ctx.lineWidth = 2.5;
    ctx.beginPath(); ctx.moveTo(0, 5); ctx.lineTo(0, -16); ctx.stroke();
    ctx.fillStyle = '#e0e0e0';
    ctx.beginPath(); ctx.moveTo(0, -20); ctx.lineTo(-4, -10); ctx.lineTo(4, -10); ctx.fill();
    ctx.fillStyle = '#ff6622';
    ctx.beginPath(); ctx.moveTo(0, 5); ctx.lineTo(-4, 10); ctx.lineTo(0, 8); ctx.lineTo(4, 10); ctx.fill();

    ctx.restore();
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

    // Outer glow
    const glowCol = t.isGold ? 'rgba(255,160,0,0.28)' : (t.isSmall ? 'rgba(0,180,255,0.22)' : (t.isBig ? 'rgba(255,0,100,0.2)' : 'rgba(255,40,40,0.18)'));
    const gg = ctx.createRadialGradient(0, 0, r * 0.5, 0, 0, r * 2);
    gg.addColorStop(0, glowCol); gg.addColorStop(1, 'transparent');
    ctx.fillStyle = gg; ctx.beginPath(); ctx.arc(0, 0, r * 2, 0, Math.PI * 2); ctx.fill();

    // Rings
    const rings = t.isGold
      ? [{ r, c: '#cc2200' }, { r: r * 0.78, c: '#ff6600' }, { r: r * 0.56, c: '#ffaa00' }, { r: r * 0.34, c: '#ffcc00' }, { r: r * 0.16, c: '#ffd700' }]
      : [{ r, c: '#aa0000' }, { r: r * 0.78, c: '#fff' }, { r: r * 0.56, c: '#cc0000' }, { r: r * 0.34, c: '#fff' }, { r: r * 0.16, c: '#ffcc00' }];

    for (const ring of rings) {
      ctx.beginPath(); ctx.arc(0, 0, ring.r, 0, Math.PI * 2);
      ctx.fillStyle = ring.c; ctx.fill();
      ctx.strokeStyle = 'rgba(0,0,0,0.25)'; ctx.lineWidth = 1; ctx.stroke();
    }

    // HP bars for big targets
    if (t.isBig && t.maxHp > 1) {
      for (let i = 0; i < t.maxHp; i++) {
        const bw = 14, bh = 5, gap = 4, totalW = t.maxHp * (bw + gap) - gap;
        const bx = -totalW / 2 + i * (bw + gap);
        ctx.fillStyle = i < t.hp ? '#ff4422' : 'rgba(255,255,255,0.2)';
        ctx.beginPath(); ctx.roundRect(bx, -r - 12, bw, bh, 2); ctx.fill();
      }
    }

    // 3D shine
    const shine = ctx.createRadialGradient(-r * 0.28, -r * 0.28, 0, 0, 0, r);
    shine.addColorStop(0, 'rgba(255,255,255,0.38)'); shine.addColorStop(0.5, 'rgba(255,255,255,0.04)'); shine.addColorStop(1, 'rgba(0,0,0,0.2)');
    ctx.fillStyle = shine; ctx.beginPath(); ctx.arc(0, 0, r, 0, Math.PI * 2); ctx.fill();

    // Special borders
    if (t.isGold) {
      ctx.strokeStyle = '#ffd700'; ctx.lineWidth = 2.5;
      ctx.beginPath(); ctx.arc(0, 0, r + 5 + Math.sin(t.pulsePhase * 2) * 2, 0, Math.PI * 2); ctx.stroke();
    }
    if (t.isSmall) {
      ctx.strokeStyle = '#00ccff'; ctx.lineWidth = 1.5; ctx.setLineDash([5, 3]);
      ctx.beginPath(); ctx.arc(0, 0, r + 6, 0, Math.PI * 2); ctx.stroke(); ctx.setLineDash([]);
    }
    if (t.isZigzag) {
      ctx.strokeStyle = '#ff44ff'; ctx.lineWidth = 1.5; ctx.setLineDash([3, 4]);
      ctx.beginPath(); ctx.arc(0, 0, r + 8, 0, Math.PI * 2); ctx.stroke(); ctx.setLineDash([]);
    }

    ctx.restore();
  }

  drawArrow(a) {
    const ctx = this.ctx;
    // Trail
    for (let i = 0; i < a.trail.length - 1; i++) {
      const t = i / a.trail.length;
      ctx.beginPath(); ctx.moveTo(a.trail[i].x, a.trail[i].y); ctx.lineTo(a.trail[i + 1].x, a.trail[i + 1].y);
      ctx.strokeStyle = `rgba(255,200,50,${t * 0.45})`; ctx.lineWidth = t * 3; ctx.lineCap = 'round'; ctx.stroke();
    }
    const angle = Math.atan2(a.vy, a.vx);
    ctx.save(); ctx.translate(a.x, a.y); ctx.rotate(angle);
    ctx.strokeStyle = '#c8a060'; ctx.lineWidth = 3; ctx.lineCap = 'round';
    ctx.beginPath(); ctx.moveTo(-18, 0); ctx.lineTo(8, 0); ctx.stroke();
    ctx.fillStyle = '#d8d8d8'; ctx.beginPath(); ctx.moveTo(14, 0); ctx.lineTo(6, -4); ctx.lineTo(6, 4); ctx.fill();
    ctx.fillStyle = '#ff6622';
    ctx.beginPath(); ctx.moveTo(-14, 0); ctx.lineTo(-19, -5); ctx.lineTo(-13, -1); ctx.fill();
    ctx.beginPath(); ctx.moveTo(-14, 0); ctx.lineTo(-19, 5); ctx.lineTo(-13, 1); ctx.fill();
    ctx.restore();
  }

  drawPowerUpDrop(p) {
    const ctx = this.ctx;
    const colors = { multishot: '#00cfff', slowmo: '#aa44ff', shield: '#44ff88' };
    const icons = { multishot: '⚡', slowmo: '🌀', shield: '🛡️' };
    const c = colors[p.type];

    ctx.save();
    ctx.translate(p.x, p.y);
    ctx.scale(p.scale, p.scale);

    // Glow
    const glow = ctx.createRadialGradient(0, 0, 8, 0, 0, 30);
    glow.addColorStop(0, c + '55'); glow.addColorStop(1, 'transparent');
    ctx.fillStyle = glow; ctx.beginPath(); ctx.arc(0, 0, 30, 0, Math.PI * 2); ctx.fill();

    // Circle bg
    ctx.beginPath(); ctx.arc(0, 0, 20, 0, Math.PI * 2);
    ctx.fillStyle = c + '22'; ctx.fill();
    ctx.strokeStyle = c; ctx.lineWidth = 2; ctx.stroke();

    // Icon
    ctx.font = '18px Arial'; ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.fillText(icons[p.type], 0, 1);

    ctx.restore();
  }

  drawParticles() {
    const ctx = this.ctx;
    for (const p of this.particles) {
      ctx.globalAlpha = p.life;
      ctx.beginPath();
      ctx.arc(p.x, p.y, Math.max(0.3, p.size * p.life), 0, Math.PI * 2);
      ctx.fillStyle = p.color;
      ctx.shadowColor = p.color; ctx.shadowBlur = 6;
      ctx.fill(); ctx.shadowBlur = 0;
    }
    ctx.globalAlpha = 1;
  }

  // ── Loop ───────────────────────────────────────────────────────────────────

  start() { this.running = true; this.lastTime = performance.now(); this.raf = requestAnimationFrame(t => this.loop(t)); }
  destroy() { this.running = false; if (this.raf) cancelAnimationFrame(this.raf); ['touchstart', 'touchmove', 'touchend'].forEach(ev => this.canvas.removeEventListener(ev, this[`_on${ev.charAt(0).toUpperCase() + ev.slice(1)}`])); this.canvas.removeEventListener('mousemove', this._onMouseMove); this.canvas.removeEventListener('mousedown', this._onMouseDown); this.canvas.removeEventListener('mouseup', this._onMouseUp); }
  loop(ts) { if (!this.running) return; const dt = Math.min(ts - this.lastTime, 33); this.lastTime = ts; this.update(dt); this.draw(); this.raf = requestAnimationFrame(t => this.loop(t)); }
}

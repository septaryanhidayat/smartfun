import './../css/app.css';
import Alpine from 'alpinejs';
import confetti from 'canvas-confetti';
import QRCode from 'qrcode';

window.Alpine = Alpine;
window.confetti = confetti;
window.QRCode = QRCode;

/**
 * SmartFun Synthesizer Sound Engine using Web Audio API
 * Zero external audio file downloads, zero lag, works offline & across all modern browsers.
 * Features:
 * - Dynamic Procedural Upbeat Background Music (BGM) in 5 Themes:
 *   1. Arcade Party (Chiptune Funky EDM)
 *   2. Space Galaxy (Cosmic Synthwave Pulse)
 *   3. Fantasy Quest (Celtic Adventure Beat - Wayground)
 *   4. Cyberpunk (Driving High-Octane Techno)
 *   5. Lava Battle (High-Tension Rock-Synth Showdown - Kahoot)
 * - Complete Tactile SFX (Correct, Wrong, Fanfare, Pop, Tick, PowerUp)
 */
class SoundEngine {
    constructor() {
        this.ctx = null;
        this.enabled = true;
        this.bgmEnabled = true;
        this.bgmVolume = 0.32;
        this.currentTheme = 'arcade';
        this.isPlayingBgm = false;

        // BGM Scheduler state
        this.schedulerTimer = null;
        this.stepIndex = 0;
        this.nextStepTime = 0;
        this.bpm = 126;

        // Sound bank melodies for 5 themes (16-step patterns)
        this.themes = {
            arcade: {
                bpm: 128,
                bassNotes: [130.81, 0, 130.81, 164.81, 196.00, 0, 164.81, 196.00, 220.00, 0, 220.00, 196.00, 164.81, 196.00, 146.83, 0], // C3, E3, G3, A3
                leadNotes: [523.25, 659.25, 783.99, 1046.50, 783.99, 659.25, 523.25, 587.33, 659.25, 783.99, 880.00, 1046.50, 1174.66, 1046.50, 880.00, 783.99],
                kickSteps: [0, 4, 8, 12],
                snareSteps: [4, 12],
                hatSteps: [2, 6, 10, 14],
                leadWave: 'square',
                bassWave: 'triangle'
            },
            space: {
                bpm: 118,
                bassNotes: [110.00, 110.00, 130.81, 146.83, 164.81, 164.81, 146.83, 130.81, 110.00, 110.00, 130.81, 146.83, 164.81, 196.00, 164.81, 146.83], // A2 minor synthwave
                leadNotes: [440.00, 0, 523.25, 0, 659.25, 783.99, 659.25, 0, 523.25, 0, 587.33, 0, 659.25, 880.00, 783.99, 0],
                kickSteps: [0, 4, 8, 12],
                snareSteps: [4, 12],
                hatSteps: [1, 3, 5, 7, 9, 11, 13, 15],
                leadWave: 'sawtooth',
                bassWave: 'sawtooth'
            },
            fantasy: {
                bpm: 122,
                bassNotes: [146.83, 0, 146.83, 0, 174.61, 0, 196.00, 0, 220.00, 0, 196.00, 0, 174.61, 0, 164.81, 0], // D minor dorian celtic
                leadNotes: [587.33, 659.25, 698.46, 880.00, 783.99, 698.46, 659.25, 587.33, 880.00, 0, 1046.50, 0, 880.00, 783.99, 698.46, 659.25],
                kickSteps: [0, 6, 8, 14],
                snareSteps: [4, 12],
                hatSteps: [2, 4, 8, 10, 12, 14],
                leadWave: 'sine',
                bassWave: 'triangle'
            },
            cyberpunk: {
                bpm: 132,
                bassNotes: [65.41, 65.41, 130.81, 65.41, 65.41, 130.81, 65.41, 98.00, 73.42, 73.42, 146.83, 73.42, 82.41, 82.41, 164.81, 110.00], // Deep C2 driving pulse
                leadNotes: [523.25, 0, 622.25, 659.25, 0, 783.99, 0, 987.77, 1046.50, 0, 783.99, 0, 659.25, 622.25, 523.25, 0],
                kickSteps: [0, 4, 8, 12],
                snareSteps: [4, 12],
                hatSteps: [0, 2, 4, 6, 8, 10, 12, 14],
                leadWave: 'sawtooth',
                bassWave: 'sawtooth'
            },
            lava: {
                bpm: 138,
                bassNotes: [82.41, 82.41, 0, 82.41, 98.00, 98.00, 0, 98.00, 110.00, 110.00, 0, 110.00, 123.47, 0, 110.00, 98.00], // E power rock tension
                leadNotes: [659.25, 659.25, 783.99, 880.00, 987.77, 0, 880.00, 783.99, 659.25, 587.33, 659.25, 783.99, 987.77, 1174.66, 987.77, 880.00],
                kickSteps: [0, 2, 4, 8, 10, 12],
                snareSteps: [4, 12],
                hatSteps: [1, 3, 5, 7, 9, 11, 13, 15],
                leadWave: 'sawtooth',
                bassWave: 'triangle'
            },
            lobby: {
                bpm: 114,
                // Bouncy funny cartoon / elevator walking bass
                bassNotes: [130.81, 0, 164.81, 0, 196.00, 0, 220.00, 196.00, 174.61, 0, 164.81, 0, 146.83, 0, 123.47, 0],
                // Playful, cute, funny bouncing cartoon melody
                leadNotes: [523.25, 0, 659.25, 0, 783.99, 880.00, 783.99, 0, 698.46, 0, 659.25, 587.33, 523.25, 0, 0, 783.99],
                kickSteps: [0, 8],
                snareSteps: [4, 12],
                hatSteps: [2, 6, 10, 14],
                leadWave: 'sine',
                bassWave: 'triangle'
            }
        };

        // Resume audio on first user gesture
        const unlock = () => {
            this.init();
            window.removeEventListener('pointerdown', unlock);
            window.removeEventListener('keydown', unlock);
        };
        window.addEventListener('pointerdown', unlock);
        window.addEventListener('keydown', unlock);
    }

    init() {
        if (!this.ctx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                this.ctx = new AudioContext();
            }
        }
        if (this.ctx && this.ctx.state === 'suspended') {
            this.ctx.resume();
        }
    }

    // ==========================================
    // PROCEDURAL BGM ENGINE (LOOKAHEAD SCHEDULER)
    // ==========================================
    startBgm(theme = null) {
        this.init();
        if (theme && this.themes[theme]) {
            this.currentTheme = theme;
        }

        if (this.isPlayingBgm) return;
        this.isPlayingBgm = true;

        const config = this.themes[this.currentTheme] || this.themes.arcade;
        this.bpm = config.bpm;
        this.stepIndex = 0;
        this.nextStepTime = this.ctx ? this.ctx.currentTime + 0.05 : 0;

        this.scheduleLoop();
    }

    stopBgm() {
        this.isPlayingBgm = false;
        if (this.schedulerTimer) {
            clearTimeout(this.schedulerTimer);
            this.schedulerTimer = null;
        }
    }

    toggleBgm() {
        if (this.isPlayingBgm) {
            this.stopBgm();
            this.bgmEnabled = false;
        } else {
            this.bgmEnabled = true;
            this.startBgm(this.currentTheme);
        }
        return this.isPlayingBgm;
    }

    setTheme(theme) {
        if (!this.themes[theme]) return;
        this.currentTheme = theme;
        this.bpm = this.themes[theme].bpm;
    }

    setBgmVolume(val) {
        this.bgmVolume = Math.max(0, Math.min(1, val));
    }

    scheduleLoop() {
        if (!this.isPlayingBgm || !this.ctx) return;

        const lookaheadTime = 0.15; // 150ms window
        const secondsPerStep = (60 / this.bpm) / 4; // 16th note steps

        while (this.nextStepTime < this.ctx.currentTime + lookaheadTime) {
            this.playStepAtTime(this.stepIndex, this.nextStepTime);
            this.nextStepTime += secondsPerStep;
            this.stepIndex = (this.stepIndex + 1) % 16;
        }

        this.schedulerTimer = setTimeout(() => this.scheduleLoop(), 40);
    }

    playStepAtTime(step, time) {
        if (!this.bgmEnabled || !this.enabled || !this.ctx) return;
        const config = this.themes[this.currentTheme] || this.themes.arcade;

        // 1. Kick Drum
        if (config.kickSteps.includes(step)) {
            this.playKick(time);
        }

        // 2. Snare / Clap
        if (config.snareSteps.includes(step)) {
            this.playSnare(time);
        }

        // 3. Hi-Hat
        if (config.hatSteps.includes(step)) {
            this.playHiHat(time);
        }

        // 4. Bass synth
        const bassFreq = config.bassNotes[step];
        if (bassFreq > 0) {
            this.playBassNote(bassFreq, time, config.bassWave);
        }

        // 5. Lead synth arpeggio
        const leadFreq = config.leadNotes[step];
        if (leadFreq > 0) {
            this.playLeadNote(leadFreq, time, config.leadWave);
        }
    }

    playKick(time) {
        try {
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.frequency.setValueAtTime(140, time);
            osc.frequency.exponentialRampToValueAtTime(32, time + 0.12);

            const vol = 0.38 * this.bgmVolume;
            gain.gain.setValueAtTime(vol, time);
            gain.gain.exponentialRampToValueAtTime(0.001, time + 0.12);

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.start(time);
            osc.stop(time + 0.13);
        } catch (e) {}
    }

    playSnare(time) {
        try {
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(220, time);
            osc.frequency.exponentialRampToValueAtTime(80, time + 0.08);

            const vol = 0.22 * this.bgmVolume;
            gain.gain.setValueAtTime(vol, time);
            gain.gain.exponentialRampToValueAtTime(0.001, time + 0.09);

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.start(time);
            osc.stop(time + 0.1);
        } catch (e) {}
    }

    playHiHat(time) {
        try {
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.type = 'square';
            osc.frequency.setValueAtTime(1200, time);

            const vol = 0.06 * this.bgmVolume;
            gain.gain.setValueAtTime(vol, time);
            gain.gain.exponentialRampToValueAtTime(0.001, time + 0.04);

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.start(time);
            osc.stop(time + 0.045);
        } catch (e) {}
    }

    playBassNote(freq, time, waveType = 'sawtooth') {
        try {
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();
            const filter = this.ctx.createBiquadFilter();

            osc.type = waveType;
            osc.frequency.setValueAtTime(freq, time);

            filter.type = 'lowpass';
            filter.frequency.setValueAtTime(450, time);

            const vol = 0.25 * this.bgmVolume;
            gain.gain.setValueAtTime(vol, time);
            gain.gain.exponentialRampToValueAtTime(0.01, time + 0.16);

            osc.connect(filter);
            filter.connect(gain);
            gain.connect(this.ctx.destination);

            osc.start(time);
            osc.stop(time + 0.17);
        } catch (e) {}
    }

    playLeadNote(freq, time, waveType = 'triangle') {
        try {
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.type = waveType;
            osc.frequency.setValueAtTime(freq, time);

            const vol = 0.14 * this.bgmVolume;
            gain.gain.setValueAtTime(vol, time);
            gain.gain.exponentialRampToValueAtTime(0.001, time + 0.14);

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.start(time);
            osc.stop(time + 0.15);
        } catch (e) {}
    }

    // ==========================================
    // SOUND EFFECTS (SFX)
    // ==========================================
    playTone(freq, type = 'sine', duration = 0.15, gainVal = 0.15) {
        if (!this.enabled) return;
        this.init();
        if (!this.ctx) return;

        try {
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.type = type;
            osc.frequency.setValueAtTime(freq, this.ctx.currentTime);

            gain.gain.setValueAtTime(gainVal, this.ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + duration);

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.start();
            osc.stop(this.ctx.currentTime + duration);
        } catch (e) {}
    }

    playTick() {
        this.playTone(850, 'triangle', 0.05, 0.12);
    }

    playPop() {
        this.init();
        if (!this.ctx || !this.enabled) return;
        try {
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(280, this.ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(850, this.ctx.currentTime + 0.1);
            gain.gain.setValueAtTime(0.18, this.ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, this.ctx.currentTime + 0.1);
            osc.connect(gain);
            gain.connect(this.ctx.destination);
            osc.start();
            osc.stop(this.ctx.currentTime + 0.1);
        } catch (e) {}
    }

    playCorrect() {
        this.init();
        if (!this.ctx || !this.enabled) return;
        const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6
        notes.forEach((freq, idx) => {
            setTimeout(() => {
                this.playTone(freq, 'triangle', 0.25, 0.24);
            }, idx * 75);
        });
    }

    playWrong() {
        this.init();
        if (!this.ctx || !this.enabled) return;
        try {
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(160, this.ctx.currentTime);
            osc.frequency.linearRampToValueAtTime(90, this.ctx.currentTime + 0.35);
            gain.gain.setValueAtTime(0.24, this.ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, this.ctx.currentTime + 0.35);
            osc.connect(gain);
            gain.connect(this.ctx.destination);
            osc.start();
            osc.stop(this.ctx.currentTime + 0.35);
        } catch (e) {}
    }

    playPowerup() {
        this.init();
        if (!this.ctx || !this.enabled) return;
        const notes = [440, 554.37, 659.25, 880, 1108.73];
        notes.forEach((freq, idx) => {
            setTimeout(() => {
                this.playTone(freq, 'sine', 0.2, 0.22);
            }, idx * 55);
        });
    }

    playFanfare() {
        this.init();
        if (!this.ctx || !this.enabled) return;
        const chords = [
            { f: 523.25, t: 0 },
            { f: 659.25, t: 140 },
            { f: 783.99, t: 280 },
            { f: 1046.50, t: 450 },
            { f: 1046.50, t: 650 },
            { f: 1046.50, t: 850 },
            { f: 1318.51, t: 1050 }
        ];
        chords.forEach(c => {
            setTimeout(() => {
                this.playTone(c.f, 'triangle', 0.45, 0.25);
            }, c.t);
        });
    }
}

window.SmartFunAudio = new SoundEngine();

// Start Alpine
Alpine.start();

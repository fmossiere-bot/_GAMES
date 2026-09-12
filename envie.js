/* ============================================================
   Envie — the Environmentle mascot as a web component
   ------------------------------------------------------------
   <envie-mascot pose="wave" hat="hat" tee="eco" head="auto"
                 says="Hello" bubble-side="right" bob></envie-mascot>

   pose        wave | stand | celebrate | think | point | peek | spyglass | flag
   hat         hat (beanie) | cap | none
   tee         eco | science
   head        auto (follows currentColor) | dark (navy) | light (white)
   says        any text → speech bubble (HTML, wraps). Empty = no bubble
   bubble-side right | left
   bob         present → gentle idle bob (disabled under reduced motion)
   flip        present → mirrored horizontally (bubble is not mirrored)

   Size with CSS on the host:  envie-mascot { width: 140px }
   Ink colour follows `color` on the host / parent: navy on light surfaces,
   white on navy. Limbs, outlines and (by default) the head all use it.
   ============================================================ */
(function () {
  if (window.customElements && customElements.get('envie-mascot')) return;

  const NAVY = '#0a293b';

  // The Environmentle logo mark (U-arrow planet), inlined so it recolours.
  // Viewbox 200×200. `ink` is the stroke/fill colour.
  const mark = (ink) => `
    <svg x="26" y="3.9" width="108" height="108" viewBox="0 0 200 200" overflow="visible">
      <defs>
        <mask id="envie-mk">
          <rect x="-60" y="-60" width="320" height="320" fill="#fff"></rect>
          <path d="M76 96 C60 96 50 106 50 118 C50 130 60 140 76 140 C87 140 93 134 100 134 C107 134 113 140 124 140 C140 140 150 130 150 118 C150 106 140 96 124 96 C113 96 107 102 100 102 C93 102 87 96 76 96 Z" fill="#000" stroke="#000" stroke-width="15" stroke-linejoin="round"></path>
          <ellipse cx="100" cy="118" rx="56" ry="27" fill="#000"></ellipse>
        </mask>
      </defs>
      <circle cx="100" cy="100" r="68" fill="none" stroke="${ink}" stroke-width="8" mask="url(#envie-mk)"></circle>
      <ellipse cx="100" cy="100" rx="94" ry="27" fill="none" stroke="${ink}" stroke-width="8" transform="rotate(-18 100 100)" mask="url(#envie-mk)"></ellipse>
      <path d="M76 96 C60 96 50 106 50 118 C50 130 60 140 76 140 C87 140 93 134 100 134 C107 134 113 140 124 140 C140 140 150 130 150 118 C150 106 140 96 124 96 C113 96 107 102 100 102 C93 102 87 96 76 96 Z" fill="none" stroke="${ink}" stroke-width="8" stroke-linejoin="round"></path>
      <path d="M76 105 L88 119 L81.5 119 L81.5 130 L70.5 130 L70.5 119 L64 119 Z" fill="${ink}"></path>
      <circle cx="124" cy="112" r="5" fill="${ink}"></circle>
      <circle cx="124" cy="126" r="5" fill="${ink}"></circle>
    </svg>`;

  const shoe = (upper, stripe, sole) => `
      <path d="M2 15 C2 10 6 6 12 4 C17 2 22 1 27 1 h4 c5 0 7 3 7 7 v7 z" fill="${upper}" stroke="currentColor" stroke-width="3" stroke-linejoin="round"></path>
      <path d="M2 15 C2 10 6 6 12 4 l4 11 z" fill="#f5f1e6" stroke="currentColor" stroke-width="2.6" stroke-linejoin="round"></path>
      <path d="M12 13 C17 8 23 6 31 7" fill="none" stroke="${stripe}" stroke-width="3.4" stroke-linecap="round"></path>
      <path d="M18 6 l6 -3 M22 9 l6 -3" fill="none" stroke="#f5f1e6" stroke-width="2.2" stroke-linecap="round"></path>
      <path d="M31 1 c5 0 7 3 7 7 v3 h-9 z" fill="#f5f1e6" stroke="currentColor" stroke-width="2.6" stroke-linejoin="round"></path>
      <path d="M0 15 h40 v4 H0 z" fill="#f5f1e6" stroke="currentColor" stroke-width="3" stroke-linejoin="round"></path>
      <path d="M0 19 h40 v2 c0 2 -2 4 -4 4 H4 c-2 0 -4 -2 -4 -4 z" fill="${sole}" stroke="currentColor" stroke-width="3" stroke-linejoin="round"></path>`;

  const TEE_PATH = 'M62 98 c-8 2 -12 6 -14 12 l9 6 v38 c0 3 2 5 5 5 h36 c3 0 5 -2 5 -5 v-38 l9 -6 c-2 -6 -6 -10 -14 -12 c-5 5 -11 7 -18 7 c-7 0 -13 -2 -18 -7 z';

  const tees = {
    eco: `
      <path d="${TEE_PATH}" fill="#f5f1e6" stroke="currentColor" stroke-width="4.5" stroke-linejoin="round"></path>
      <g class="tee-print" font-size="7.4" text-anchor="middle">
        <text x="80" y="120" fill="#c9b95e">BE ECO</text>
        <text x="80" y="130" fill="#c0693c">FRIENDLY</text>
        <text x="80" y="140" fill="#7d9c93">NOT EGO</text>
        <text x="80" y="150" fill="#5f8ab0">CENTRIC</text>
      </g>`,
    science: `
      <path d="${TEE_PATH}" fill="#c0392b" stroke="currentColor" stroke-width="4.5" stroke-linejoin="round"></path>
      <g class="tee-print" font-size="7.6" text-anchor="middle" fill="#fff">
        <text x="80" y="120">MAKE</text>
        <text x="80" y="130">SCIENCE</text>
        <text x="80" y="140">GREAT</text>
        <text x="80" y="150">AGAIN</text>
      </g>`
  };

  const ARM = 'fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"';
  const hand = (x, y) => `<circle cx="${x}" cy="${y}" r="6" fill="currentColor"></circle>`;

  const poses = {
    wave: `
      <path d="M50 112 C38 118 30 128 26 140" ${ARM}></path>${hand(24, 144)}
      <g class="arm-wave">
        <path d="M110 112 C124 106 132 94 134 80" ${ARM}></path>${hand(135, 75)}
      </g>`,
    stand: `
      <path d="M50 112 C40 120 34 132 32 146" ${ARM}></path>${hand(31, 151)}
      <path d="M110 112 C120 120 126 132 128 146" ${ARM}></path>${hand(129, 151)}`,
    celebrate: `
      <path d="M50 112 C36 104 30 92 28 78" ${ARM}></path>${hand(26, 73)}
      <path d="M110 112 C124 104 130 92 132 78" ${ARM}></path>${hand(134, 73)}
      <path class="sparks" d="M16 60 l-6 -6 M22 48 l-3 -8 M144 60 l6 -6 M138 48 l3 -8" fill="none" stroke="#e67e22" stroke-width="4" stroke-linecap="round"></path>`,
    think: `
      <path d="M50 112 C40 118 34 128 32 140" ${ARM}></path>${hand(31, 145)}
      <path d="M110 112 C114 102 108 94 100 90" ${ARM}></path>${hand(96, 88)}`,
    point: `
      <path d="M50 112 C40 118 34 128 32 140" ${ARM}></path>${hand(31, 145)}
      <path d="M110 112 C120 111 128 109 134 107" ${ARM}></path>${hand(138, 106)}
      <path d="M146 105 h10" fill="none" stroke="#e67e22" stroke-width="4.5" stroke-linecap="round"></path>`,
    peek: `
      <path d="M50 112 C44 104 44 96 46 88" ${ARM}></path>${hand(46, 83)}
      <path d="M110 112 C116 104 116 96 114 88" ${ARM}></path>${hand(114, 83)}`,
    spyglass: `
      <path d="M50 112 C66 120 78 128 90 138" ${ARM}></path>
      <path d="M110 112 C120 118 128 126 134 136" ${ARM}></path>
      <g transform="rotate(36 118 128)">
        <rect x="100" y="119" width="48" height="18" rx="9" fill="#f5f1e6" stroke="currentColor" stroke-width="4.5"></rect>
        <path d="M148 114 l13 7 v10 l-13 7 z" fill="#4e8a4d" stroke="currentColor" stroke-width="4" stroke-linejoin="round"></path>
      </g>`,
    flag: `
      <path d="M50 112 C38 118 30 128 26 140" ${ARM}></path>${hand(24, 144)}
      <path d="M138 34 v134" ${ARM}></path>
      <path d="M138 38 c-14 6 -18 18 -32 22 v-26 c14 -4 18 -14 32 -18 z" fill="#e67e22" stroke="currentColor" stroke-width="4.5" stroke-linejoin="round"></path>
      <path d="M110 112 C122 108 131 102 137 96" ${ARM}></path>`
  };

  const hats = {
    hat: `
      <path d="M46 30 C46 12 60 2 80 2 C100 2 114 12 114 30 z" fill="#4e8a4d" stroke="currentColor" stroke-width="4.5" stroke-linejoin="round"></path>
      <path d="M60 8 C58 16 57 23 57 30 M80 3 v27 M100 8 C102 16 103 23 103 30" fill="none" stroke="#3a6b39" stroke-width="3"></path>
      <path d="M44 28 h72 v10 c0 3 -2 5 -5 5 H49 c-3 0 -5 -2 -5 -5 z" fill="#3a6b39" stroke="currentColor" stroke-width="4.5" stroke-linejoin="round"></path>
      <path d="M56 29 v13 M68 29 v13 M80 29 v13 M92 29 v13 M104 29 v13" fill="none" stroke="#2c5230" stroke-width="2.4"></path>
      <circle cx="80" cy="-2" r="9" fill="#e67e22" stroke="currentColor" stroke-width="4"></circle>`,
    cap: `
      <path d="M52 30 C54 14 66 6 80 6 C94 6 106 14 108 30 z" fill="#4e8a4d" stroke="currentColor" stroke-width="4.5" stroke-linejoin="round"></path>
      <path d="M108 30 C124 30 134 33 136 38 C130 41 118 41 104 40 z" fill="#e67e22" stroke="currentColor" stroke-width="4.5" stroke-linejoin="round"></path>
      <path d="M52 30 h56" fill="none" stroke="currentColor" stroke-width="4.5" stroke-linecap="round"></path>`,
    none: ''
  };

  const STYLE = `
    :host {
      display: inline-block;
      position: relative;
      width: 140px;
      aspect-ratio: 160 / 230;
      color: inherit;
      line-height: 0;
      vertical-align: bottom;
      --envie-bubble-bg: #fff;
      --envie-bubble-ink: ${NAVY};
      --envie-bubble-width: 104%;
    }
    :host([hidden]) { display: none; }
    .wrap { position: absolute; inset: 0; }
    :host([flip]) .wrap svg { transform: scaleX(-1); }
    :host([bob]) .wrap { animation: envie-bob 8s ease-in-out infinite; }
    svg { position: absolute; inset: 0; width: 100%; height: 100%; overflow: visible; display: block; }
    .tee-print { font-family: 'Comfortaa', system-ui, sans-serif; font-weight: 700; letter-spacing: -.2px; }
    .arm-wave { transform-origin: 110px 112px; animation: envie-wave 2.6s ease-in-out infinite; }
    .bubble {
      position: absolute; top: -4%; width: var(--envie-bubble-width); box-sizing: border-box;
      background: var(--envie-bubble-bg); color: var(--envie-bubble-ink);
      border: 3px solid var(--envie-bubble-ink); border-radius: 20px;
      padding: 12px 16px; box-shadow: 0 8px 24px rgba(10,41,59,.16);
      font-family: 'Comfortaa', system-ui, sans-serif; font-weight: 700;
      font-size: 14px; line-height: 1.35; text-wrap: pretty; pointer-events: none;
    }
    .bubble.right { left: 72%; }
    .bubble.left  { right: 72%; }
    .bubble .tail {
      position: absolute; bottom: 16px; width: 18px; height: 18px;
      background: var(--envie-bubble-bg); border-bottom: 3px solid var(--envie-bubble-ink);
    }
    .bubble.right .tail { left: -11px; border-left: 3px solid var(--envie-bubble-ink); transform: rotate(45deg); border-bottom-left-radius: 4px; }
    .bubble.left  .tail { right: -11px; border-right: 3px solid var(--envie-bubble-ink); transform: rotate(-45deg); border-bottom-right-radius: 4px; }
    @keyframes envie-wave { 0%,100% { transform: rotate(0deg) } 50% { transform: rotate(-18deg) } }
    @keyframes envie-bob  { 0%,100% { transform: translateY(0) rotate(-2deg) } 50% { transform: translateY(-10px) rotate(2deg) } }
    @media (prefers-reduced-motion: reduce) {
      .arm-wave, :host([bob]) .wrap { animation: none !important; }
    }`;

  class EnvieMascot extends HTMLElement {
    static get observedAttributes() { return ['pose', 'hat', 'tee', 'head', 'says', 'bubble-side']; }
    constructor() {
      super();
      this.attachShadow({ mode: 'open' });
      this._rendered = false;
    }
    connectedCallback() {
      if (!this.hasAttribute('aria-hidden') && !this.hasAttribute('role')) this.setAttribute('aria-hidden', 'true');
      this.render();
    }
    attributeChangedCallback() { if (this.isConnected) this.render(); }

    get pose() { return this.getAttribute('pose') || 'wave'; }
    set pose(v) { this.setAttribute('pose', v); }
    get says() { return this.getAttribute('says') || ''; }
    set says(v) { v ? this.setAttribute('says', v) : this.removeAttribute('says'); }

    render() {
      const pose = poses[this.pose] ? this.pose : 'wave';
      const hatKey = this.getAttribute('hat') || 'hat';
      const hat = hats[hatKey] !== undefined ? hats[hatKey] : hats.hat;
      const tee = tees[this.getAttribute('tee') || 'eco'] || tees.eco;
      const head = this.getAttribute('head') || 'auto';
      const ink = head === 'dark' ? NAVY : head === 'light' ? '#fff' : 'currentColor';
      const says = this.says.trim();
      const side = (this.getAttribute('bubble-side') || 'right') === 'left' ? 'left' : 'right';

      this.shadowRoot.innerHTML = `
        <style>${STYLE}</style>
        <div class="wrap">
          <svg viewBox="0 0 160 230" aria-hidden="true">
            <!-- legs -->
            <path d="M70 174 C69 182 69 188 69 192" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round"></path>
            <path d="M92 174 C93 182 93 188 93 192" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round"></path>
            <!-- sneakers -->
            <g transform="translate(40 191) scale(.86)">${shoe('#4e8a4d', '#e67e22', '#e67e22')}</g>
            <g transform="translate(84 191) scale(-.86 .86) translate(-40 0)">${shoe('#e67e22', '#4e8a4d', '#4e8a4d')}</g>
            <!-- shorts -->
            <path d="M56 150 h48 v16 c0 5 -3 8 -8 8 H90 L81 157 L72 174 H64 c-5 0 -8 -3 -8 -8 z" fill="#d9d4c6" stroke="currentColor" stroke-width="4.5" stroke-linejoin="round"></path>
            <!-- tee -->
            ${tee}
            <!-- arms -->
            ${poses[pose]}
            <!-- head: the logo mark -->
            ${mark(ink)}
            <!-- headwear -->
            ${hat}
          </svg>
        </div>
        ${says ? `<div class="bubble ${side}" role="presentation">${says}<span class="tail"></span></div>` : ''}`;
    }
  }

  customElements.define('envie-mascot', EnvieMascot);
})();

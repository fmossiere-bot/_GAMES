/* ============================================================
   Envie's first-run tour. Four short steps over the home page:
   the three challenges, the tab bar, the companion, points and
   credits. Shown once per player (flag synced to Firestore), and
   again on request from Profile > Help.
   ============================================================ */
(function () {
  'use strict';

  const nkOf = () => (playerName || '').toLowerCase().replace(/\s+/g, '_');
  const KEY = (nk) => 'onboarded_' + nk;
  const esc = (s) => String(s == null ? '' : s).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

  function isNewPlayer(nk) {
    if (localStorage.getItem(KEY(nk))) return false;
    const p = JSON.parse(localStorage.getItem('env_progress_' + nk) || '{}');
    return !(p.totalScore > 0 || p.sessionsPlayed > 0 || p.actionsPledged > 0);
  }

  function steps(name) {
    const ways = `
      <div class="tour-ways" aria-hidden="true">
        <span><i class="tile sm aqua">${EA.icon('gamepad-2', { size: 18 })}</i>Game</span>
        <span><i class="tile sm">${EA.icon('book-open', { size: 18 })}</i>Story</span>
        <span><i class="tile sm sky">${EA.icon('sprout', { size: 18 })}</i>Action</span>
      </div>`;
    const tabs = `
      <div class="tour-tabs" aria-hidden="true">
        <span>${EA.icon('home', { size: 18 })}Home<em>today's pick</em></span>
        <span>${EA.icon('gamepad-2', { size: 18 })}Games<em>the library</em></span>
        <span>${EA.icon('book-open', { size: 18 })}Stories<em>short reads</em></span>
        <span>${EA.icon('search', { size: 18 })}Companion<em>ask me</em></span>
      </div>`;
    const ask = `
      <div class="tour-ask" aria-hidden="true">
        <span class="tour-ask-field">${EA.icon('search', { size: 16 })}Is nuclear power low carbon?</span>
        <span class="tour-ask-send">${EA.icon('send', { size: 15 })}</span>
      </div>`;
    const credit = `
      <div class="tour-credit" aria-hidden="true">
        <p>0 / 10,000 pts to your first impact credit</p>
        <div class="bar"><div class="bar-fill" style="width:4%"></div></div>
      </div>`;
    return [
      { pose: 'wave', hat: 'hat', tee: 'eco',
        head: `Hi ${esc(name)}, I'm Envie.`,
        sub: 'Every day I hand you three ways to play your part: a game, a story or an action. Pick one and it keeps your streak going. One a day is the rhythm, two is the cap.',
        art: ways, lift: false },
      { pose: 'point', hat: 'cap', tee: 'eco',
        head: 'Four tabs, that is all.',
        sub: 'Home is where I hand you today\'s pick. Games and Stories hold the whole library if you would rather choose your own. Companion is me.',
        art: tabs, lift: true },
      { pose: 'stand', hat: 'cap', tee: 'science',
        head: 'Ask me anything about climate.',
        sub: 'I answer from our own checked wiki first, and I say so when I fall back to general AI. I can also take apart a claim you have heard and hand you what to say back.',
        art: ask, lift: false },
      { pose: 'celebrate', hat: 'hat', tee: 'eco',
        head: 'Points become real things.',
        sub: 'Games, stories and actions all earn points. Every 10,000 turns into an impact credit you can spend on a native tree or a patch of meadow with our partners. Now, pick one.',
        art: credit, lift: false },
    ];
  }

  let _i = 0;
  let _steps = [];

  function startTour(force) {
    const nk = nkOf();
    if (!force && !isNewPlayer(nk)) return false;
    _steps = steps(playerName || 'there');
    _i = 0;
    let el = document.getElementById('tour');
    if (!el) {
      el = document.createElement('div'); el.id = 'tour';
      el.innerHTML = '<div class="tour-scrim"></div><div class="tour-card" role="dialog" aria-modal="true" aria-label="How the app works"></div>';
      document.body.appendChild(el);
    }
    render();
    requestAnimationFrame(() => el.classList.add('on'));
    return true;
  }

  function render() {
    const s = _steps[_i];
    const card = document.querySelector('#tour .tour-card');
    const last = _i === _steps.length - 1;
    card.innerHTML = `
      <div class="erow sm">
        <envie-mascot pose="${s.pose}" hat="${s.hat}" tee="${s.tee}" aria-hidden="true"></envie-mascot>
        <div class="bb ${['r1', 'r2', 'r3', 'r4'][_i % 4]} tail-low"><p class="bh">${s.head}</p><p class="bs">${s.sub}</p>${EA.TAIL}</div>
      </div>
      ${s.art}
      <div class="tour-foot">
        <button type="button" class="tour-skip" id="tour-skip">${last ? '' : 'Skip'}</button>
        <div class="tour-dots">${_steps.map((_, k) => `<i class="${k === _i ? 'on' : ''}"></i>`).join('')}</div>
        <button type="button" class="cta amber tour-next" id="tour-next">${last ? "Let's go" : 'Next'} ${EA.icon('arrow-right', { size: 16 })}</button>
      </div>`;
    document.getElementById('tour-next').onclick = () => (last ? finish() : go(_i + 1));
    document.getElementById('tour-skip').onclick = finish;
    const nav = document.getElementById('app-bottom-nav');
    if (nav) nav.classList.toggle('tour-lift', !!s.lift);
    card.classList.remove('flip'); void card.offsetWidth; card.classList.add('flip');
  }

  function go(i) { _i = Math.max(0, Math.min(_steps.length - 1, i)); render(); }

  function finish() {
    const nk = nkOf();
    localStorage.setItem(KEY(nk), new Date().toISOString().slice(0, 10));
    const el = document.getElementById('tour');
    if (el) el.classList.remove('on');
    const nav = document.getElementById('app-bottom-nav');
    if (nav) nav.classList.remove('tour-lift');
    if (typeof syncPlayerToFirestore === 'function') syncPlayerToFirestore(nk);
  }

  window.startTour = startTour;
  window.tourOnboardedKey = KEY;
})();

/* ============================================================
   Environmentle home, actions, credits and profile screens.
   Rendering only: the decisions live in engine.js (window.Engine),
   icons and bubbles in app.js (window.EA). Uses the globals the
   index.html inline script defines: playerName, _loadedChallenges,
   ALL_STORIES, openGame, openCourse, switchTab, showScreen,
   setActiveTab, syncPlayerToFirestore, formatRelativeDate.
   ============================================================ */
(function () {
  'use strict';

  const nkOf = () => (playerName || '').toLowerCase().replace(/\s+/g, '_');
  const esc  = (s) => String(s == null ? '' : s).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  const fmt  = (n) => (n || 0).toLocaleString();

  const TYPE_ICON = { water: 'droplet', food: 'utensils', energy: 'zap', transport: 'car', circularity: 'recycle', nature: 'sprout', community: 'message-circle', mindset: 'lightbulb', credit: 'sprout' };
  const TYPE_TONE = { water: 'aqua', food: 'sky', energy: 'amber', transport: 'aqua', circularity: 'amber', nature: 'sky', community: 'rose', mindset: 'moss', credit: 'sky' };

  // ── HOME ────────────────────────────────────────────────
  async function renderHome() {
    const nk = nkOf();
    await Engine.loadActions();
    const st = Engine.state(nk);
    const today = st.today;
    const dailyChallenge = (_loadedChallenges || []).find((c) => c.date === today) || null;
    const ctx = { state: st, dailyChallenge, stories: ALL_STORIES };
    const d = Engine.decide(nk, ctx);
    const ranked = Engine.rankActions(nk, st, _loadedChallenges);
    const action = ranked[0] || null;

    // Title
    const title = document.getElementById('hub-title');
    if (title) title.innerHTML = st.doneCount >= 3
      ? 'All three <em>done</em>'
      : st.anyDone ? 'One more<em>?</em>' : 'Today, pick <em>one</em>';
    document.getElementById('screen-hub').classList.toggle('credit-mode', d.lead === 'credit');

    renderWays(st, d);
    renderLead(st, d, action, dailyChallenge);
    renderMore(st, d, action);
    renderProfileBadge(st);
  }

  // The three-way strip: Game · Story · Action
  function renderWays(st, d) {
    const el = document.getElementById('hub-ways');
    if (!el) return;
    const ways = [
      { key: 'game',   label: 'Game',   meta: '3 min', icon: 'gamepad-2', tone: 'aqua', go: () => (d.dailyGame && d.gameAvailable) ? openGame('quiz', 'daily') : switchTab('games') },
      { key: 'story',  label: 'Story',  meta: '6 min', icon: 'book-open', tone: 'white', go: () => d.unreadStory ? openCourse(d.unreadStory.id) : switchTab('learn') },
      { key: 'action', label: 'Action', meta: '2 min', icon: 'sprout',    tone: 'sky',  go: () => openActions() },
    ];
    const leadKey = d.lead === 'credit' ? 'action' : d.lead;
    el.innerHTML = ways.map((w) => {
      const done = st.done[w.key];
      const lead = !done && w.key === leadKey;
      const cls = ['way', w.tone, done ? 'done' : '', lead ? 'lead' : ''].filter(Boolean).join(' ');
      const badge = done ? '<span class="way-badge done">Done today</span>' : lead ? '<span class="way-badge">Suggested</span>' : '';
      const meta = done ? EA.icon('check', { size: 12 }) + ' Done' : w.meta;
      return `<button type="button" class="${cls}" data-way="${w.key}">${badge}<span class="tile sm ${w.tone === 'white' ? '' : w.tone}">${EA.icon(w.icon, { size: 20 })}</span><p class="way-label">${w.label}</p><p class="way-meta">${meta}</p></button>`;
    }).join('');
    el.querySelectorAll('.way').forEach((b) => {
      const w = ways.find((x) => x.key === b.dataset.way);
      b.onclick = () => w.go();
    });
  }

  // The big card under the strip: what Envie suggests today
  function renderLead(st, d, action, daily) {
    const el = document.getElementById('hub-lead');
    if (!el) return;
    const c = st.credits;

    if (d.lead === 'credit') {
      const held = c.held;
      el.innerHTML = `
        <div class="lead-wrap">
          <envie-mascot pose="point" hat="cap" aria-hidden="true"></envie-mascot>
          <div class="lead-card credit" onclick="openCredit()">
            <div class="lead-top"><span class="pill solid-sky">Action · ${held} credit${held > 1 ? 's' : ''} ready</span></div>
            <div class="lead-main"><span class="tile lg sky ring">${EA.icon('sprout', { size: 26 })}</span><p class="lead-title">Turn ${fmt(c.step)} points into a real tree</p></div>
            <p class="lead-desc">Your points became something that goes in the ground. Pick where it goes and we send it to the partner.</p>
            <div class="lead-actions"><button type="button" class="cta sky" onclick="event.stopPropagation(); openCredit()">Spend my credit ${EA.icon('arrow-right', { size: 17 })}</button><button type="button" class="cta ghost sm" onclick="event.stopPropagation(); openActions()">Others</button></div>
          </div>
        </div>`;
      return;
    }

    if (d.lead === 'action' && action) {
      el.innerHTML = `
        <div class="lead-wrap">
          <envie-mascot pose="point" hat="cap" aria-hidden="true"></envie-mascot>
          <div class="lead-card action" onclick="openActions('${esc(action.id)}')">
            <div class="lead-top"><span class="pill sky">Suggested action</span><span class="lead-sub">${esc(typeLabel(action.type))} · ${esc(action.level)}</span></div>
            <div class="lead-main"><span class="tile lg sky">${EA.icon(TYPE_ICON[action.type] || 'sprout', { size: 26 })}</span><p class="lead-title">${esc(action.title)}</p></div>
            <p class="lead-desc">${esc(action.desc)}</p>
            <div class="lead-actions"><button type="button" class="cta sky" onclick="event.stopPropagation(); pledgeFromHome('${esc(action.id)}')">Pledge today ${EA.icon('arrow-right', { size: 17 })}</button><span class="lead-pts">+${action.points} pts</span></div>
          </div>
        </div>`;
      return;
    }

    if (d.lead === 'story' && d.unreadStory) {
      const s = d.unreadStory;
      el.innerHTML = `
        <div class="lead-card story" onclick="openCourse('${esc(s.id)}')">
          <div class="lead-top"><span class="pill amber">Today's story</span><span class="lead-sub">${esc(s.tag)} · ${esc(s.meta)}</span></div>
          <div class="lead-main"><span class="tile lg amber">${EA.icon('book-open', { size: 26 })}</span><p class="lead-title">${esc(s.title)}</p></div>
          <div class="lead-actions"><button type="button" class="cta amber">Read ${EA.icon('arrow-right', { size: 17 })}</button><span class="lead-pts">+75 pts</span></div>
        </div>`;
      return;
    }

    if (d.lead === 'done') {
      el.innerHTML = `
        <div class="lead-wrap">
          <envie-mascot pose="celebrate" hat="hat" aria-hidden="true"></envie-mascot>
          <div class="lead-card done-card">
            <div class="lead-top"><span class="pill sky">Streak kept</span></div>
            <p class="lead-title">Game, story and action. All three today.</p>
            <p class="lead-desc">Envie has nothing left to hand you. Come back tomorrow, or ask the companion anything.</p>
            <div class="lead-actions"><button type="button" class="cta ghost" onclick="switchTab('companion')">Open the companion</button></div>
          </div>
        </div>`;
      return;
    }

    // Default: the game. Today's dated challenge, or the free games.
    const played = st.done.game;
    let top, titleTxt, sub, cta, go;
    if (daily) {
      top = played ? 'Done today' : "Today's game";
      titleTxt = daily.title_line1 + ' ' + daily.title_line2;
      sub = daily.category_badge + ' · 3 questions';
      cta = played ? 'Play another' : 'Play';
      go = played ? () => switchTab('games') : () => openGame('quiz', 'daily');
    } else {
      const upcoming = (_loadedChallenges || []).filter((c) => c.date > st.today).sort((a, b) => a.date.localeCompare(b.date))[0];
      top = st.isWeekend ? 'Weekend games' : 'Free games';
      titleTxt = 'No dated challenge today. The library is open.';
      sub = upcoming ? 'Next challenge ' + unlockLabel(upcoming.date) : 'Quiz, carbon sort, water';
      cta = 'Pick a game';
      go = () => switchTab('games');
    }
    el.innerHTML = `
      <div>
        <div class="lead-card game ${played ? 'played' : ''}">
          <div class="lead-top"><span class="pill ${played ? 'sky' : 'aqua'}">${top}</span><span class="lead-sub">${esc(sub)}</span></div>
          <div class="lead-main"><span class="tile lg aqua">${EA.icon('globe', { size: 26 })}</span><p class="lead-title">${esc(titleTxt)}</p></div>
          <div class="lead-actions"><button type="button" class="cta amber" id="hub-lead-cta">${cta} ${EA.icon('arrow-right', { size: 17 })}</button><span class="lead-pts">${daily ? '450 pts max' : 'up to 480 pts'}</span></div>
        </div>
      </div>`;
    const card = el.querySelector('.lead-card');
    card.onclick = go;
  }

  // Below the lead: the smaller action row and the credit progress bar
  function renderMore(st, d, action) {
    const el = document.getElementById('hub-more');
    if (!el) return;
    const c = st.credits;
    let html = '';

    if ((d.showActionCard || d.showSmallActionUnderCredit) && action) {
      html += `<p class="eyebrow">${d.lead === 'credit' ? 'Or a smaller action today' : 'Suggested action'}</p>
        <div class="row act-row ${d.lead === 'credit' ? '' : 'dashed'}" onclick="openActions('${esc(action.id)}')">
          <span class="tile sm ${d.lead === 'credit' ? 'amber' : 'sky'}">${EA.icon('check', { size: 21 })}</span>
          <div class="row-body"><p class="row-title">${esc(action.title)}</p><p class="row-meta">Pledge today · +${action.points} pts</p></div>
          ${EA.icon('chevron-right', { size: 18, cls: 'row-chev' })}
        </div>`;
    } else if (st.done.action && st.actionsToday.length) {
      html += `<p class="eyebrow">Action pledged</p>
        <div class="row act-row done" onclick="openProfile()">
          <span class="tile sm sky">${EA.icon('check', { size: 21 })}</span>
          <div class="row-body"><p class="row-title">${esc(st.actionsToday[0].title)}</p><p class="row-meta">Pledged today · streak kept</p></div>
          ${EA.icon('chevron-right', { size: 18, cls: 'row-chev' })}
        </div>`;
    }

    const barTxt = c.held > 0
      ? `${c.held} credit${c.held > 1 ? 's' : ''} held · next at ${fmt(c.nextAt)} pts`
      : c.earned === 0
        ? `${fmt(c.total)} / ${fmt(c.step)} pts to your first impact credit`
        : `${fmt(c.total)} / ${fmt(c.nextAt)} pts to your next credit`;
    html += `
      <div class="credit-bar" onclick="openProfile()">
        <span class="tile sm sky">${EA.icon('sprout', { size: 17 })}</span>
        <div class="credit-bar-body"><p>${barTxt}</p><div class="bar"><div class="bar-fill" style="width:${Math.max(3, c.pct)}%"></div></div></div>
      </div>`;
    const p = st.progress;
    html += `
      <div class="hub-progress-card home-stats">
        <div class="progress-stat"><div class="progress-stat-num">${fmt(p.totalScore)}</div><div class="progress-stat-label">Score</div></div>
        <div class="progress-stat"><div class="progress-stat-num">${p.sessionsPlayed || 0}</div><div class="progress-stat-label">Games</div></div>
        <div class="progress-stat"><div class="progress-stat-num">${p.actionsPledged || 0}</div><div class="progress-stat-label">Pledged</div></div>
        <div class="progress-stat"><div class="progress-stat-num streak">${EA.icon('flame', { size: 15 })}${st.streak}</div><div class="progress-stat-label">Streak</div></div>
      </div>`;
    el.innerHTML = html;
  }

  function renderProfileBadge(st) {
    document.querySelectorAll('.profile-icon-btn').forEach((b) => {
      let badge = b.querySelector('.credit-badge');
      if (st.credits.held > 0) {
        if (!badge) { badge = document.createElement('span'); badge.className = 'credit-badge'; b.appendChild(badge); }
        badge.textContent = st.credits.held;
      } else if (badge) badge.remove();
    });
  }

  function unlockLabel(dateStr) {
    const d = new Date(dateStr + 'T00:00:00');
    const t = new Date(); t.setDate(t.getDate() + 1);
    if (d.toDateString() === t.toDateString()) return 'tomorrow';
    return d.toLocaleDateString('en-IE', { weekday: 'long' });
  }

  function typeLabel(t) { return (Engine.types() || {})[t] || t; }

  // ── ACTIONS SCREEN ──────────────────────────────────────
  let _filter = 'all';
  let _highlight = null;
  let _showAll = false;

  async function openActions(highlightId, filter) {
    _highlight = highlightId || null;
    _filter = filter || (_highlight ? 'all' : _filter);
    _showAll = false;
    await Engine.loadActions();
    renderActions();
    setActiveTab('');
    showScreen('screen-actions');
    if (_highlight) setTimeout(() => {
      const card = document.querySelector('#screen-actions .act-card.hi');
      if (card) card.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }, 350);
  }

  function renderActions() {
    const nk = nkOf();
    const st = Engine.state(nk);
    const ranked = Engine.rankActions(nk, st, _loadedChallenges);
    const types = Engine.types();
    const left = st.actionsLeftThisWeek;

    // Envie's line
    const bubble = document.getElementById('actions-bubble');
    if (bubble) {
      let head, sub;
      if (left <= 0) { head = 'That is your week sorted.'; sub = `You have pledged ${Engine.ACTIONS_PER_WEEK} this week. Browse for ideas, pledge again from Monday.`; }
      else if (st.actionsWeek.length) { head = 'One more this week, if you like.'; sub = 'Small and specific beats big and vague. Pick something you would actually do.'; }
      else { head = 'Pick one you can actually do.'; sub = 'These come from what you have been playing and reading. Pledge it, and it counts as today.'; }
      bubble.innerHTML = `<p class="bh">${head}</p><p class="bs">${sub}</p>` + EA.TAIL;
    }
    const cap = document.getElementById('actions-cap');
    if (cap) cap.textContent = left > 0 ? `${left} left this week` : 'Week done';

    // Filters
    const fl = document.getElementById('actions-filters');
    if (fl) {
      const keys = ['all'].concat(Object.keys(types));
      fl.innerHTML = keys.map((k) => `<button type="button" class="chip ${k === _filter ? 'on' : ''}" data-f="${k}">${k === 'all' ? 'For you' : esc(types[k])}</button>`).join('');
      fl.querySelectorAll('.chip').forEach((b) => b.onclick = () => { _filter = b.dataset.f; _showAll = false; renderActions(); });
    }

    // List
    const list = document.getElementById('actions-list');
    if (!list) return;
    let items = _filter === 'all' ? ranked : ranked.filter((a) => a.type === _filter);
    if (_highlight) {
      const hi = items.find((a) => a.id === _highlight);
      if (hi) items = [hi].concat(items.filter((a) => a !== hi));
    }
    const LIMIT = 10;
    const shown = _showAll ? items : items.slice(0, LIMIT);
    const pledgedIds = new Set(st.history.map((h) => h.id || h.title));
    list.innerHTML = shown.map((a) => actionCard(a, left > 0, a.id === _highlight, pledgedIds.has(a.id))).join('')
      + (items.length > shown.length ? `<button type="button" class="cta ghost" id="actions-more">Show ${items.length - shown.length} more</button>` : '')
      + (items.length === 0 ? '<p class="actions-empty">' + (ranked.length === 0 && !st.history.length
          ? 'The actions list could not be loaded. Open the app through a web server, not as a file.'
          : 'Nothing left in this list. You have pledged them all.') + '</p>' : '');
    const more = document.getElementById('actions-more');
    if (more) more.onclick = () => { _showAll = true; renderActions(); };
    list.querySelectorAll('[data-pledge]').forEach((b) => b.onclick = () => pledgeAction(b.dataset.pledge));
    if (window.EA && EA.icons) EA.icons(list);
  }

  function actionCard(a, canPledge, hi, pledged) {
    const tone = TYPE_TONE[a.type] || 'sky';
    return `
      <div class="act-card ${hi ? 'hi' : ''} ${pledged ? 'pledged' : ''}" id="act-${esc(a.id)}">
        <div class="act-head">
          <span class="tile sm ${tone}">${EA.icon(TYPE_ICON[a.type] || 'sprout', { size: 20 })}</span>
          <div class="act-body">
            <p class="act-tags"><span>${esc(typeLabel(a.type))}</span><span class="lvl ${a.level}">${a.level === 'easy' ? 'Easy' : 'Medium'}</span>${a.scope === 'community' && a.type !== 'community' ? '<span>Community</span>' : ''}</p>
            <p class="act-title">${esc(a.title)}</p>
          </div>
        </div>
        <p class="act-desc">${esc(a.desc)}</p>
        <div class="act-foot">
          <span class="act-pts">+${a.points} pts${a.impact ? ' · ' + esc(a.impact) : ''}</span>
          ${pledged ? '<span class="act-done">' + EA.icon('check', { size: 14 }) + ' Pledged</span>'
                    : `<button type="button" class="act-btn" data-pledge="${esc(a.id)}" ${canPledge ? '' : 'disabled'}>Pledge</button>`}
        </div>
      </div>`;
  }

  async function pledgeAction(id) {
    await Engine.loadActions();
    const nk = nkOf();
    const st = Engine.state(nk);
    const a = Engine.rankActions(nk, st, _loadedChallenges).find((x) => x.id === id)
      || ((await Engine.loadActions()).actions || []).find((x) => x.id === id);
    if (!a) return;
    const r = Engine.pledge(nk, a);
    if (!r.ok) { renderActions(); return; }
    syncPlayerToFirestore(nk);
    showToast('Pledged. +' + r.points + ' pts', r.streak >= 2 ? r.streak + '-day streak' : 'Streak started', 'flag');
    renderActions();
  }
  async function pledgeFromHome(id) {
    await pledgeAction(id);
    renderHome();
  }

  // ── CREDIT SCREEN ───────────────────────────────────────
  let _partnerPick = null;
  async function openCredit() {
    _partnerPick = null;
    await Engine.loadPartners();
    renderCredit();
    setActiveTab('');
    showScreen('screen-credit');
  }

  async function renderCredit() {
    const nk = nkOf();
    const partners = await Engine.loadPartners();
    const c = Engine.credits(nk);
    const body = document.getElementById('credit-body');
    if (!body) return;
    const held = c.held;

    const hero = held > 0
      ? `<div class="erow center lg"><envie-mascot pose="celebrate" hat="hat" aria-hidden="true"></envie-mascot><div class="bb r3 tail-low"><p class="bh">${held} credit${held > 1 ? 's' : ''} ready to plant.</p><p class="bs">Pick where it goes. We send it to the partner, you get the photo back.</p>${EA.TAIL}</div></div>`
      : `<div class="erow center lg"><envie-mascot pose="think" hat="hat" aria-hidden="true"></envie-mascot><div class="bb r2 tail-low"><p class="bh">No credit yet.</p><p class="bs">${fmt(c.toGo)} points to go. Every game, story and action counts.</p>${EA.TAIL}</div></div>`;

    const rows = partners.map((p) => {
      const can = held >= p.credits;
      const sel = _partnerPick === p.id;
      return `<div class="row partner ${sel ? 'sel' : ''} ${can ? '' : 'locked'}" data-p="${esc(p.id)}">
        <span class="tile sm ${p.tone}">${EA.icon(p.icon, { size: 21 })}</span>
        <div class="row-body"><p class="row-title">${esc(p.title)}</p><p class="row-meta">${esc(p.sub)}</p></div>
        <span class="pill ${can ? 'sky' : ''}">${p.credits} credit${p.credits > 1 ? 's' : ''}</span>
      </div>`;
    }).join('');

    const picked = partners.find((p) => p.id === _partnerPick);
    const confirm = picked ? `
      <div class="credit-confirm">
        <p class="credit-confirm-desc">${esc(picked.desc)}</p>
        <button type="button" class="cta sky" id="credit-go">Plant it · ${picked.credits} credit${picked.credits > 1 ? 's' : ''} ${EA.icon('arrow-right', { size: 17 })}</button>
        <p class="credit-note">Placeholder partner. Nothing is sent yet, the pledge is saved to your profile.</p>
      </div>` : '';

    const ledger = c.ledger.length ? `<p class="eyebrow">Already planted</p>` + c.ledger.slice().reverse().map((e) =>
      `<div class="row done"><span class="tile sm sky">${EA.icon('check', { size: 19 })}</span><div class="row-body"><p class="row-title">${esc(e.title)}</p><p class="row-meta">${formatRelativeDate(e.date)} · ${e.credits} credit${e.credits > 1 ? 's' : ''} · ${esc(e.status)}</p></div></div>`).join('') : '';

    body.innerHTML = `${hero}
      <div class="credit-list-head"><p class="eyebrow">Where it can go</p><span>${partners.length} partners</span></div>
      <div class="credit-list">${rows}</div>${confirm}
      <div class="credit-bar static"><div class="credit-bar-body"><p>Next credit at ${fmt(c.nextAt)} pts · <strong>${fmt(c.toGo)} to go</strong></p><div class="bar"><div class="bar-fill" style="width:${Math.max(3, c.pct)}%"></div></div></div></div>
      ${ledger}`;
    body.querySelectorAll('.partner').forEach((r) => r.onclick = () => {
      if (r.classList.contains('locked')) return;
      _partnerPick = r.dataset.p === _partnerPick ? null : r.dataset.p;
      renderCredit();
    });
    const go = document.getElementById('credit-go');
    if (go) go.onclick = () => spendCredit(picked);
  }

  function spendCredit(partner) {
    const nk = nkOf();
    const r = Engine.spendCredit(nk, partner);
    if (!r.ok) return;
    syncPlayerToFirestore(nk);
    _partnerPick = null;
    showToast(partner.title, 'Sent to the partner. Counts as today\'s action.', 'celebrate');
    renderCredit();
  }

  // ── PROFILE ─────────────────────────────────────────────
  function openProfile() {
    const nk = nkOf();
    const st = Engine.state(nk);
    const c  = st.credits;
    const p  = st.progress;
    const level = Math.floor((p.totalScore || 0) / 250) + 1;

    setText('profile-name', playerName || 'Player');
    setText('profile-tag', 'Player · Level ' + level);
    setText('profile-total-score', fmt(p.totalScore));
    setText('profile-sessions', p.sessionsPlayed || 0);
    setText('profile-actions', p.actionsPledged || 0);
    const stk = document.getElementById('profile-streak');
    if (stk) stk.innerHTML = EA.icon('flame', { size: 15 }) + st.streak;
    const av = document.querySelector('#screen-profile .profile-envie');
    if (av) av.setAttribute('pose', c.held > 0 ? 'celebrate' : 'wave');

    // Credit card: ready vs progress
    const card = document.getElementById('profile-credit');
    if (card) {
      if (c.held > 0) {
        card.className = 'credit-card ready';
        card.innerHTML = `
          <div class="credit-ready-head"><span class="tile lg sky ring round">${EA.icon('sprout', { size: 28 })}</span>
            <div><p class="eyebrow sky">Milestone reached</p><p class="credit-ready-title">${c.held} credit${c.held > 1 ? 's' : ''} ready to plant</p></div></div>
          <p class="credit-ready-desc">${fmt(c.step)} points became one real thing in the ground. Pick where it goes and we send it to the partner, you get the photo back.</p>
          <div class="lead-actions"><button type="button" class="cta sky" onclick="openCredit()">Use my credit ${EA.icon('arrow-right', { size: 17 })}</button><button type="button" class="cta ghost sm" onclick="saveCredit()">Save it</button></div>`;
      } else {
        const gamesLeft = Math.max(1, Math.ceil(c.toGo / 450));
        card.className = 'credit-card';
        card.innerHTML = `
          <div class="credit-head"><div><p class="eyebrow">Impact credits</p><p class="credit-title">Turn points into planting</p></div><span class="pill">${EA.icon('clock', { size: 14 })} ${c.spent} planted</span></div>
          <div class="credit-num"><span class="big">${fmt(c.total)}</span><span class="of">/ ${fmt(c.nextAt)} pts</span></div>
          <div class="bar tall"><div class="bar-fill" style="width:${Math.max(2, c.pct)}%"></div></div>
          <div class="credit-foot"><p><strong>${fmt(c.toGo)} points</strong> to your ${c.earned === 0 ? 'first' : 'next'} credit</p><span>≈ ${gamesLeft} game${gamesLeft > 1 ? 's' : ''}</span></div>
          <div class="credit-what"><span class="tile sm sky">${EA.icon('sprout', { size: 19 })}</span><p>1 credit funds a native tree, a metre of hedgerow or a pollinator patch with our partners.</p></div>`;
      }
    }

    // Milestones: first badge, then the next two credits
    const ms = document.getElementById('profile-milestones');
    if (ms) {
      const marks = [
        { at: c.firstMilestone, label: fmt(c.firstMilestone) + ' pts', n: '' },
        { at: (c.earned + 1) * c.step, label: fmt((c.earned + 1) * c.step) + ' pts', n: String(c.earned + 1) },
        { at: (c.earned + 2) * c.step, label: fmt((c.earned + 2) * c.step) + ' pts', n: String(c.earned + 2) },
      ];
      if (c.earned > 0) marks[0] = { at: c.earned * c.step, label: fmt(c.earned * c.step) + ' pts', n: String(c.earned) };
      ms.innerHTML = marks.map((m, i) => {
        const hit = c.total >= m.at;
        const next = !hit && (i === 0 || c.total >= marks[i - 1].at);
        const dot = hit ? `<span class="ms-dot hit">${EA.icon('check', { size: 15 })}</span>` : `<span class="ms-dot ${next ? 'next' : ''}">${m.n}</span>`;
        const line = i < marks.length - 1 ? `<span class="ms-line ${hit ? 'hit' : ''}"></span>` : '';
        return `<div class="ms ${hit ? 'hit' : ''} ${next ? 'next' : ''}">${dot}<span class="ms-label">${m.label}</span></div>${line}`;
      }).join('');
    }

    renderActionHistory(nk, st);
    setActiveTab('');
    showScreen('screen-profile');
  }

  function saveCredit() {
    showToast('Saved', 'Your credit stays on your profile until you use it.', 'flag');
  }

  function renderActionHistory(nk, st) {
    const list = document.getElementById('actions-history-list');
    if (!list) return;
    const history = (st || Engine.state(nk)).history;
    if (!history.length) {
      list.innerHTML = '<div class="actions-history-empty">Nothing pledged yet. Envie will suggest one when the week is far enough along.</div>';
      return;
    }
    list.innerHTML = history.slice().reverse().map((a) => `
      <div class="actions-history-item">
        <div class="actions-history-check">${EA.icon(a.type === 'credit' ? 'sprout' : 'check', { size: 14 })}</div>
        <div class="actions-history-content">
          <p class="actions-history-title">${esc(a.title)}</p>
          <p class="actions-history-date">${formatRelativeDate(a.date)}${a.type === 'credit' ? ' · impact credit' : a.points ? ' · +' + a.points + ' pts' : ''}</p>
        </div>
      </div>`).join('');
  }

  // ── toast ───────────────────────────────────────────────
  let _toastTimer = null;
  function showToast(head, sub, pose) {
    let t = document.getElementById('env-toast');
    if (!t) { t = document.createElement('div'); t.id = 'env-toast'; document.body.appendChild(t); }
    t.innerHTML = `<div class="erow sm"><envie-mascot pose="${pose || 'flag'}" hat="hat" aria-hidden="true"></envie-mascot><div class="bb r2 sky tail-low"><p class="bh">${esc(head)}</p>${sub ? '<p class="bs">' + esc(sub) + '</p>' : ''}${EA.TAIL}</div></div>`;
    t.classList.add('on');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => t.classList.remove('on'), 3200);
  }

  function setText(id, v) { const el = document.getElementById(id); if (el) el.textContent = v; }

  window.renderHome = renderHome;
  window.openActions = openActions;
  window.pledgeFromHome = pledgeFromHome;
  window.openCredit = openCredit;
  window.openProfile = openProfile;
  window.saveCredit = saveCredit;
  window.renderActionHistory = renderActionHistory;
  window.showEnvieToast = showToast;
})();

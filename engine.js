/* ============================================================
   Environmentle home engine — daily challenges, actions, credits
   Loaded on index.html after app.js. Exposes window.Engine.

   What it knows:
   - the three daily challenge types (game · story · action) and
     which of them the player has done today / this week
   - the streak stamp shared by every challenge type
   - the actions repository (actions.json) and how to pick one
   - impact credits: earned from total score, spent with partners
   - the decision of what the home page leads with today

   Storage keys are per player (nk = normalised name), matching
   the keys the games already write, so nothing in the games
   needs to know this file exists.
   ============================================================ */
(function () {
  'use strict';

  const CREDIT_STEP        = 1500;  // points per impact credit: about 12 days of one challenge a day
  const FIRST_MILESTONE    = 500;   // first badge before the first credit
  const LEVEL_STEP         = 500;   // a level every 500 pts: three levels per credit
  const ACTIONS_PER_WEEK   = 2;     // hard cap on pledges per week
  const CHALLENGES_PER_DAY = 2;     // one is the rhythm, two is the cap
  const SUGGEST_FROM_DOW   = 4;     // Thursday: action suggestions start
  const SUGGEST_AFTER_DAYS = 2;     // ...or once 2 challenge days are done this week
  const ACTIVITY_CAP       = 90;
  const HISTORY_CAP        = 40;

  // ── date helpers ────────────────────────────────────────
  const todayStr = () => new Date().toISOString().slice(0, 10);
  function yesterdayStr() {
    return new Date(Date.now() - 86400000).toISOString().slice(0, 10);
  }
  // Monday-based week, as YYYY-MM-DD. All date strings in the app come from
  // toISOString() (the games included), so the week maths stays in UTC too;
  // mixing local midnight with ISO dates put Sunday outside its own week.
  function weekStart(d) {
    const dt = new Date((d || todayStr()) + 'T00:00:00Z');
    const dow = (dt.getUTCDay() + 6) % 7; // Mon=0
    dt.setUTCDate(dt.getUTCDate() - dow);
    return dt.toISOString().slice(0, 10);
  }
  function weekEnd(d) {
    const dt = new Date(weekStart(d) + 'T00:00:00Z');
    dt.setUTCDate(dt.getUTCDate() + 6);
    return dt.toISOString().slice(0, 10);
  }
  const inThisWeek = (iso) => iso && iso >= weekStart() && iso <= weekEnd();

  // ── storage ─────────────────────────────────────────────
  function get(key, fallback) {
    try { const v = localStorage.getItem(key); return v === null ? fallback : JSON.parse(v); }
    catch (e) { return fallback; }
  }
  function set(key, val) {
    try { localStorage.setItem(key, JSON.stringify(val)); } catch (e) { /* storage off */ }
  }
  const K = {
    progress: (nk) => 'env_progress_' + nk,
    streak:   (nk) => 'streak_' + nk,
    history:  (nk) => 'actions_history_' + nk,
    activity: (nk) => 'activity_' + nk,
    ledger:   (nk) => 'credit_ledger_' + nk,
    story:    (nk) => 'story_last_read_' + nk,
    stories:  (nk) => 'completed_stories_' + nk,
    recent:   (nk) => 'recent_games_' + nk,
    creditSeen: (nk) => 'credit_seen_' + nk,
    skips:    (nk) => 'action_skips_' + nk,
  };

  // ── streak (same algorithm the games use) ───────────────
  function stampStreak(nk) {
    const key = K.streak(nk);
    const data = get(key, {});
    const today = todayStr();
    if (data.lastDate === today) return data.count || 1;
    if (data.lastDate === yesterdayStr()) data.count = (data.count || 0) + 1;
    else data.count = 1;
    data.lastDate = today;
    set(key, data);
    return data.count;
  }

  // ── activity log: one entry per challenge completed ─────
  function logActivity(nk, type, extra) {
    const log = get(K.activity(nk), []);
    const today = todayStr();
    if (log.some((e) => e.date === today && e.type === type && (!extra || e.ref === extra))) return;
    log.push(Object.assign({ date: today, type }, extra ? { ref: extra } : {}));
    set(K.activity(nk), log.slice(-ACTIVITY_CAP));
  }

  // Games only stamp their own "last played" keys; fold those into the log
  // so the week view is complete without changing the games.
  function reconcileGames(nk) {
    const today = todayStr();
    const played = {
      quiz:  localStorage.getItem('quiz_last_played_' + nk),
      sort:  localStorage.getItem('sio_last_played_'  + nk),
      water: localStorage.getItem('wc_last_played_'   + nk),
    };
    Object.keys(played).forEach((g) => { if (played[g] === today) logActivity(nk, 'game', g); });
    return played;
  }

  // ── progress / credits ──────────────────────────────────
  function progress(nk) { return get(K.progress(nk), {}); }
  function addPoints(nk, pts, isAction) {
    const p = progress(nk);
    p.totalScore = (p.totalScore || 0) + pts;
    if (isAction) p.actionsPledged = (p.actionsPledged || 0) + 1;
    p.sessionsPlayed = p.sessionsPlayed || 0;
    set(K.progress(nk), p);
    return p;
  }

  function level(nk) { return Math.floor((progress(nk).totalScore || 0) / LEVEL_STEP) + 1; }

  function credits(nk) {
    const total  = progress(nk).totalScore || 0;
    const earned = Math.floor(total / CREDIT_STEP);
    const ledger = get(K.ledger(nk), []);
    const spent  = ledger.reduce((s, e) => s + (e.credits || 0), 0);
    const held   = Math.max(0, earned - spent);
    const nextAt = (earned + 1) * CREDIT_STEP;
    const into   = total - earned * CREDIT_STEP;
    return {
      total, earned, spent, held, ledger,
      nextAt, toGo: nextAt - total,
      pct: Math.min(100, Math.round((into / CREDIT_STEP) * 100)),
      step: CREDIT_STEP,
      firstMilestone: FIRST_MILESTONE,
      firstMilestoneHit: total >= FIRST_MILESTONE,
    };
  }

  // ── day / week state ────────────────────────────────────
  function state(nk) {
    const today   = todayStr();
    const played  = reconcileGames(nk);
    const history = get(K.history(nk), []);
    const log     = get(K.activity(nk), []);
    const dow     = new Date(today + 'T00:00:00Z').getUTCDay(); // 0 Sun .. 6 Sat, same frame as today

    const gamesDoneToday = Object.keys(played).filter((g) => played[g] === today);
    const storyDone      = localStorage.getItem(K.story(nk)) === today;
    const actionsToday   = history.filter((a) => a.date === today);
    const actionsWeek    = history.filter((a) => inThisWeek(a.date));
    const weekDays       = new Set(log.filter((e) => inThisWeek(e.date)).map((e) => e.date));

    const done = {
      game:   gamesDoneToday.length > 0,
      story:  storyDone,
      action: actionsToday.length > 0,
    };
    const doneCount = Object.values(done).filter(Boolean).length;

    const skips = get(K.skips(nk), []);
    return {
      today, dow,
      skips,
      skippedThisWeek: new Set(skips.filter((e) => inThisWeek(e.date)).map((e) => e.id)),
      skipCounts: skips.reduce((m, e) => { m[e.id] = (m[e.id] || 0) + 1; return m; }, {}),
      isWeekend: dow === 0 || dow === 6,
      played, gamesDoneToday,
      done, doneCount, anyDone: doneCount > 0,
      actionsToday, actionsWeek,
      actionsLeftThisWeek: Math.max(0, ACTIONS_PER_WEEK - actionsWeek.length),
      weekDaysActive: weekDays.size,
      streak: get(K.streak(nk), {}).count || 0,
      credits: credits(nk),
      progress: progress(nk),
      history,
      completedStories: get(K.stories(nk), []),
    };
  }

  // ── actions repository ──────────────────────────────────
  let _actions = null;   // { meta, actions }
  let _partners = null;
  async function loadActions() {
    if (_actions) return _actions;
    try {
      const res = await fetch('actions.json', { cache: 'no-cache' });
      _actions = await res.json();
    } catch (e) { _actions = { meta: { types: {} }, actions: [] }; }
    return _actions;
  }
  async function loadPartners() {
    if (_partners) return _partners;
    try {
      const res = await fetch('partners.json', { cache: 'no-cache' });
      _partners = (await res.json()).partners || [];
    } catch (e) { _partners = []; }
    return _partners;
  }

  // Stable per-player, per-week randomness so the suggestion does not
  // change every time the home page re-renders.
  function seed(str) {
    let h = 2166136261;
    for (let i = 0; i < str.length; i++) { h ^= str.charCodeAt(i); h = Math.imul(h, 16777619); }
    return (h >>> 0) / 4294967295;
  }

  // What the player has been learning about lately, as action types.
  // Games and stories map to the action types they touch on.
  const STORY_TYPES = {
    'climate-and-finance':  ['community', 'mindset'],
    'data-centres-ireland': ['energy', 'mindset'],
    'climate-wins-2025':    ['mindset', 'community'],
    'ben-and-jerrys':       ['food', 'community'],
    'ireland-forestry':     ['nature'],
  };
  const GAME_TYPES = { sort: ['transport', 'food', 'circularity'], water: ['water', 'food'], quiz: [] };

  function affinities(nk, st, challenges) {
    const score = {};
    const bump = (t, n) => { score[t] = (score[t] || 0) + n; };
    // this week's calendar themes: the daily challenges dated this week
    (challenges || []).forEach((c) => { if (inThisWeek(c.date)) bump('cal:' + c.id, 3); });
    // recent games
    get(K.recent(nk), []).slice(0, 6).forEach((r) => {
      const g = (r.key || '').split(':')[0];
      (GAME_TYPES[g] || []).forEach((t) => bump(t, 2));
    });
    // stories read (most recent last)
    st.completedStories.slice(-3).forEach((id) => (STORY_TYPES[id] || []).forEach((t) => bump(t, 2)));
    return score;
  }

  // Rank the repository for this player, this week. Returns sorted array.
  function rankActions(nk, st, challenges) {
    const all = (_actions && _actions.actions) || [];
    const pledged = new Set(st.history.map((a) => a.id || a.title));
    const aff = affinities(nk, st, challenges);
    const wk  = weekStart();
    const skippedWeek = st.skippedThisWeek || new Set();
    const skipCounts  = st.skipCounts || {};
    return all
      .filter((a) => !pledged.has(a.id) && !pledged.has(a.title))
      .filter((a) => !skippedWeek.has(a.id) && (skipCounts[a.id] || 0) < 2)   // "not this one" sticks for the week; twice and it is gone
      .map((a) => {
        let s = 0;
        if (a.source && a.source.kind === 'challenge' && aff['cal:' + a.source.id]) s += aff['cal:' + a.source.id];
        s += aff[a.type] || 0;
        if (a.level === 'easy') s += 1;
        if (a.type === 'mindset') s -= 1;                       // prefer doing over reading
        if (st.isWeekend && a.scope === 'community') s += 1;   // weekends suit the bigger ones
        s += seed(nk + wk + a.id) * 0.9;                        // stable jitter
        return { a, s };
      })
      .sort((x, y) => y.s - x.s)
      .map((x) => x.a);
  }

  // Which game to hand the player: today's dated quiz if it is still open,
  // otherwise the free game they have not touched for longest (and not
  // played today). Null when every game is done for the day.
  const GAME_NAMES = { quiz: 'Random quiz', sort: 'Sort it out', water: 'Water challenge' };
  function suggestGame(nk, st, dailyGame) {
    if (dailyGame && st.played.quiz !== st.today) {
      return { type: 'quiz', mode: 'daily', name: dailyGame.title_line1 + ' ' + dailyGame.title_line2, meta: dailyGame.category_badge + ' · 3 questions', pts: '150 pts max', daily: true };
    }
    const recent = get(K.recent(nk), []);
    const lastTs = (g) => { const r = recent.find((e) => (e.key || '').split(':')[0] === g); return r ? r.ts || 0 : 0; };
    const open = ['water', 'sort', 'quiz'].filter((g) => st.played[g] !== st.today);
    if (!open.length) return null;
    open.sort((a, b) => lastTs(a) - lastTs(b));
    const g = open[0];
    const meta = { quiz: 'Quiz · 3 questions', sort: 'Carbon · 6 cards', water: 'Higher or lower · 5 rounds' }[g];
    const pts  = '150 pts max';
    return { type: g, mode: g === 'quiz' ? 'random' : null, name: GAME_NAMES[g], meta, pts, daily: false };
  }

  // ── the decision: what does home lead with today ────────
  //   lead:  'credit' | 'game' | 'story' | 'action' | 'done'
  //   showActionCard: whether the smaller "Suggested action" row appears
  function decide(nk, ctx) {
    const st = ctx.state;
    const dailyGame = ctx.dailyChallenge;            // today's dated challenge or null
    const gameAvailable = !!dailyGame && !st.done.game;
    const anyGameLeft = ['quiz', 'sort', 'water'].some((g) => st.played[g] !== st.today);
    const game = suggestGame(nk, st, dailyGame);
    const unreadStory = ctx.stories.find((s) => !st.completedStories.includes(s.id)) || null;

    // Actions are rationed: none pledged this week yet, and either late in
    // the week or the player has already put in a couple of days.
    const actionWindow = st.actionsWeek.length === 0 &&
      (st.dow >= SUGGEST_FROM_DOW || st.dow === 0 || st.weekDaysActive >= SUGGEST_AFTER_DAYS);

    let lead, reason;
    if (st.doneCount >= CHALLENGES_PER_DAY) {
      lead = 'done'; reason = 'Day done';
    } else if (st.credits.held > 0 && !st.done.action) {
      lead = 'credit'; reason = 'A credit is waiting';
    } else if (actionWindow && !st.done.action && (st.isWeekend || st.done.game || st.done.story)) {
      lead = 'action'; reason = st.isWeekend ? 'Weekend: time to try something' : 'You have learnt; now act';
    } else if (gameAvailable) {
      lead = 'game'; reason = "Today's challenge";
    } else if (unreadStory && !st.done.story) {
      lead = 'story'; reason = 'A story you have not read';
    } else if (actionWindow && !st.done.action) {
      lead = 'action'; reason = 'An action fits today';
    } else if (st.done.game && st.done.story && st.done.action) {
      lead = 'done'; reason = 'All three done today';
    } else if (anyGameLeft) {
      lead = 'game'; reason = 'Free games';
    } else {
      lead = 'story'; reason = 'Read one again';
    }

    return {
      lead, reason,
      dailyGame, gameAvailable, anyGameLeft, unreadStory, game,
      dayDone: st.doneCount >= CHALLENGES_PER_DAY,
      showActionCard: actionWindow && !st.done.action && lead !== 'action' && lead !== 'credit' && lead !== 'done',
      showSmallActionUnderCredit: lead === 'credit' && st.actionsLeftThisWeek > 0,
    };
  }

  // ── writes: pledge an action, spend a credit, read a story ──
  function pledge(nk, action) {
    const st = state(nk);
    if (st.actionsLeftThisWeek <= 0) return { ok: false, why: 'cap' };
    if (st.history.some((h) => (h.id || h.title) === (action.id || action.title))) return { ok: false, why: 'dup' };
    const hist = st.history.concat([{ id: action.id, title: action.title, date: todayStr(), type: action.type, points: action.points }]);
    set(K.history(nk), hist.slice(-HISTORY_CAP));
    addPoints(nk, action.points || 0, true);
    const streak = stampStreak(nk);
    logActivity(nk, 'action', action.id);
    return { ok: true, streak, points: action.points || 0 };
  }

  const EMAIL_RX = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  function getEmail(nk) { return localStorage.getItem('player_email_' + nk) || ''; }
  function setEmail(nk, email) { localStorage.setItem('player_email_' + nk, email.trim()); }
  function validEmail(email) { return EMAIL_RX.test((email || '').trim()); }

  // Spending a credit is the one moment we ask for an email: it is how the
  // pledge gets validated and passed to the partner, and how the photo
  // comes back. Stored with the ledger entry and on the player record.
  function spendCredit(nk, partner, email) {
    const c = credits(nk);
    if (c.held < (partner.credits || 1)) return { ok: false, why: 'insufficient' };
    if (!validEmail(email)) return { ok: false, why: 'email' };
    setEmail(nk, email);
    const ledger = c.ledger.concat([{ partnerId: partner.id, title: partner.title, credits: partner.credits || 1, date: todayStr(), status: 'pending', email: email.trim() }]);
    set(K.ledger(nk), ledger);
    const hist = get(K.history(nk), []).concat([{ id: 'credit:' + partner.id + ':' + todayStr(), title: partner.title, date: todayStr(), type: 'credit', points: 0 }]);
    set(K.history(nk), hist.slice(-HISTORY_CAP));
    const p = progress(nk); p.actionsPledged = (p.actionsPledged || 0) + 1; set(K.progress(nk), p);
    const streak = stampStreak(nk);
    logActivity(nk, 'action', 'credit:' + partner.id);
    return { ok: true, streak };
  }

  // "Not this one": remembered for the week, and for good after the second time
  function skip(nk, id) {
    const skips = get(K.skips(nk), []);
    if (skips.some((e) => e.id === id && e.date === todayStr())) return;
    skips.push({ id, date: todayStr() });
    set(K.skips(nk), skips.slice(-120));
  }

  // Two challenges a day is the cap
  function dayDone(nk) { return state(nk).doneCount >= CHALLENGES_PER_DAY; }

  function recordStory(nk, id) {
    localStorage.setItem(K.story(nk), todayStr());
    const streak = stampStreak(nk);
    logActivity(nk, 'story', id);
    return streak;
  }

  // Fields the Firestore sync should carry, and how to merge them back
  function syncFields(nk) {
    return {
      activity:      get(K.activity(nk), []).slice(-ACTIVITY_CAP),
      creditLedger:  get(K.ledger(nk), []),
      storyLastRead: localStorage.getItem(K.story(nk)) || null,
      actionSkips:   get(K.skips(nk), []).slice(-120),
      email:         getEmail(nk) || null,
      creditsUsed:   get(K.ledger(nk), []).reduce((n, e) => n + (e.credits || 0), 0),
    };
  }
  function mergeRemote(nk, d) {
    if (!d) return;
    if (Array.isArray(d.activity)) {
      const seen = new Set();
      const all = get(K.activity(nk), []).concat(d.activity)
        .filter((e) => e && e.date && e.type)
        .filter((e) => { const k = e.date + '|' + e.type + '|' + (e.ref || ''); if (seen.has(k)) return false; seen.add(k); return true; })
        .sort((a, b) => a.date.localeCompare(b.date));
      set(K.activity(nk), all.slice(-ACTIVITY_CAP));
    }
    if (Array.isArray(d.creditLedger)) {
      const seen = new Set();
      const all = get(K.ledger(nk), []).concat(d.creditLedger)
        .filter((e) => e && e.partnerId && e.date)
        .filter((e) => { const k = e.partnerId + '|' + e.date; if (seen.has(k)) return false; seen.add(k); return true; });
      set(K.ledger(nk), all);
    }
    if (Array.isArray(d.actionSkips)) {
      const seen = new Set();
      const all = get(K.skips(nk), []).concat(d.actionSkips)
        .filter((e) => e && e.id && e.date)
        .filter((e) => { const k = e.id + '|' + e.date; if (seen.has(k)) return false; seen.add(k); return true; })
        .sort((a, b) => a.date.localeCompare(b.date));
      set(K.skips(nk), all.slice(-120));
    }
    if (d.email && !getEmail(nk)) setEmail(nk, d.email);
    if (d.storyLastRead) {
      const local = localStorage.getItem(K.story(nk));
      if (!local || d.storyLastRead > local) localStorage.setItem(K.story(nk), d.storyLastRead);
    }
  }

  window.Engine = {
    CREDIT_STEP, FIRST_MILESTONE, LEVEL_STEP, ACTIONS_PER_WEEK, CHALLENGES_PER_DAY,
    todayStr, weekStart, weekEnd, inThisWeek,
    state, credits, level, decide, rankActions, suggestGame, dayDone,
    loadActions, loadPartners,
    pledge, skip, spendCredit, recordStory, stampStreak, logActivity,
    getEmail, validEmail,
    syncFields, mergeRemote,
    types: () => (_actions && _actions.meta && _actions.meta.types) || {},
  };
})();

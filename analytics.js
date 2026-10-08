// ── Environmentle — Analytics + consent ─────────────
// One GA4 property for environmentle.org and app.environmentle.org,
// so a visit that starts on the splash carries on into the app.
//
// Canonical copy lives in app/; copy it to the root for the splash
// after edits (same as envie.js).
//
// Load this plain (no async/defer) in <head> so gtag(), track() and
// trackPage() exist before any page script runs. Add
// data-manual-pageview to the tag on pages that report their own
// first view (the app shell does it from showScreen()), and
// data-voice="envie" on app pages so the consent banner speaks as Envie.
//
// Nothing reaches Google until the visitor says yes. The choice is a
// cookie on .environmentle.org, so one answer covers splash and app.
(function () {
  var GA_ID = 'G-25VXXEP5EE';
  var COOKIE = 'env_consent';
  var MAX_AGE = 60 * 60 * 24 * 180;   // ask again after ~6 months

  var me = document.currentScript;
  var manualPageview = !!(me && me.hasAttribute('data-manual-pageview'));

  // ── gtag queue (works before gtag.js loads, or if it never does) ──
  window.dataLayer = window.dataLayer || [];
  window.gtag = function () { dataLayer.push(arguments); };

  gtag('consent', 'default', {
    analytics_storage: 'denied',
    ad_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied'
  });
  gtag('js', new Date());
  gtag('config', GA_ID, {
    send_page_view: !manualPageview,
    allow_google_signals: false,
    allow_ad_personalization_signals: false
  });

  // ── consent cookie ──
  function cookieDomain() {
    return /(^|\.)environmentle\.org$/.test(location.hostname) ? '; domain=.environmentle.org' : '';
  }
  function readChoice() {
    var m = document.cookie.match(new RegExp('(?:^|; )' + COOKIE + '=(granted|denied)'));
    return m ? m[1] : null;
  }
  function writeChoice(v) {
    document.cookie = COOKIE + '=' + v + '; max-age=' + MAX_AGE + '; path=/; SameSite=Lax' + cookieDomain();
  }
  function clearGaCookies() {
    document.cookie.split('; ').forEach(function (c) {
      var name = c.split('=')[0];
      if (name === '_ga' || name.indexOf('_ga_') === 0) {
        var expire = name + '=; max-age=0; path=/';
        document.cookie = expire;
        document.cookie = expire + cookieDomain();
      }
    });
  }

  var loaded = false;
  function loadGtag() {
    if (loaded) return;
    loaded = true;
    var s = document.createElement('script');
    s.async = true;
    s.src = 'https://www.googletagmanager.com/gtag/js?id=' + GA_ID;
    document.head.appendChild(s);
  }

  function apply(v) {
    if (v === 'granted') {
      gtag('consent', 'update', { analytics_storage: 'granted' });
      loadGtag();
    } else {
      gtag('consent', 'update', { analytics_storage: 'denied' });
      clearGaCookies();
    }
  }

  // ── banner ──
  var CSS =
    '.env-consent{position:fixed;left:16px;right:16px;bottom:calc(16px + env(safe-area-inset-bottom,0px));z-index:10000;' +
    'max-width:420px;margin:0 auto;padding:18px 18px 16px;border-radius:18px;background:#faf8f4;color:#1a1a1a;' +
    'box-shadow:0 18px 44px rgba(10,41,59,.28),0 0 0 1px rgba(10,41,59,.08);' +
    "font:15px/1.45 'Mulish','Helvetica Neue',Arial,sans-serif;" +
    'transform:translateY(12px);opacity:0;transition:transform .35s cubic-bezier(.2,.7,.2,1),opacity .35s}' +
    '.env-consent.in{transform:none;opacity:1}' +
    '.env-consent h2{margin:0 0 6px;font:700 16px/1.3 \'Comfortaa\',\'Trebuchet MS\',sans-serif;color:#0a293b}' +
    '.env-consent p{margin:0 0 14px;color:#3d3d3d}' +
    '.env-consent .row{display:flex;gap:10px}' +
    '.env-consent button{flex:1;min-height:44px;border-radius:999px;font-family:inherit;font-weight:700;font-size:15px;line-height:1;cursor:pointer;border:0}' +
    '.env-consent .yes{background:#4e8a4d;color:#fff}' +
    '.env-consent .no{background:#fff;color:#0a293b;box-shadow:inset 0 0 0 1.5px rgba(10,41,59,.25)}' +
    '.env-consent button:focus-visible{outline:3px solid #e67e22;outline-offset:2px}' +
    '@media (prefers-reduced-motion:reduce){.env-consent{transition:none}}';

  // Splash copy is plain; app pages add data-voice="envie" to the tag.
  var voice = me && me.getAttribute('data-voice') === 'envie' ? 'envie' : 'plain';
  var COPY = {
    plain: {
      title: 'Can we count visits?',
      body: 'It shows us which games, stories and features are useful, so we can make them better. ' +
            'No ads, and we never sell data. You can change your mind any time.'
    },
    envie: {
      title: 'Mind if I count visits?',
      body: 'It shows me which games and stories you actually use, so I can make them better. ' +
            'No ads, and I never sell data. You can change your mind in Privacy on your profile.'
    }
  };

  var banner = null;
  function openBanner() {
    if (banner) return;
    if (!document.body) { document.addEventListener('DOMContentLoaded', openBanner); return; }
    if (!document.getElementById('env-consent-css')) {
      var st = document.createElement('style');
      st.id = 'env-consent-css';
      st.textContent = CSS;
      document.head.appendChild(st);
    }
    banner = document.createElement('div');
    banner.className = 'env-consent';
    banner.setAttribute('role', 'dialog');
    banner.setAttribute('aria-labelledby', 'env-consent-title');
    var t = COPY[voice];
    banner.innerHTML =
      '<h2 id="env-consent-title">' + t.title + '</h2>' +
      '<p>' + t.body + '</p>' +
      '<div class="row"><button type="button" class="no">No thanks</button>' +
      '<button type="button" class="yes">Allow</button></div>';
    banner.querySelector('.yes').onclick = function () { choose('granted'); };
    banner.querySelector('.no').onclick = function () { choose('denied'); };
    document.body.appendChild(banner);
    requestAnimationFrame(function () { banner.classList.add('in'); });
  }
  function closeBanner() {
    if (!banner) return;
    var b = banner;
    banner = null;
    b.classList.remove('in');
    setTimeout(function () { b.remove(); }, 350);
  }
  function choose(v) {
    writeChoice(v);
    apply(v);
    closeBanner();
  }

  // Footer / Privacy links call envConsent.open() to change the answer.
  window.envConsent = {
    get: readChoice,
    open: openBanner,
    set: choose
  };

  var choice = readChoice();
  if (choice) apply(choice); else openBanner();

  // ── public helpers ──
  // Custom event: track('game_complete', { game: 'bin-day', score: 12 })
  window.track = function (name, params) {
    try { gtag('event', name, params || {}); } catch (e) {}
  };

  // Virtual page view for in-app screens that don't change the URL,
  // e.g. trackPage('games') reports as /games.
  window.trackPage = function (name, title) {
    try {
      gtag('event', 'page_view', {
        page_location: location.origin + '/' + name,
        page_title: title || name
      });
    } catch (e) {}
  };
})();

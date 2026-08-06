<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>AI Companion — The Uptake Games</title>
<link rel="icon" type="image/png" sizes="32x32" href="/_images/favicon-32.png">
<link rel="apple-touch-icon" sizes="180x180" href="/_images/icon-180.png">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="AI Companion">
<meta name="theme-color" content="#0a293b">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;600;700&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy:        #0a293b;
    --navy-mid:    #1a4258;
    --green:       #4e8a4d;
    --green-light: #7dbf7c;
    --green-pale:  #eaf5ea;
    --orange:      #e27e22;
    --orange-pale: #fdf0e3;
    --orange-dark: #904f15;
    --yellow:      #f7b801;
    --white:       #FFFFFF;
    --radius:      14px;
    --radius-sm:   8px;
    --surface-deep:    #001523;
    --surface-mid:     #022234;
    --surface-card:    #0f2d3f;
    --surface-card-hi: #1b384a;
    --surface-bright:  #203c4f;
    --on-surface-soft: #cae6fe;
  }

  html {
    height: 100%;
  }

  body {
    height: 100dvh;
    max-height: 100dvh;
    overflow: hidden;
    font-family: 'Montserrat', sans-serif;
    background: var(--surface-deep);
    color: white;
    display: flex;
    flex-direction: column;
    /* leave room for the fixed bottom nav */
    padding-bottom: calc(64px + env(safe-area-inset-bottom, 0px));
  }

  /* ── TOP BAR ── */
  .top-bar {
    flex-shrink: 0;
    background: rgba(10,41,59,0.97);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255,255,255,0.07);
    padding: calc(env(safe-area-inset-top, 0px) + 12px) 20px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 10;
  }

  .top-bar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
    text-decoration: none;
  }

  .top-bar-logo {
    flex-shrink: 0;
    object-fit: contain;
  }

  .top-bar-title {
    font-family: 'Comfortaa', cursive;
    font-size: 16px;
    font-weight: 700;
    color: white;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Profile button — matches the homepage */
  .profile-icon-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.9);
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.15s, transform 0.12s;
    padding: 0;
    text-decoration: none;
    flex-shrink: 0;
  }
  .profile-icon-btn:active { transform: scale(0.92); background: rgba(255,255,255,0.14); }
  .profile-icon-btn .material-symbols-outlined { font-size: 22px; }

  /* ── RESET CONVERSATION BUTTON (appended after the latest reply) ── */
  .reset-row {
    display: flex;
    justify-content: center;
    padding: 8px 0 4px;
  }
  .reset-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.55);
    font-family: 'Montserrat', sans-serif;
    font-size: 12px;
    font-weight: 500;
    padding: 7px 14px;
    border-radius: 100px;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.15s, color 0.15s;
  }
  .reset-btn:hover { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.8); }
  .reset-btn:active { transform: scale(0.97); }
  .reset-btn .material-symbols-outlined { font-size: 15px !important; }

  /* ── BOTTOM NAV (mirrors the homepage) ── */
  #app-bottom-nav {
    display: flex;
    position: fixed;
    bottom: 0; left: 0; right: 0;
    z-index: 300;
    background: rgba(10,41,59,0.97);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-top: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 -8px 28px rgba(0,0,0,0.35);
    justify-content: space-around;
    align-items: center;
    height: calc(64px + env(safe-area-inset-bottom, 0px));
    padding: 0 8px env(safe-area-inset-bottom, 0px);
  }
  .nav-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    padding: 7px 18px;
    border-radius: 100px;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.2s, color 0.2s, transform 0.15s;
    background: none;
    border: none;
    color: rgba(255,255,255,0.4);
    min-width: 60px;
    text-decoration: none;
    font-family: 'Montserrat', sans-serif;
  }
  .nav-tab .material-symbols-outlined { font-size: 18px !important; }
  .nav-tab-label {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
  }
  .nav-tab.active { color: var(--orange); }
  .nav-tab:active { transform: scale(0.88); }

  /* ── CHAT AREA ── */
  #chat-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 16px 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    scroll-behavior: smooth;
  }

  /* Reading order inside the scroller. The composer sits at the top while the
     thread is empty, so it reads as the invitation to start. Once there is a
     conversation it drops below the latest reply, which is where you look
     when you want to continue. */
  #chat-thread  { order: 1; display: flex; flex-direction: column; gap: 14px; }
  .input-area   { order: 2; }
  .chat-empty   { order: 3; }
  #reset-row    { order: 4; }
  body.chat-blank .input-area { order: 0; }
  /* nothing to lay out until a message exists — avoids a stray gap */
  #chat-thread:empty { display: none; }

  /* welcome empty state */
  .chat-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 14px;
    padding: 32px 24px;
    text-align: center;
  }

  .chat-empty-icon {
    width: 64px; height: 64px;
    border-radius: 18px;
    background: rgba(78,138,77,0.14);
    border: 1px solid rgba(78,138,77,0.22);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4px;
  }
  .chat-empty-icon .material-symbols-outlined { font-size: 34px !important; color: var(--green-light); }

  .chat-empty-title {
    font-family: 'Comfortaa', cursive;
    font-size: 18px;
    font-weight: 700;
    color: rgba(255,255,255,0.85);
  }

  .chat-empty-sub {
    font-size: 13px;
    color: rgba(255,255,255,0.45);
    line-height: 1.6;
    max-width: 280px;
  }

  .chat-empty-suggestions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
    max-width: 320px;
    margin-top: 8px;
  }

  .suggestion-chip {
    background: var(--surface-card);
    border: 1px solid rgba(255,255,255,0.09);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 12.5px;
    font-weight: 500;
    color: rgba(255,255,255,0.65);
    cursor: pointer;
    text-align: left;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
    line-height: 1.4;
  }
  .suggestion-chip:hover,
  .suggestion-chip:active {
    background: var(--surface-card-hi);
    border-color: rgba(78,138,77,0.3);
    color: white;
  }

  /* ── MESSAGE BUBBLES ── */
  .msg-row {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .msg-row.user { align-items: flex-end; }
  .msg-row.assistant { align-items: flex-start; }

  .msg-bubble {
    max-width: 88%;
    border-radius: 16px;
    padding: 11px 15px;
    font-size: 13.5px;
    line-height: 1.6;
    word-break: break-word;
  }

  .msg-row.user .msg-bubble {
    background: var(--orange);
    color: white;
    border-bottom-right-radius: 4px;
    font-weight: 500;
  }

  .msg-row.assistant .msg-bubble {
    background: var(--surface-card-hi);
    color: rgba(255,255,255,0.9);
    border-bottom-left-radius: 4px;
    border: 1px solid rgba(255,255,255,0.07);
  }

  .msg-bubble p { margin-bottom: 8px; }
  .msg-bubble p:last-child { margin-bottom: 0; }
  .msg-bubble strong { color: white; }
  .msg-bubble em { color: rgba(255,255,255,0.7); font-style: italic; }
  .msg-bubble ul, .msg-bubble ol { padding-left: 18px; margin-bottom: 8px; }
  .msg-bubble li { margin-bottom: 4px; }
  .msg-bubble a {
    color: var(--orange);
    text-decoration: underline;
    text-decoration-color: rgba(226,126,34,0.45);
    text-underline-offset: 2px;
    transition: color 0.15s, text-decoration-color 0.15s;
  }
  .msg-bubble a:hover {
    color: #f0a55a;
    text-decoration-color: rgba(240,165,90,0.7);
  }

  /* message bubble — richer markdown targets */
  .msg-bubble code {
    background: rgba(255,255,255,0.08);
    border-radius: 4px;
    padding: 1px 5px;
    font-size: 12px;
    font-family: monospace;
  }
  .msg-bubble h3 {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--green-light);
    margin-bottom: 6px;
    margin-top: 10px;
  }
  .msg-bubble h3:first-child { margin-top: 0; }

  /* source badges row */
  .msg-sources {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 6px;
  }

  .msg-source {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 20px;
    padding: 4px 10px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    text-decoration: none;
    white-space: nowrap;
  }
  .msg-source .material-symbols-outlined { font-size: 12px !important; }

  .msg-source.wiki {
    background: rgba(78,138,77,0.18);
    border: 1px solid rgba(78,138,77,0.3);
    color: var(--green-light);
    cursor: default;
  }

  .msg-source.ai {
    background: rgba(226,126,34,0.14);
    border: 1px solid rgba(226,126,34,0.25);
    color: #f0a55a;
    cursor: default;
  }

  .msg-source.ai-supplemented {
    background: rgba(247,184,1,0.1);
    border: 1px solid rgba(247,184,1,0.25);
    color: #f7b801;
    cursor: default;
  }

  /* further reading links */
  .msg-further {
    margin-top: 10px;
    padding: 10px 12px;
    background: rgba(78,138,77,0.07);
    border: 1px solid rgba(78,138,77,0.18);
    border-radius: 10px;
  }
  .msg-further-title {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--green-light);
    margin-bottom: 6px;
  }
  .msg-further a {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--orange);
    text-decoration: none;
    padding: 3px 0;
    transition: color 0.15s;
  }
  .msg-further a:hover { color: #f0a55a; }
  .msg-further a .material-symbols-outlined { font-size: 13px !important; color: var(--orange); flex-shrink: 0; }

  /* thinking indicator */
  .thinking-row {
    display: flex;
    align-items: flex-start;
    gap: 0;
  }

  .thinking-bubble {
    background: var(--surface-card-hi);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 16px;
    border-bottom-left-radius: 4px;
    padding: 13px 18px;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .thinking-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: rgba(255,255,255,0.35);
    animation: thinking 1.2s infinite ease-in-out;
  }
  .thinking-dot:nth-child(2) { animation-delay: 0.2s; }
  .thinking-dot:nth-child(3) { animation-delay: 0.4s; }

  @keyframes thinking {
    0%, 80%, 100% { transform: scale(0.7); opacity: 0.4; }
    40%           { transform: scale(1);   opacity: 1; }
  }

  /* ── INPUT AREA ── */
  /* In-flow composer. It used to be docked to the bottom of the body; it now
     scrolls with the thread so it can follow the latest answer. */
  .input-area {
    flex-shrink: 0;
    display: flex;
    gap: 10px;
    align-items: flex-end;
    scroll-margin-bottom: 20px;
  }

  .input-wrap {
    flex: 1;
    position: relative;
    background: var(--surface-card);
    border: 1.5px solid rgba(255,255,255,0.1);
    border-radius: 24px;
    transition: border-color 0.2s;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
  }
  .input-wrap:focus-within {
    border-color: rgba(78,138,77,0.5);
  }

  #user-input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    padding: 11px 16px;
    font-family: 'Montserrat', sans-serif;
    /* also 16px, to stop iOS zooming the page on focus */
    font-size: 16px;
    font-weight: 400;
    color: white;
    line-height: 1.5;
    resize: none;
    max-height: 120px;
    overflow-y: auto;
    -webkit-appearance: none;
  }

  #user-input::placeholder { color: rgba(255,255,255,0.3); }

  .send-btn {
    flex-shrink: 0;
    width: 44px; height: 44px;
    border-radius: 50%;
    background: var(--orange);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.15s, transform 0.12s, opacity 0.15s;
  }
  .send-btn:hover { background: #c96f18; }
  .send-btn:active { transform: scale(0.9); }
  .send-btn:disabled { opacity: 0.3; cursor: default; }
  .send-btn .material-symbols-outlined { font-size: 20px !important; }

  /* ── ERROR TOAST ── */
  .error-toast {
    display: none;
    align-items: center;
    gap: 8px;
    background: rgba(220, 60, 60, 0.12);
    border: 1px solid rgba(220, 60, 60, 0.3);
    border-radius: 10px;
    padding: 10px 14px;
    margin: 0 0 8px;
    font-size: 12.5px;
    color: #f08080;
    line-height: 1.45;
  }
  .error-toast.visible { display: flex; }
  .error-toast .material-symbols-outlined { font-size: 16px !important; flex-shrink: 0; }

  /* ── SCROLLBAR ── */
  #chat-scroll::-webkit-scrollbar { width: 4px; }
  #chat-scroll::-webkit-scrollbar-track { background: transparent; }
  #chat-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

  /* ══════════════════════════════════════════════════════════════════
     COUNTER MODE — curated rebuttal cards for difficult conversations.
     All classes prefixed .cc- so nothing collides with the chat UI.
     View switching is driven by a class on <body>, never inline styles,
     because #chat-scroll relies on display:flex.
     ══════════════════════════════════════════════════════════════════ */

  /* ── MODE SWITCH ── */
  .cc-modebar {
    flex-shrink: 0;
    display: flex;
    gap: 4px;
    padding: 8px 16px 10px;
    background: rgba(10,41,59,0.97);
    border-bottom: 1px solid rgba(255,255,255,0.07);
  }
  .cc-mode-btn {
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 9px 6px;
    border-radius: 100px;
    border: 1px solid transparent;
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.45);
    font-family: 'Montserrat', sans-serif;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.18s, color 0.18s, border-color 0.18s;
  }
  .cc-mode-btn .material-symbols-outlined { font-size: 15px !important; flex-shrink: 0; }
  .cc-mode-btn.active {
    background: rgba(226,126,34,0.16);
    border-color: rgba(226,126,34,0.4);
    color: var(--orange);
  }

  /* ── COUNTER VIEW SHELL ── */
  #counter-view {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 14px 16px calc(env(safe-area-inset-bottom, 0px) + 20px);
    display: none;
    flex-direction: column;
    gap: 12px;
  }
  #counter-view::-webkit-scrollbar { width: 4px; }
  #counter-view::-webkit-scrollbar-track { background: transparent; }
  #counter-view::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

  /* the composer lives inside #chat-scroll, so hiding that hides it too */
  body.mode-counter #chat-scroll   { display: none; }
  body.mode-counter #counter-view  { display: flex; }

  /* ── SEARCH ── */
  .cc-search-wrap {
    position: relative;
    display: flex;
    align-items: center;
    background: var(--surface-card);
    border: 1.5px solid rgba(255,255,255,0.1);
    border-radius: 100px;
    padding: 0 14px;
    transition: border-color 0.2s;
  }
  .cc-search-wrap:focus-within { border-color: rgba(226,126,34,0.45); }
  .cc-search-wrap .material-symbols-outlined {
    font-size: 18px !important;
    color: rgba(255,255,255,0.35);
    flex-shrink: 0;
  }
  #cc-search, #wk-search {
    flex: 1;
    min-width: 0;
    background: transparent;
    border: none;
    outline: none;
    padding: 10px;
    font-family: 'Montserrat', sans-serif;
    /* 16px exactly: iOS Safari zooms the whole page when you focus an input
       smaller than this, which made searching feel broken on the phone. */
    font-size: 16px;
    color: white;
    -webkit-appearance: none;
    appearance: none;
  }
  #cc-search::placeholder, #wk-search::placeholder { color: rgba(255,255,255,0.3); }
  /* Safari and Chrome both draw their own clear button on type=search */
  #cc-search::-webkit-search-decoration,
  #cc-search::-webkit-search-cancel-button,
  #wk-search::-webkit-search-decoration,
  #wk-search::-webkit-search-cancel-button { -webkit-appearance: none; appearance: none; }
  .cc-search-clear {
    background: none; border: none; padding: 4px;
    color: rgba(255,255,255,0.35);
    cursor: pointer; display: none;
    -webkit-tap-highlight-color: transparent;
  }
  .cc-search-clear.visible { display: block; }

  /* ── CATEGORY FILTER ── */
  .cc-cats {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 2px;
    margin: 0 -16px;
    padding-left: 16px;
    padding-right: 16px;
  }
  .cc-cats::-webkit-scrollbar { display: none; }
  .cc-cat {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 13px;
    border-radius: 100px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.09);
    color: rgba(255,255,255,0.5);
    font-family: 'Montserrat', sans-serif;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.15s, color 0.15s, border-color 0.15s;
  }
  .cc-cat .material-symbols-outlined { font-size: 14px !important; }
  .cc-cat.active {
    background: rgba(78,138,77,0.18);
    border-color: rgba(78,138,77,0.4);
    color: var(--green-light);
  }

  /* ── VERDICT PILL ── */
  .cc-verdict {
    display: inline-flex;
    align-items: center;
    align-self: flex-start;
    border-radius: 100px;
    padding: 3px 9px;
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    white-space: nowrap;
  }
  .cc-verdict.v-red    { background: rgba(220,60,60,0.15);  border: 1px solid rgba(220,60,60,0.32);  color: #f08080; }
  .cc-verdict.v-orange { background: rgba(226,126,34,0.14); border: 1px solid rgba(226,126,34,0.3);  color: #f0a55a; }
  .cc-verdict.v-yellow { background: rgba(247,184,1,0.1);   border: 1px solid rgba(247,184,1,0.28);  color: var(--yellow); }
  .cc-verdict.v-green  { background: rgba(78,138,77,0.18);  border: 1px solid rgba(78,138,77,0.34);  color: var(--green-light); }

  /* ── CLAIM CARD (list) ── */
  .cc-card {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
    text-align: left;
    background: var(--surface-card);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: var(--radius);
    padding: 14px 15px;
    cursor: pointer;
    font-family: 'Montserrat', sans-serif;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.15s, border-color 0.15s, transform 0.12s;
  }
  .cc-card:hover  { background: var(--surface-card-hi); border-color: rgba(255,255,255,0.14); }
  .cc-card:active { transform: scale(0.985); }
  .cc-card-claim {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.45;
    color: rgba(255,255,255,0.92);
  }
  .cc-card-claim::before { content: '\201C'; }
  .cc-card-claim::after  { content: '\201D'; }
  .cc-card-foot {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 10.5px;
    font-weight: 600;
    color: rgba(255,255,255,0.35);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .cc-card-foot .material-symbols-outlined { font-size: 13px !important; }

  .cc-count {
    font-size: 11px;
    color: rgba(255,255,255,0.3);
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }

  .cc-noresults {
    text-align: center;
    padding: 36px 20px;
    color: rgba(255,255,255,0.4);
    font-size: 13px;
    line-height: 1.6;
  }

  .cc-list-view { display: flex; flex-direction: column; gap: 12px; }
  #cc-results  { display: flex; flex-direction: column; gap: 10px; }

  /* ── DETAIL VIEW ── */
  body.cc-detail .cc-list-view { display: none; }
  .cc-detail-view { display: none; flex-direction: column; gap: 14px; }
  body.cc-detail .cc-detail-view { display: flex; }

  .cc-back {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    align-self: flex-start;
    background: none;
    border: none;
    padding: 2px 0;
    color: rgba(255,255,255,0.5);
    font-family: 'Montserrat', sans-serif;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
  }
  .cc-back:hover { color: white; }
  .cc-back .material-symbols-outlined { font-size: 17px !important; }

  .cc-detail-claim {
    font-family: 'Comfortaa', cursive;
    font-size: 18px;
    font-weight: 700;
    line-height: 1.4;
    color: white;
  }
  .cc-detail-claim::before { content: '\201C'; }
  .cc-detail-claim::after  { content: '\201D'; }

  .cc-block { display: flex; flex-direction: column; gap: 6px; }
  .cc-block-title {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--green-light);
    display: flex;
    align-items: center;
    gap: 5px;
  }
  .cc-block-title .material-symbols-outlined { font-size: 14px !important; }
  .cc-block-body {
    font-size: 13.5px;
    line-height: 1.65;
    color: rgba(255,255,255,0.78);
  }

  /* the quotable line */
  .cc-say {
    background: rgba(226,126,34,0.1);
    border: 1px solid rgba(226,126,34,0.3);
    border-radius: var(--radius);
    padding: 14px 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .cc-say .cc-block-title { color: var(--orange); }
  .cc-say-text {
    font-size: 15px;
    line-height: 1.55;
    font-weight: 500;
    color: white;
  }
  .cc-copy {
    align-self: flex-start;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(226,126,34,0.16);
    border: 1px solid rgba(226,126,34,0.32);
    border-radius: 100px;
    padding: 6px 13px;
    color: var(--orange);
    font-family: 'Montserrat', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.15s;
  }
  .cc-copy:hover  { background: rgba(226,126,34,0.26); }
  .cc-copy:active { transform: scale(0.95); }
  .cc-copy .material-symbols-outlined { font-size: 14px !important; }

  /* pushback pairs */
  .cc-push {
    background: var(--surface-card);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: var(--radius-sm);
    padding: 11px 13px;
    margin-bottom: 7px;
  }
  .cc-push:last-child { margin-bottom: 0; }
  .cc-push-if {
    font-size: 12.5px;
    font-weight: 600;
    color: rgba(255,255,255,0.5);
    font-style: italic;
    margin-bottom: 5px;
  }
  .cc-push-if::before { content: '\201C'; }
  .cc-push-if::after  { content: '\201D'; }
  .cc-push-reply {
    font-size: 13px;
    line-height: 1.6;
    color: rgba(255,255,255,0.85);
  }

  .cc-sources { display: flex; flex-direction: column; gap: 4px; }
  .cc-sources a {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--orange);
    text-decoration: none;
    padding: 3px 0;
    line-height: 1.4;
  }
  .cc-sources a:hover { color: #f0a55a; }
  .cc-sources a .material-symbols-outlined { font-size: 13px !important; flex-shrink: 0; }

  .cc-ask {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    width: 100%;
    background: rgba(78,138,77,0.14);
    border: 1px solid rgba(78,138,77,0.32);
    border-radius: var(--radius);
    padding: 13px;
    color: var(--green-light);
    font-family: 'Montserrat', sans-serif;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.15s;
  }
  .cc-ask:hover  { background: rgba(78,138,77,0.24); }
  .cc-ask:active { transform: scale(0.985); }
  .cc-ask .material-symbols-outlined { font-size: 17px !important; }

  /* ══════════════════════════════════════════════════════════════════
     WIKI MODE — browsable view of the same knowledge base the Companion
     reads. Deliberately reuses the Counter mode shell (.cc-search-wrap,
     .cc-cats, .cc-back, .cc-count) so the two feel like one section.
     ══════════════════════════════════════════════════════════════════ */

  #wiki-view {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 14px 16px calc(env(safe-area-inset-bottom, 0px) + 20px);
    display: none;
    flex-direction: column;
    gap: 12px;
  }
  #wiki-view::-webkit-scrollbar { width: 4px; }
  #wiki-view::-webkit-scrollbar-track { background: transparent; }
  #wiki-view::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

  body.mode-wiki #chat-scroll { display: none; }
  body.mode-wiki #wiki-view   { display: flex; }

  .wk-list-view { display: flex; flex-direction: column; gap: 12px; }
  #wk-results   { display: flex; flex-direction: column; gap: 10px; }
  body.wk-detail .wk-list-view { display: none; }
  body.wk-detail #wk-detail    { display: flex; }

  /* page card */
  .wk-card {
    display: flex;
    flex-direction: column;
    gap: 6px;
    width: 100%;
    text-align: left;
    background: var(--surface-card);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: var(--radius);
    padding: 13px 15px;
    cursor: pointer;
    font-family: 'Montserrat', sans-serif;
    -webkit-tap-highlight-color: transparent;
    transition: background 0.15s, border-color 0.15s, transform 0.12s;
  }
  .wk-card:hover  { background: var(--surface-card-hi); border-color: rgba(255,255,255,0.14); }
  .wk-card:active { transform: scale(0.985); }
  .wk-card-title {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
    color: rgba(255,255,255,0.92);
  }
  .wk-card-summary {
    font-size: 12px;
    line-height: 1.5;
    color: rgba(255,255,255,0.42);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .wk-card-foot {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    font-weight: 700;
    color: rgba(255,255,255,0.3);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .wk-card-foot .material-symbols-outlined { font-size: 12px !important; }

  /* detail header */
  .wk-title {
    font-family: 'Comfortaa', cursive;
    font-size: 20px;
    font-weight: 700;
    line-height: 1.35;
    color: white;
  }
  .wk-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
  }
  .wk-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border-radius: 100px;
    padding: 3px 9px;
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    background: rgba(78,138,77,0.16);
    border: 1px solid rgba(78,138,77,0.3);
    color: var(--green-light);
  }
  .wk-chip.tag {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.45);
    text-transform: none;
    letter-spacing: 0;
    font-weight: 600;
  }
  .wk-chip .material-symbols-outlined { font-size: 12px !important; }

  /* rendered markdown body */
  .wk-body {
    font-size: 14px;
    line-height: 1.7;
    color: rgba(255,255,255,0.8);
    word-break: break-word;
  }
  .wk-body h1, .wk-body h2, .wk-body h3, .wk-body h4 {
    font-family: 'Comfortaa', cursive;
    color: white;
    line-height: 1.35;
    margin: 22px 0 8px;
  }
  .wk-body h1 { font-size: 18px; }
  .wk-body h2 { font-size: 16.5px; }
  .wk-body h3 { font-size: 15px; color: var(--green-light); }
  .wk-body h4 { font-size: 13.5px; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 0.04em; }
  .wk-body > *:first-child { margin-top: 0; }
  .wk-body p  { margin-bottom: 12px; }
  .wk-body ul, .wk-body ol { padding-left: 20px; margin-bottom: 12px; }
  .wk-body li { margin-bottom: 6px; }
  .wk-body strong { color: white; font-weight: 700; }
  .wk-body em { color: rgba(255,255,255,0.65); }
  .wk-body hr { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0; }
  .wk-body code {
    background: rgba(255,255,255,0.08);
    border-radius: 4px;
    padding: 1px 5px;
    font-size: 12.5px;
    font-family: monospace;
  }
  .wk-body a {
    color: var(--orange);
    text-decoration: underline;
    text-decoration-color: rgba(226,126,34,0.45);
    text-underline-offset: 2px;
  }
  .wk-body a:hover { color: #f0a55a; }
  .wk-body mark {
    background: rgba(247,184,1,0.18);
    color: var(--yellow);
    border-radius: 3px;
    padding: 0 3px;
  }
  .wk-body blockquote {
    border-left: 3px solid rgba(78,138,77,0.5);
    background: rgba(78,138,77,0.06);
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    padding: 10px 14px;
    margin: 0 0 12px;
    color: rgba(255,255,255,0.72);
    font-size: 13.5px;
  }
  .wk-body blockquote p:last-child { margin-bottom: 0; }
  .wk-body .wk-callout {
    border-left: 3px solid var(--orange);
    background: rgba(226,126,34,0.08);
  }
  /* internal wiki link — resolves to another page in this browser */
  .wk-body .wk-link {
    color: var(--green-light);
    text-decoration-color: rgba(125,191,124,0.45);
    cursor: pointer;
  }
  .wk-body .wk-link:hover { color: white; }
  .wk-body .wk-link.dead {
    color: rgba(255,255,255,0.4);
    text-decoration: none;
    cursor: default;
  }
  /* tables scroll inside their own box so the page never scrolls sideways */
  .wk-table-wrap { overflow-x: auto; margin-bottom: 14px; -webkit-overflow-scrolling: touch; }
  .wk-body table {
    border-collapse: collapse;
    width: 100%;
    min-width: 380px;
    font-size: 12.5px;
  }
  .wk-body th, .wk-body td {
    border: 1px solid rgba(255,255,255,0.1);
    padding: 8px 10px;
    text-align: left;
    vertical-align: top;
    line-height: 1.5;
  }
  .wk-body th {
    background: rgba(255,255,255,0.05);
    color: white;
    font-weight: 700;
  }
  /* Obsidian image embeds point at an attachments store we cannot reach */
  .wk-embed {
    display: flex;
    align-items: center;
    gap: 7px;
    background: rgba(255,255,255,0.03);
    border: 1px dashed rgba(255,255,255,0.14);
    border-radius: var(--radius-sm);
    padding: 9px 12px;
    margin-bottom: 12px;
    font-size: 11.5px;
    color: rgba(255,255,255,0.32);
  }
  .wk-embed .material-symbols-outlined { font-size: 15px !important; }

  .wk-countrow {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
  }
  .wk-refresh {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: none;
    border: none;
    padding: 2px 0;
    color: rgba(255,255,255,0.32);
    font-family: 'Montserrat', sans-serif;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
  }
  .wk-refresh:hover { color: var(--green-light); }
  .wk-refresh .material-symbols-outlined { font-size: 14px !important; }
  .wk-refresh.spinning .material-symbols-outlined { animation: wk-spin 0.7s linear infinite; }
  @keyframes wk-spin { to { transform: rotate(360deg); } }

  .wk-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 40px 20px;
    color: rgba(255,255,255,0.4);
    font-size: 13px;
  }

  /* toast for copy confirmation */
  .cc-toast {
    position: fixed;
    left: 50%;
    bottom: calc(84px + env(safe-area-inset-bottom, 0px));
    transform: translateX(-50%) translateY(8px);
    background: var(--surface-bright);
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 100px;
    padding: 9px 18px;
    font-size: 12px;
    font-weight: 600;
    color: white;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s, transform 0.2s;
    z-index: 400;
  }
  .cc-toast.visible { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
</head>
<body class="chat-blank">

<!-- TOP BAR -->
<header class="top-bar">
  <a class="top-bar-brand" href="/">
    <img class="top-bar-logo" src="_images/Environmentle_logo1.png" width="32" height="32" alt="Environmentle logo">
    <span class="top-bar-title">Climate Companion</span>
  </a>
  <a class="profile-icon-btn" href="/?profile=1" aria-label="Profile">
    <span class="material-symbols-outlined">person</span>
  </a>
</header>

<!-- MODE SWITCH — Ask (chat) / Counter (rebuttal cards) / Wiki (browse the source) -->
<div class="cc-modebar">
  <button type="button" class="cc-mode-btn active" id="cc-mode-ask" onclick="setMode('ask')">
    <span class="material-symbols-outlined">forum</span>Ask
  </button>
  <button type="button" class="cc-mode-btn" id="cc-mode-wiki" onclick="setMode('wiki')">
    <span class="material-symbols-outlined">menu_book</span>Browse Wiki
  </button>
  <button type="button" class="cc-mode-btn" id="cc-mode-counter" onclick="setMode('counter')">
    <span class="material-symbols-outlined">shield</span>Counter Claims
  </button>
</div>

<!-- COUNTER MODE -->
<div id="counter-view">
  <div class="cc-list-view">
    <div class="cc-search-wrap">
      <span class="material-symbols-outlined">search</span>
      <input id="cc-search" type="search" autocomplete="off" autocorrect="off" spellcheck="false"
             placeholder="Someone told me…" aria-label="Search claims" oninput="ccFilter()">
      <button type="button" class="cc-search-clear" id="cc-search-clear" onclick="ccClearSearch()" aria-label="Clear search">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="cc-cats" id="cc-cats"></div>
    <div class="cc-count" id="cc-count"></div>
    <div id="cc-results"></div>
  </div>
  <div class="cc-detail-view" id="cc-detail"></div>
</div>

<!-- WIKI MODE — browsable view of the same knowledge base the Companion reads -->
<div id="wiki-view">
  <div class="wk-list-view">
    <div class="cc-search-wrap">
      <span class="material-symbols-outlined">search</span>
      <input id="wk-search" type="search" autocomplete="off" autocorrect="off" spellcheck="false"
             placeholder="Search the wiki…" aria-label="Search the wiki" oninput="wkFilter()">
      <button type="button" class="cc-search-clear" id="wk-search-clear" onclick="wkClearSearch()" aria-label="Clear search">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="cc-cats" id="wk-cats"></div>
    <div class="wk-countrow">
      <span class="cc-count" id="wk-count"></span>
      <button type="button" class="wk-refresh" onclick="wkRefresh(this)" aria-label="Refresh from the wiki">
        <span class="material-symbols-outlined">refresh</span>Refresh
      </button>
    </div>
    <div id="wk-results"></div>
  </div>
  <div class="cc-detail-view" id="wk-detail"></div>
</div>

<div class="cc-toast" id="cc-toast">Copied</div>

<!-- CHAT — the input lives inside the scroller so it can sit at the top when
     there is nothing to read, then drop below the latest answer once there is.
     Position is controlled by flex `order`, never by moving nodes around. -->
<div id="chat-scroll">

  <div id="chat-thread"></div>

  <div class="input-area">
    <div class="input-wrap">
      <textarea
        id="user-input"
        rows="1"
        placeholder="Ask about climate action…"
        aria-label="Your question"
        onkeydown="handleKey(event)"
        oninput="autoResize(this)"
      ></textarea>
    </div>
    <button class="send-btn" id="send-btn" onclick="sendMessage()" disabled aria-label="Send">
      <span class="material-symbols-outlined">send</span>
    </button>
  </div>

  <div class="chat-empty" id="chat-empty">
    <div class="chat-empty-icon">
      <span class="material-symbols-outlined">eco</span>
    </div>
    <div class="chat-empty-title">Ask me anything</div>
    <p class="chat-empty-sub">Climate action, sustainability, net zero, renewables — I'm here to help.</p>
    <div class="chat-empty-suggestions">
      <button class="suggestion-chip" onclick="sendSuggestion(this)">What is carbon neutrality and how is it different from net zero?</button>
      <button class="suggestion-chip" onclick="sendSuggestion(this)">What are the most effective actions individuals can take on climate?</button>
      <button class="suggestion-chip" onclick="sendSuggestion(this)">How does climate change affect biodiversity?</button>
    </div>
  </div>

</div>

<script>
// ── STATE ──
let isLoading = false;
let messageCount = 0;

// ── CHAT CACHE ──
// Persist the conversation across navigations for a short window so users
// don't lose context when they tap Home and come back. After CHAT_CACHE_TTL
// the cache is treated as stale and discarded.
const CHAT_CACHE_KEY = 'companion_chat_cache';
const CHAT_CACHE_TTL = 5 * 60 * 1000; // 5 minutes
let _messages = []; // {role: 'user'|'assistant', text, sourceType?, sourceUrl?, sourceLabel?}

function saveCache() {
  try {
    localStorage.setItem(CHAT_CACHE_KEY, JSON.stringify({ ts: Date.now(), messages: _messages }));
  } catch (e) { /* quota / private mode — fail silent */ }
}

function loadCache() {
  try {
    const raw = localStorage.getItem(CHAT_CACHE_KEY);
    if (!raw) return;
    const data = JSON.parse(raw);
    if (!data || !data.ts || !Array.isArray(data.messages) || !data.messages.length) return;
    if (Date.now() - data.ts > CHAT_CACHE_TTL) {
      localStorage.removeItem(CHAT_CACHE_KEY);
      return;
    }
    _messages = data.messages.slice();
    for (const m of _messages) {
      if (m.role === 'user')          appendUser(m.text);
      else if (m.role === 'assistant') appendAssistant(m.text, m.sourceType, m.sources, m.furtherLinks);
    }
    if (_messages.length) showResetRow();
  } catch (e) { /* malformed cache — ignore */ }
}

// ── TEXTAREA AUTO-RESIZE ──
function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
  document.getElementById('send-btn').disabled = el.value.trim() === '' || isLoading;
}

// ── KEYBOARD: ENTER sends, SHIFT+ENTER newline ──
function handleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    if (!document.getElementById('send-btn').disabled) sendMessage();
  }
}

// ── SUGGESTION CHIPS ──
function sendSuggestion(btn) {
  const text = btn.textContent.trim();
  document.getElementById('user-input').value = text;
  autoResize(document.getElementById('user-input'));
  sendMessage();
}

// ── EMPTY STATE TOGGLE ──
// Hide-not-remove so newChat() can bring it back without rebuilding the markup.
// The body class also drives where the composer sits: top while blank, below
// the thread once there is something to continue from.
function hideEmpty() {
  const el = document.getElementById('chat-empty');
  if (el) el.style.display = 'none';
  document.body.classList.remove('chat-blank');
}
function showEmpty() {
  const el = document.getElementById('chat-empty');
  if (el) el.style.display = '';
  document.body.classList.add('chat-blank');
}

// Reset button — appended at the very bottom of the chat thread once there
// is something to reset. appendChild moves the existing node to the end on
// every call, so it stays anchored below the latest reply.
function showResetRow() {
  const scroll = document.getElementById('chat-scroll');
  let row = document.getElementById('reset-row');
  if (!row) {
    row = document.createElement('div');
    row.id = 'reset-row';
    row.className = 'reset-row';
    row.innerHTML = '<button type="button" class="reset-btn" onclick="newChat()">' +
                      '<span class="material-symbols-outlined">refresh</span>' +
                      'Reset conversation' +
                    '</button>';
  }
  scroll.appendChild(row);
}
function hideResetRow() {
  const row = document.getElementById('reset-row');
  if (row) row.remove();
}

// Clear the current conversation and the persistence cache.
function newChat() {
  _messages = [];
  try { localStorage.removeItem(CHAT_CACHE_KEY); } catch (e) {}
  const thread = document.getElementById('chat-thread');
  Array.from(thread.querySelectorAll('.msg-row, .thinking-row')).forEach(el => el.remove());
  hideResetRow();
  showEmpty();
  const input = document.getElementById('user-input');
  if (input) { input.value = ''; input.style.height = 'auto'; input.focus(); }
  document.getElementById('send-btn').disabled = true;
}

// ── SCROLL TO BOTTOM ──
function scrollBottom() {
  const el = document.getElementById('chat-scroll');
  el.scrollTop = el.scrollHeight;
}

// ── APPEND USER MESSAGE ──
function appendUser(text) {
  hideEmpty();
  messageCount++;
  const row = document.createElement('div');
  row.className = 'msg-row user';
  const bubble = document.createElement('div');
  bubble.className = 'msg-bubble';
  bubble.textContent = text;
  row.appendChild(bubble);
  document.getElementById('chat-thread').appendChild(row);
  scrollBottom();
}

// ── APPEND THINKING INDICATOR ──
function appendThinking() {
  const row = document.createElement('div');
  row.className = 'thinking-row';
  row.id = 'thinking';
  const bubble = document.createElement('div');
  bubble.className = 'thinking-bubble';
  for (let i = 0; i < 3; i++) {
    const d = document.createElement('div');
    d.className = 'thinking-dot';
    bubble.appendChild(d);
  }
  row.appendChild(bubble);
  document.getElementById('chat-thread').appendChild(row);
  scrollBottom();
}

function removeThinking() {
  const el = document.getElementById('thinking');
  if (el) el.remove();
}

// ── MARKDOWN RENDERER ──
// Handles ###headings, **bold**, *italic*, `code`, [links](url),
// bullet lists, numbered lists, mixed lists, and paragraphs.
function renderMarkdown(text) {
  const blocks = text.replace(/\r\n/g, '\n').split(/\n{2,}/);
  let html = '';
  for (const block of blocks) {
    const lines = block.trim().split('\n');
    if (!lines.length || !block.trim()) continue;

    // Heading (### or ##)
    if (/^#{2,3}\s/.test(lines[0].trim())) {
      html += lines.map(l => '<h3>' + inlineMarkdown(l.replace(/^#{2,3}\s/, '').trim()) + '</h3>').join('');
      continue;
    }
    // Bullet list — allow mixed lines in a block
    if (lines.some(l => /^[\*\-\+]\s/.test(l.trim()))) {
      html += '<ul>' + lines.map(l => {
        const t = l.trim();
        return '<li>' + inlineMarkdown(t.replace(/^[\*\-\+]\s/, '').trim()) + '</li>';
      }).join('') + '</ul>';
      continue;
    }
    // Numbered list
    if (lines.every(l => /^\d+\.\s/.test(l.trim()))) {
      html += '<ol>' + lines.map(l => '<li>' + inlineMarkdown(l.replace(/^\d+\.\s/, '').trim()) + '</li>').join('') + '</ol>';
      continue;
    }
    // Paragraph
    html += '<p>' + lines.map(l => inlineMarkdown(l)).join('<br>') + '</p>';
  }
  return html;
}

function inlineMarkdown(text) {
  return text
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.+?)\*/g,     '<em>$1</em>')
    .replace(/`(.+?)`/g,       '<code>$1</code>')
    .replace(/\[([^\]]+)\]\((https?:\/\/[^)]+)\)/g, (match, text, url) => {
      // Skip internal wiki links
      if (url.includes('github.com/fmossiere-bot') ||
          url.includes('wiki.the-uptake.com') ||
          url.includes('raw.githubusercontent.com/fmossiere-bot')) {
        return text;
      }
      // Skip malformed URLs — a real domain must have a dot after ://
      const afterProtocol = url.replace(/^https?:\/\//, '');
      const domain = afterProtocol.split('/')[0];
      if (!domain.includes('.')) return text;
      // Skip placeholder/hallucinated domains
      const blockedDomains = ['example.com', 'example.org', 'placeholder.com', 'yourdomain.com'];
      if (blockedDomains.some(d => domain.includes(d))) return text;

      return `<a href="${url}" target="_blank" rel="noopener noreferrer">${text}</a>`;
    });
}

// ── APPEND AI ANSWER ──
function appendAssistant(text, sourceType, sources, furtherLinks) {
  const row = document.createElement('div');
  row.className = 'msg-row assistant';

  const bubble = document.createElement('div');
  bubble.className = 'msg-bubble';
  bubble.innerHTML = renderMarkdown(text);
  row.appendChild(bubble);

  // Source badges
  const badgesRow = document.createElement('div');
  badgesRow.className = 'msg-sources';

  if ((sourceType === 'wiki' || sourceType === 'hybrid') && sources && sources.length) {
    sources.forEach(src => {
      const badge = document.createElement('span');
      badge.className = 'msg-source wiki';
      const label = src.label.length > 40 ? src.label.substring(0, 38) + '…' : src.label;
      badge.innerHTML = '<span class="material-symbols-outlined">menu_book</span> ' + escapeHtml(label);
      badgesRow.appendChild(badge);
    });
    if (sourceType === 'hybrid') {
      const badge = document.createElement('span');
      badge.className = 'msg-source ai-supplemented';
      badge.innerHTML = '<span class="material-symbols-outlined">psychology</span> + AI knowledge';
      badgesRow.appendChild(badge);
    }
  } else {
    const badge = document.createElement('span');
    badge.className = 'msg-source ai';
    badge.innerHTML = '<span class="material-symbols-outlined">psychology</span> From AI knowledge';
    badgesRow.appendChild(badge);
  }
  row.appendChild(badgesRow);

  // Further reading links
  if (furtherLinks && furtherLinks.length) {
    const further = document.createElement('div');
    further.className = 'msg-further';
    further.innerHTML = '<div class="msg-further-title">Further reading</div>' +
      furtherLinks.map(lk =>
        `<a href="${escapeHtml(lk.url)}" target="_blank" rel="noopener noreferrer">` +
        `<span class="material-symbols-outlined">open_in_new</span>${escapeHtml(lk.text)}</a>`
      ).join('');
    row.appendChild(further);
  }

  document.getElementById('chat-thread').appendChild(row);
  scrollBottom();
}

// ── APPEND ERROR ──
function appendError(msg) {
  const row = document.createElement('div');
  row.className = 'msg-row assistant';
  const bubble = document.createElement('div');
  bubble.className = 'msg-bubble';
  bubble.style.background = 'rgba(220,60,60,0.12)';
  bubble.style.border = '1px solid rgba(220,60,60,0.25)';
  bubble.style.color = '#f08080';
  bubble.innerHTML = '<p>⚠ ' + escapeHtml(msg) + '</p>';
  row.appendChild(bubble);
  document.getElementById('chat-thread').appendChild(row);
  scrollBottom();
}

function escapeHtml(str) {
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── MAIN SEND ──
async function sendMessage() {
  const input = document.getElementById('user-input');
  const question = input.value.trim();
  if (!question || isLoading) return;

  isLoading = true;
  document.getElementById('send-btn').disabled = true;
  input.value = '';
  input.style.height = 'auto';

  appendUser(question);
  appendThinking();

  // Build the conversation to send: prior exchanges (so Claude can resolve "that",
  // "this", etc.) plus the new user question. The backend caps it server-side too.
  const history  = _messages.map(m => ({ role: m.role, content: m.text }));
  const messages = history.concat([{ role: 'user', content: question }]);

  try {
    const resp = await fetch('/api-proxy.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ messages })
    });

    const rawText = await resp.text();
    removeThinking();

    let data;
    try {
      data = JSON.parse(rawText);
    } catch (e) {
      appendError('Server error (HTTP ' + resp.status + '): ' + rawText.substring(0, 400));
      return;
    }

    if (!resp.ok || data.error) {
      appendError(data.error || 'Something went wrong. Please try again.');
    } else {
      if (data.debug) console.log('[Companion]', data.debug);
      appendAssistant(data.answer, data.source, data.sources, data.further_links);
      _messages.push({ role: 'user', text: question });
      _messages.push({
        role:         'assistant',
        text:         data.answer,
        sourceType:   data.source,
        sources:      data.sources      || [],
        furtherLinks: data.further_links || [],
      });
      saveCache();
      showResetRow();
    }
  } catch (err) {
    removeThinking();
    appendError('Network error: ' + err.message);
  } finally {
    isLoading = false;
    input.focus();
    document.getElementById('send-btn').disabled = input.value.trim() === '';
  }
}

// ── WIRE UP INPUT ENABLING ──
document.getElementById('user-input').addEventListener('input', function() {
  document.getElementById('send-btn').disabled = this.value.trim() === '' || isLoading;
});

// ── RESTORE RECENT CONVERSATION (within the 5-minute window) ──
loadCache();

/* ═══════════════════════════════════════════════════════════════════
   COUNTER MODE
   Curated rebuttal cards from claims.json. Phase 1 is entirely local:
   one fetch of a static file, no API call and no cost per view. The
   free-text "ask about any claim" path lands in phase 2.
   ═══════════════════════════════════════════════════════════════════ */

let _claims     = [];
let _categories = [];
let _verdicts   = {};
let _ccCat      = 'all';
let _ccListScroll = 0;   // where the claim list was before opening a card
let _ccLoaded   = false;
let _ccMode     = 'ask';

// Verdict colour keys map to .cc-verdict.v-* classes.
const CC_VERDICT_CLASS = { red: 'v-red', orange: 'v-orange', yellow: 'v-yellow', green: 'v-green' };

// Each mode keeps its own scroll position. Hiding an element resets scrollTop,
// so leaving and returning would otherwise dump you back at the top.
const MODE_VIEW   = { ask: 'chat-scroll', counter: 'counter-view', wiki: 'wiki-view' };
const _modeScroll = { ask: 0, counter: 0, wiki: 0 };

function setMode(mode) {
  const leaving = document.getElementById(MODE_VIEW[_ccMode]);
  if (leaving) _modeScroll[_ccMode] = leaving.scrollTop;

  _ccMode = mode;
  document.body.classList.toggle('mode-counter', mode === 'counter');
  document.body.classList.toggle('mode-wiki',    mode === 'wiki');
  document.getElementById('cc-mode-ask').classList.toggle('active',     mode === 'ask');
  document.getElementById('cc-mode-counter').classList.toggle('active', mode === 'counter');
  document.getElementById('cc-mode-wiki').classList.toggle('active',    mode === 'wiki');

  // The other modes' open detail is deliberately left open. Its markup is
  // scoped inside a hidden view, so it costs nothing, and coming back puts
  // you on the page you were reading rather than at the top of the list.

  if (mode === 'counter')   ccLoad();
  else if (mode === 'wiki') wkLoad();
  else {
    const input = document.getElementById('user-input');
    if (input) input.focus();
  }

  const entering = document.getElementById(MODE_VIEW[mode]);
  if (entering) requestAnimationFrame(() => { entering.scrollTop = _modeScroll[mode] || 0; });

  // Keep the URL honest so the mode survives a refresh or a shared link.
  try {
    const url = new URL(window.location.href);
    if (mode === 'ask') url.searchParams.delete('mode');
    else                url.searchParams.set('mode', mode);
    history.replaceState(null, '', url);
  } catch (e) { /* older browsers — cosmetic only */ }
}

async function ccLoad() {
  if (_ccLoaded) return;
  const results = document.getElementById('cc-results');
  results.innerHTML = '<div class="cc-noresults">Loading…</div>';
  try {
    // Cards come from the wiki now, generated from wiki/myths/ by
    // build_claims.py on every push. Nothing to upload when a myth changes.
    // The local copy stays as a fallback for when GitHub is unreachable.
    let data;
    try {
      const resp = await fetch(WIKI_RAW + 'claims.json', { cache: 'no-cache' });
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      data = await resp.json();
    } catch (wikiErr) {
      const resp = await fetch('claims.json', { cache: 'no-cache' });
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      data = await resp.json();
    }
    _claims     = Array.isArray(data.claims) ? data.claims : [];
    _categories = Array.isArray(data.categories) ? data.categories : [];
    _verdicts   = data.verdicts || {};
    _ccLoaded   = true;
    ccRenderCats();
    ccFilter();
  } catch (err) {
    results.innerHTML = '<div class="cc-noresults">Could not load the claim library.<br>' +
                        escapeHtml(err.message) + '</div>';
  }
}

function ccRenderCats() {
  const wrap = document.getElementById('cc-cats');
  const all  = '<button type="button" class="cc-cat active" data-cat="all" onclick="ccSetCat(this)">' +
               '<span class="material-symbols-outlined">apps</span>All</button>';
  wrap.innerHTML = all + _categories.map(c =>
    '<button type="button" class="cc-cat" data-cat="' + escapeHtml(c.id) + '" onclick="ccSetCat(this)">' +
    '<span class="material-symbols-outlined">' + escapeHtml(c.icon) + '</span>' +
    escapeHtml(c.label) + '</button>'
  ).join('');
}

function ccSetCat(btn) {
  _ccCat = btn.dataset.cat;
  Array.from(document.querySelectorAll('.cc-cat')).forEach(b => b.classList.toggle('active', b === btn));
  ccFilter();
}

function ccClearSearch() {
  const input = document.getElementById('cc-search');
  input.value = '';
  ccFilter();
  input.focus();
}

// Weighted the same way as the wiki search: the claim itself outranks the
// alternative phrasings, which outrank the body text.
function ccScore(c, terms, q) {
  const claim = (c.claim || '').toLowerCase();
  const heard = (c.heard_as || []).join(' ').toLowerCase();
  const rest  = ((c.say_this || '') + ' ' + (c.truth || '')).toLowerCase();
  let score = 0;
  for (const t of terms) {
    if      (claim.includes(t)) score += 100;
    else if (heard.includes(t)) score += 40;
    else if (rest.includes(t))  score += 1;
    else return -1;
  }
  if (claim.startsWith(q)) score += 200;
  else if (claim.includes(q)) score += 60;
  else if (heard.includes(q)) score += 30;
  return score;
}

function ccFilter() {
  const q     = document.getElementById('cc-search').value.trim().toLowerCase();
  const terms = q ? q.split(/\s+/) : [];
  document.getElementById('cc-search-clear').classList.toggle('visible', q !== '');

  let matches;
  if (!terms.length) {
    matches = _claims.filter(c => _ccCat === 'all' || c.category === _ccCat);
  } else {
    matches = _claims
      .filter(c => _ccCat === 'all' || c.category === _ccCat)
      .map(c => ({ c, s: ccScore(c, terms, q) }))
      .filter(x => x.s >= 0)
      .sort((a, b) => b.s - a.s || a.c.claim.localeCompare(b.c.claim))
      .map(x => x.c);
  }

  const count = document.getElementById('cc-count');
  count.textContent = matches.length
    ? matches.length + (matches.length === 1 ? ' claim' : ' claims')
    : '';

  const results = document.getElementById('cc-results');
  if (!matches.length) {
    results.innerHTML = '<div class="cc-noresults">No claim matches that yet.<br>' +
                        'Try the Ask tab and the Companion will take it on.</div>';
    return;
  }
  results.innerHTML = matches.map(ccCardHTML).join('');
}

function ccVerdictHTML(verdictId) {
  const v   = _verdicts[verdictId] || { label: verdictId, color: 'orange' };
  const cls = CC_VERDICT_CLASS[v.color] || 'v-orange';
  return '<span class="cc-verdict ' + cls + '">' + escapeHtml(v.label) + '</span>';
}

function ccCardHTML(c) {
  const cat = _categories.find(x => x.id === c.category);
  // The quote marks around the claim are CSS pseudo-elements, so screen readers
  // need the claim spelled out on the button itself.
  return '<button type="button" class="cc-card" aria-label="' + escapeHtml(c.claim) + '"' +
         ' onclick="ccOpen(\'' + escapeHtml(c.id) + '\')">' +
           ccVerdictHTML(c.verdict) +
           '<div class="cc-card-claim">' + escapeHtml(c.claim) + '</div>' +
           '<div class="cc-card-foot">' +
             '<span class="material-symbols-outlined">' + escapeHtml(cat ? cat.icon : 'label') + '</span>' +
             escapeHtml(cat ? cat.label : c.category) +
           '</div>' +
         '</button>';
}

function ccOpen(id) {
  const c = _claims.find(x => x.id === id);
  if (!c) return;

  const pushback = (c.pushback || []).map(p =>
    '<div class="cc-push">' +
      '<div class="cc-push-if">' + escapeHtml(p.if) + '</div>' +
      '<div class="cc-push-reply">' + escapeHtml(p.reply) + '</div>' +
    '</div>'
  ).join('');

  const sources = (c.sources || []).map(s =>
    '<a href="' + escapeHtml(s.url) + '" target="_blank" rel="noopener noreferrer">' +
      '<span class="material-symbols-outlined">open_in_new</span>' + escapeHtml(s.label) +
    '</a>'
  ).join('');

  document.getElementById('cc-detail').innerHTML =
    '<button type="button" class="cc-back" onclick="ccCloseDetail()">' +
      '<span class="material-symbols-outlined">arrow_back</span>All claims</button>' +
    ccVerdictHTML(c.verdict) +
    '<div class="cc-detail-claim">' + escapeHtml(c.claim) + '</div>' +

    '<div class="cc-block">' +
      '<div class="cc-block-title"><span class="material-symbols-outlined">lightbulb</span>Why it sounds right</div>' +
      '<div class="cc-block-body">' + escapeHtml(c.kernel) + '</div>' +
    '</div>' +

    '<div class="cc-block">' +
      '<div class="cc-block-title"><span class="material-symbols-outlined">fact_check</span>What is actually true</div>' +
      '<div class="cc-block-body">' + escapeHtml(c.truth) + '</div>' +
    '</div>' +

    '<div class="cc-say">' +
      '<div class="cc-block-title"><span class="material-symbols-outlined">record_voice_over</span>Say this</div>' +
      '<div class="cc-say-text" id="cc-say-text">' + escapeHtml(c.say_this) + '</div>' +
      '<button type="button" class="cc-copy" onclick="ccCopy(this)">' +
        '<span class="material-symbols-outlined">content_copy</span>Copy</button>' +
    '</div>' +

    (pushback
      ? '<div class="cc-block">' +
          '<div class="cc-block-title"><span class="material-symbols-outlined">reply</span>If they push back</div>' +
          '<div>' + pushback + '</div>' +
        '</div>'
      : '') +

    (sources
      ? '<div class="cc-block">' +
          '<div class="cc-block-title"><span class="material-symbols-outlined">menu_book</span>Sources</div>' +
          '<div class="cc-sources">' + sources + '</div>' +
        '</div>'
      : '') +

    // No link through to the wiki on purpose. The source links above cover
    // "where did this come from", and Ask the Companion covers "tell me more".
    // A third route to the same material was clutter.
    '<button type="button" class="cc-ask" onclick="ccAskCompanion(' + JSON.stringify(c.claim).replace(/"/g, '&quot;') + ')">' +
      '<span class="material-symbols-outlined">forum</span>Ask the Companion about this</button>';

  const view = document.getElementById('counter-view');
  // Remember where the list was so Back returns you to the same card.
  if (!document.body.classList.contains('cc-detail')) _ccListScroll = view.scrollTop;
  document.body.classList.add('cc-detail');
  view.scrollTop = 0;
}

function ccCloseDetail() {
  document.body.classList.remove('cc-detail');
  const view = document.getElementById('counter-view');
  requestAnimationFrame(() => { view.scrollTop = _ccListScroll; });
}

function ccCopy(btn) {
  const text = document.getElementById('cc-say-text').textContent;
  const done = () => {
    const toast = document.getElementById('cc-toast');
    toast.classList.add('visible');
    setTimeout(() => toast.classList.remove('visible'), 1400);
  };
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(done).catch(() => ccCopyFallback(text, done));
  } else {
    ccCopyFallback(text, done);
  }
}

// Safari on older iOS refuses navigator.clipboard outside a secure context.
function ccCopyFallback(text, done) {
  const ta = document.createElement('textarea');
  ta.value = text;
  ta.style.position = 'fixed';
  ta.style.opacity = '0';
  document.body.appendChild(ta);
  ta.select();
  try { document.execCommand('copy'); done(); } catch (e) { /* give up quietly */ }
  ta.remove();
}

// Bridge into the chat: hand the claim to the Companion as a question.
// Starts a clean thread, so the claim is not appended to whatever unrelated
// conversation happened to be sitting in the Ask tab.
function ccAskCompanion(claim) {
  setMode('ask');
  newChat();
  const input = document.getElementById('user-input');
  input.value = 'Someone told me: "' + claim + '". How should I respond?';
  autoResize(input);
  sendMessage();
}

/* ═══════════════════════════════════════════════════════════════════
   WIKI MODE
   Browses the same knowledge base api-proxy.php retrieves from. Pages
   are fetched straight from raw.githubusercontent.com, which serves
   access-control-allow-origin:*, so no proxy is needed and nothing has
   to be redeployed when the wiki changes. The index (wiki-snippets.json)
   is regenerated by the repo's GitHub Action on every push, and GitHub's
   raw CDN caches for 5 minutes, so WK_INDEX_TTL matches that. The
   refresh button forces a fresh pull when you have just pushed.
   ═══════════════════════════════════════════════════════════════════ */

const WIKI_RAW      = 'https://raw.githubusercontent.com/fmossiere-bot/climate-action-wiki/main/wiki/';
const WK_INDEX_KEY  = 'wiki_index_cache';
const WK_INDEX_TTL  = 5 * 60 * 1000;

// The `category` field in wiki-snippets.json has gaps and two spellings of
// a couple of folders, so the top-level folder is used as the source of
// truth instead. It is always present and always consistent.
const WK_FOLDERS = {
  'concepts':           { label: 'Concepts',     icon: 'lightbulb' },
  'solutions':          { label: 'Solutions',    icon: 'construction' },
  'sectors':            { label: 'Sectors',      icon: 'factory' },
  'sources':            { label: 'Sources',      icon: 'article' },
  'myths':              { label: 'Myths',        icon: 'fact_check' },
  'climate-science':    { label: 'Science',      icon: 'science' },
  'ireland-hub':        { label: 'Ireland',      icon: 'flag' },
  'standards-labels':   { label: 'Standards',    icon: 'verified' },
  'circularity-waste':  { label: 'Circularity',  icon: 'recycling' },
  'biodiversity-land':  { label: 'Biodiversity', icon: 'forest' },
  'legislation-policy': { label: 'Policy',       icon: 'gavel' },
  'climate-finance':    { label: 'Finance',      icon: 'payments' },
  'climate-adaptation': { label: 'Adaptation',   icon: 'device_thermostat' },
  '_inbox':             { label: 'Inbox',        icon: 'inbox' },
};

// A folder the map has never heard of gets a readable label derived from its
// name. The old behaviour filed it under "Inbox", which is how 26 myth pages
// ended up mislabelled the first time a new folder appeared in the wiki.
function wkFolderMeta(folder) {
  if (WK_FOLDERS[folder]) return WK_FOLDERS[folder];
  return {
    label: folder.replace(/^_+/, '').replace(/[-_]+/g, ' ')
                 .replace(/\b\w/g, ch => ch.toUpperCase()) || 'Other',
    icon: 'folder',
  };
}

let _wkPages   = [];
let _wkByKey   = {};   // basename / label / slug (all lowercased) -> slug, for [[wikilinks]]
let _wkBySlug  = {};
let _wkCats    = [];
let _wkCat     = 'all';
let _wkListScroll = 0;
let _wkLoaded  = false;
const _wkBodyCache = new Map();

function wkFolderOf(page) {
  return (page.filename || '').split('/')[0] || 'other';
}

async function wkLoad(force) {
  if (_wkLoaded && !force) return;
  const results = document.getElementById('wk-results');
  results.innerHTML = '<div class="wk-loading">Loading the wiki…</div>';

  let raw = null;
  if (!force) {
    try {
      const cached = JSON.parse(sessionStorage.getItem(WK_INDEX_KEY) || 'null');
      if (cached && Date.now() - cached.ts < WK_INDEX_TTL) raw = cached.pages;
    } catch (e) { /* ignore a bad cache */ }
  }

  if (!raw) {
    try {
      const resp = await fetch(WIKI_RAW + 'wiki-snippets.json', { cache: force ? 'reload' : 'default' });
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      raw = await resp.json();
      try {
        sessionStorage.setItem(WK_INDEX_KEY, JSON.stringify({ ts: Date.now(), pages: raw }));
      } catch (e) { /* quota — the in-memory copy still works */ }
    } catch (err) {
      results.innerHTML = '<div class="cc-noresults">Could not reach the wiki.<br>' +
                          escapeHtml(err.message) + '</div>';
      return;
    }
  }

  const all = Array.isArray(raw) ? raw : [];

  // `sources/` holds one summary per ingested article. They are raw material
  // for the Companion's retrieval, not pages worth browsing, so they stay out
  // of the list and out of search. They remain fully openable, because pages
  // elsewhere in the wiki link to them with [[wikilinks]] and a claim card can
  // point at one. Filtering them from the list is a browsing decision, not a
  // reason to make existing links dead.
  _wkPages = all
    .filter(p => (p.filename || '').split('/')[0] !== 'sources')
    .sort((a, b) => (a.label || '').localeCompare(b.label || ''));

  // Resolution table covers EVERY page, sources included.
  _wkByKey  = {};
  _wkBySlug = {};
  all.forEach(p => {
    _wkBySlug[p.slug] = p;
    const base = (p.filename || '').split('/').pop().replace(/\.md$/i, '');
    [base, p.label, p.slug].forEach(k => {
      if (k) _wkByKey[k.toLowerCase().trim()] = p.slug;
    });
  });

  // Only offer categories that actually contain pages, biggest first.
  const counts = {};
  _wkPages.forEach(p => { const f = wkFolderOf(p); counts[f] = (counts[f] || 0) + 1; });
  _wkCats = Object.keys(counts)
    .sort((a, b) => counts[b] - counts[a])
    .map(f => ({ id: f, count: counts[f], ...wkFolderMeta(f) }));

  _wkLoaded = true;
  wkRenderCats();
  wkFilter();
}

// Forces a fresh pull, for right after you have pushed to the wiki repo.
async function wkRefresh(btn) {
  btn.classList.add('spinning');
  _wkBodyCache.clear();
  try { sessionStorage.removeItem(WK_INDEX_KEY); } catch (e) {}
  await wkLoad(true);
  btn.classList.remove('spinning');
  const toast = document.getElementById('cc-toast');
  toast.textContent = 'Wiki refreshed';
  toast.classList.add('visible');
  setTimeout(() => { toast.classList.remove('visible'); toast.textContent = 'Copied'; }, 1400);
}

function wkRenderCats() {
  document.getElementById('wk-cats').innerHTML =
    '<button type="button" class="cc-cat active" data-cat="all" onclick="wkSetCat(this)">' +
      '<span class="material-symbols-outlined">apps</span>All ' + _wkPages.length + '</button>' +
    _wkCats.map(c =>
      '<button type="button" class="cc-cat" data-cat="' + escapeHtml(c.id) + '" onclick="wkSetCat(this)">' +
      '<span class="material-symbols-outlined">' + escapeHtml(c.icon) + '</span>' +
      escapeHtml(c.label) + ' ' + c.count + '</button>'
    ).join('');
}

function wkSetCat(btn) {
  _wkCat = btn.dataset.cat;
  Array.from(document.querySelectorAll('#wk-cats .cc-cat')).forEach(b => b.classList.toggle('active', b === btn));
  wkFilter();
}

function wkClearSearch() {
  const input = document.getElementById('wk-search');
  input.value = '';
  wkFilter();
  input.focus();
}

// Summaries are 400-character keyword dumps, so matching them equally with the
// title buried the page you were actually looking for. Fields are weighted
// instead, and a title hit outranks everything else.
function wkScore(p, terms, q) {
  const title   = (p.label   || '').toLowerCase();
  const slug    = (p.slug    || '').toLowerCase();
  const tags    = (p.tags    || []).join(' ').toLowerCase();
  const summary = (p.summary || '').toLowerCase();
  let score = 0;
  for (const t of terms) {
    if      (title.includes(t))   score += 100;
    else if (slug.includes(t))    score += 40;
    else if (tags.includes(t))    score += 20;
    else if (summary.includes(t)) score += 1;
    else return -1;               // every term has to land somewhere
  }
  if (title.startsWith(q)) score += 200;   // exact opening beats a mid-word hit
  else if (title.includes(q)) score += 60; // whole phrase in the title
  return score;
}

function wkFilter() {
  const q     = document.getElementById('wk-search').value.trim().toLowerCase();
  const terms = q ? q.split(/\s+/) : [];
  document.getElementById('wk-search-clear').classList.toggle('visible', q !== '');

  let matches;
  if (!terms.length) {
    matches = _wkPages.filter(p => _wkCat === 'all' || wkFolderOf(p) === _wkCat);
  } else {
    matches = _wkPages
      .filter(p => _wkCat === 'all' || wkFolderOf(p) === _wkCat)
      .map(p => ({ p, s: wkScore(p, terms, q) }))
      .filter(x => x.s >= 0)
      .sort((a, b) => b.s - a.s || a.p.label.localeCompare(b.p.label))
      .map(x => x.p);
  }

  document.getElementById('wk-count').textContent =
    matches.length ? matches.length + (matches.length === 1 ? ' page' : ' pages') : '';

  const results = document.getElementById('wk-results');
  if (!matches.length) {
    results.innerHTML = '<div class="cc-noresults">No page matches that.<br>' +
                        'Try the Ask tab and the Companion will search it for you.</div>';
    return;
  }
  // Long lists stay responsive by capping the render; searching narrows it.
  const shown = matches.slice(0, 80);
  results.innerHTML = shown.map(wkCardHTML).join('') +
    (matches.length > shown.length
      ? '<div class="cc-noresults">' + (matches.length - shown.length) +
        ' more. Refine your search to see them.</div>'
      : '');
}

function wkCardHTML(p) {
  const folder = wkFolderMeta(wkFolderOf(p));
  // Summaries are auto-generated keyword strings. They run to 3,000 chars on
  // some pages, and on others they are leftover YAML punctuation like "|-".
  let summary = (p.summary || '').trim().replace(/\s+/g, ' ');
  if (summary.replace(/[^a-z0-9]/gi, '').length < 4) summary = '';
  summary = summary.slice(0, 180);
  return '<button type="button" class="wk-card" aria-label="' + escapeHtml(p.label) + '"' +
         ' onclick="wkOpen(\'' + escapeHtml(p.slug) + '\')">' +
           '<div class="wk-card-title">' + escapeHtml(p.label) + '</div>' +
           (summary ? '<div class="wk-card-summary">' + escapeHtml(summary) + '</div>' : '') +
           '<div class="wk-card-foot">' +
             '<span class="material-symbols-outlined">' + escapeHtml(folder.icon) + '</span>' +
             escapeHtml(folder.label) +
           '</div>' +
         '</button>';
}

// ── PAGE VIEW ──
async function wkOpen(slug) {
  const page = _wkBySlug[slug];
  if (!page) return;
  const folder = wkFolderMeta(wkFolderOf(page));
  const detail = document.getElementById('wk-detail');

  // `updated` only exists once the page's frontmatter has been parsed, so the
  // header is rebuilt rather than patched. Patching it with a string replace
  // put the chip inside .wk-title, since that is the first </div> in here.
  const header = updated =>
    '<button type="button" class="cc-back" onclick="wkCloseDetail()">' +
      '<span class="material-symbols-outlined">arrow_back</span>All pages</button>' +
    '<div class="wk-title">' + escapeHtml(page.label) + '</div>' +
    '<div class="wk-meta">' +
      '<span class="wk-chip"><span class="material-symbols-outlined">' + escapeHtml(folder.icon) + '</span>' +
        escapeHtml(folder.label) + '</span>' +
      (updated ? '<span class="wk-chip tag">updated ' + escapeHtml(updated) + '</span>' : '') +
      (page.tags || []).slice(0, 6).map(t =>
        '<span class="wk-chip tag">#' + escapeHtml(t) + '</span>').join('') +
    '</div>';

  const view = document.getElementById('wiki-view');
  if (!document.body.classList.contains('wk-detail')) _wkListScroll = view.scrollTop;
  detail.innerHTML = header('') + '<div class="wk-loading">Loading page…</div>';
  document.body.classList.add('wk-detail');
  view.scrollTop = 0;

  let md = _wkBodyCache.get(slug);
  if (md === undefined) {
    try {
      const url  = WIKI_RAW + page.filename.split('/').map(encodeURIComponent).join('/');
      const resp = await fetch(url);
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      md = await resp.text();
      _wkBodyCache.set(slug, md);
    } catch (err) {
      detail.innerHTML = header('') +
        '<div class="cc-noresults">Could not load this page.<br>' + escapeHtml(err.message) + '</div>';
      return;
    }
  }

  // A different page may have been opened while this one was in flight.
  if (!document.body.classList.contains('wk-detail')) return;

  const parsed = wkStripFrontmatter(md);

  // House style repeats the title as an H1 at the top of the body. The detail
  // header already shows it, so drop that first H1 to avoid printing the title
  // twice. Later H1s, if any, are left alone.
  const body = parsed.body.replace(/^\s*#\s+.*(\n|$)/, '');

  // The page ends at its own Sources list. No "Open the source file" link to
  // GitHub, and no "Ask the Companion": the Companion answers from this very
  // page, so it would only read the same thing back to you.
  detail.innerHTML = header(parsed.meta.updated || '') +
    '<div class="wk-body">' + wkRender(body) + '</div>';
}

function wkCloseDetail() {
  document.body.classList.remove('wk-detail');
  const view = document.getElementById('wiki-view');
  requestAnimationFrame(() => { view.scrollTop = _wkListScroll; });
}

// ── MARKDOWN ──
// Obsidian-flavoured: YAML frontmatter, [[wikilinks]], ![[embeds]],
// ==highlights==, > [!NOTE] callouts, and pipe tables.

function wkStripFrontmatter(md) {
  const meta = {};
  let body = md.replace(/\r\n/g, '\n');
  // HTML comments are authoring notes. The renderer escapes markup, so without
  // this they would display as literal <!-- ... --> text on the page.
  body = body.replace(/<!--[\s\S]*?-->/g, '');
  const m = body.match(/^---\n([\s\S]*?)\n---\n?/);
  if (m) {
    m[1].split('\n').forEach(line => {
      const kv = line.match(/^([a-z_]+):\s*(.*)$/i);
      if (kv) meta[kv[1].toLowerCase()] = kv[2].trim().replace(/^["']|["']$/g, '');
    });
    body = body.slice(m[0].length);
  }
  return { meta, body };
}

function wkInline(text) {
  let t = escapeHtml(text);

  // ![[image.png]] — Obsidian attachments live outside the repo path
  t = t.replace(/!\[\[([^\]|]+)(\|[^\]]*)?\]\]/g,
    () => '<span class="wk-embed"><span class="material-symbols-outlined">image</span>Image (not synced)</span>');

  // [[Page]] / [[Page|label]] / [[Page#Heading]]
  t = t.replace(/\[\[([^\]]+)\]\]/g, (match, inner) => {
    const parts   = inner.split('|');
    const target  = parts[0].split('#')[0].trim();
    const display = (parts[1] || parts[0].replace('#', ' ')).trim();
    const slug    = _wkByKey[target.toLowerCase()];
    return slug
      ? '<a class="wk-link" onclick="wkOpen(\'' + slug + '\')">' + display + '</a>'
      : '<span class="wk-link dead">' + display + '</span>';
  });

  // Markdown images become a caption rather than a broken box
  t = t.replace(/!\[([^\]]*)\]\((https?:[^)\s]+)[^)]*\)/g,
    (m, alt) => '<span class="wk-embed"><span class="material-symbols-outlined">image</span>' + (alt || 'Image') + '</span>');

  t = t.replace(/\[([^\]]+)\]\((https?:\/\/[^)\s]+)[^)]*\)/g,
    (m, label, url) => '<a href="' + url + '" target="_blank" rel="noopener noreferrer">' + label + '</a>');

  // Bare URLs left on their own
  t = t.replace(/(^|[\s(])(https?:\/\/[^\s<)]+)/g,
    (m, pre, url) => pre + '<a href="' + url + '" target="_blank" rel="noopener noreferrer">' + url.replace(/^https?:\/\//, '').slice(0, 48) + '</a>');

  t = t.replace(/==(.+?)==/g,     '<mark>$1</mark>');
  t = t.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
  t = t.replace(/(^|[^*])\*([^*\n]+)\*/g, '$1<em>$2</em>');
  t = t.replace(/`([^`]+)`/g,     '<code>$1</code>');
  return t;
}

function wkRender(md) {
  const lines = md.split('\n');
  let html = '', i = 0;

  const isTableRow = l => /^\s*\|.*\|\s*$/.test(l);
  const cells = l => l.trim().replace(/^\||\|$/g, '').split('|').map(c => c.trim());

  while (i < lines.length) {
    const line = lines[i];
    const t    = line.trim();

    if (!t) { i++; continue; }

    // fenced code
    if (/^```/.test(t)) {
      const buf = [];
      i++;
      while (i < lines.length && !/^```/.test(lines[i].trim())) buf.push(lines[i++]);
      i++;
      html += '<pre><code>' + escapeHtml(buf.join('\n')) + '</code></pre>';
      continue;
    }

    // horizontal rule
    if (/^(-{3,}|\*{3,}|_{3,})$/.test(t)) { html += '<hr>'; i++; continue; }

    // heading
    const h = t.match(/^(#{1,6})\s+(.*)$/);
    if (h) {
      // A heading with nothing beneath it is an unfilled section, e.g. a
      // "Go deeper" the author has not written yet. Drop it rather than
      // showing a bare title with a gap under it.
      let j = i + 1, hasContent = false;
      while (j < lines.length) {
        const lt = lines[j].trim();
        if (/^#{1,6}\s/.test(lt)) break;
        if (lt) { hasContent = true; break; }
        j++;
      }
      if (!hasContent) { i++; continue; }

      const level = Math.min(h[1].length, 4);
      html += '<h' + level + '>' + wkInline(h[2].trim()) + '</h' + level + '>';
      i++;
      continue;
    }

    // table: header row, separator, body rows
    if (isTableRow(line) && i + 1 < lines.length && /^\s*\|[\s:|-]+\|\s*$/.test(lines[i + 1])) {
      const head = cells(lines[i]);
      i += 2;
      const body = [];
      while (i < lines.length && isTableRow(lines[i])) body.push(cells(lines[i++]));
      html += '<div class="wk-table-wrap"><table><thead><tr>' +
              head.map(c => '<th>' + wkInline(c) + '</th>').join('') +
              '</tr></thead><tbody>' +
              body.map(r => '<tr>' + r.map(c => '<td>' + wkInline(c) + '</td>').join('') + '</tr>').join('') +
              '</tbody></table></div>';
      continue;
    }

    // blockquote, including > [!NOTE] callouts
    if (/^>/.test(t)) {
      const buf = [];
      let callout = false;
      while (i < lines.length && /^\s*>/.test(lines[i])) {
        let inner = lines[i].replace(/^\s*>\s?/, '');
        const cm  = inner.match(/^\s*\[!(\w+)\]\s*(.*)$/);
        if (cm) { callout = true; inner = cm[2]; }
        buf.push(inner);
        i++;
      }
      const inner = wkRender(buf.join('\n'));
      html += '<blockquote' + (callout ? ' class="wk-callout"' : '') + '>' + inner + '</blockquote>';
      continue;
    }

    // lists — a run of bullet or numbered items, task boxes included
    if (/^[-*+]\s/.test(t) || /^\d+\.\s/.test(t)) {
      const ordered = /^\d+\.\s/.test(t);
      const tag = ordered ? 'ol' : 'ul';
      let items = '';
      while (i < lines.length) {
        const lt = lines[i].trim();
        if (ordered ? !/^\d+\.\s/.test(lt) : !/^[-*+]\s/.test(lt)) break;
        let item = lt.replace(ordered ? /^\d+\.\s+/ : /^[-*+]\s+/, '');
        item = item.replace(/^\[([ xX])\]\s*/, (m, c) =>
          (c.toLowerCase() === 'x' ? '☑ ' : '☐ '));
        items += '<li>' + wkInline(item) + '</li>';
        i++;
      }
      html += '<' + tag + '>' + items + '</' + tag + '>';
      continue;
    }

    // paragraph — consume until a blank line or the start of another block
    const para = [];
    while (i < lines.length) {
      const lt = lines[i].trim();
      if (!lt || /^(#{1,6}\s|>|```|[-*+]\s|\d+\.\s)/.test(lt) || isTableRow(lines[i]) ||
          /^(-{3,}|\*{3,}|_{3,})$/.test(lt)) break;
      para.push(lt);
      i++;
    }
    if (para.length) html += '<p>' + wkInline(para.join(' ')) + '</p>';
  }
  return html;
}

// ── DEEP LINK: companion.php?mode=counter | ?mode=wiki ──
(function () {
  const mode = new URLSearchParams(window.location.search).get('mode');
  if (mode === 'counter' || mode === 'wiki') setMode(mode);
})();
</script>

<!-- BOTTOM NAV — mirrors the homepage. Companion tab is active here. -->
<nav id="app-bottom-nav">
  <a class="nav-tab" href="/">
    <span class="material-symbols-outlined">home</span>
    <span class="nav-tab-label">Home</span>
  </a>
  <a class="nav-tab" href="/?tab=games">
    <span class="material-symbols-outlined">sports_esports</span>
    <span class="nav-tab-label">Games</span>
  </a>
  <a class="nav-tab" href="/?tab=learn">
    <span class="material-symbols-outlined">auto_stories</span>
    <span class="nav-tab-label">Stories</span>
  </a>
  <a class="nav-tab active" href="companion.php">
    <span class="material-symbols-outlined">search</span>
    <span class="nav-tab-label">Companion</span>
  </a>
</nav>
</body>
</html>

# Environmentle text inventory (for the Envie voice rewrite)

Rebuilt 2026-09-13 after the home engine, actions repository, credits and tour landed. Step 1 of `envie-voice-and-text-rewrite-guide.md`. Nothing rewritten yet. Companion (companion.php, api-proxy.php, claims.json) is out of scope for this pass.

## Where the words live

| Area | Files | Size | Voice today |
|---|---|---|---|
| Hub: name screen, home (lead cards, three-way strip, credit bar, community), tour, Games tab, Stories tab, story player, actions screen, action sheet, credit screen, profile, FAQ, push | index.html, home.js, engine.js, tour.js, app.js, envie.js, sw.js, manifest.json | ~250 live strings + FAQ (7 questions, 32 paragraphs), 17 dead | Mixed. Tour, action bubbles, credit hero and toasts already Envie. FAQ and lead cards speak as "we" or about Envie in third person |
| Daily Quiz | environmentle-quiz-game.html | 61 strings | Intro bubble, feedback toast and score screen Envie-ish; rest neutral |
| Sort It Out | environmentle-sort-it-out.html | 56 strings | Intro and score Envie-ish; play and feedback have no Envie |
| Water Challenge | environmentle-water-challenge.html | ~103 live, 13 dead | Closest to Envie already, dry and light |
| Actions repository | actions.json (255 actions: title + desc, impact line) | title 2.5k w, desc 5.7k w, 50 em-dashes | Instructional, neutral |
| Partners | partners.json (3 placeholder partners) | small | Already plain, one Envie mention |
| Challenge content | challenges.json (120 challenges, 360 questions) | intro_fact 5.5k w, question facts 10.7k w, questions/options ~11k w, 242 em-dashes. action_intro + actions (10.7k w) still mapped by the quiz but no longer shown | Encyclopaedic "we" voice |
| Carbon cards | cards.csv (44 rows: comparison + comment) | ~1k w, 22 em-dashes | Factual one-liners |
| Water cards | water-cards.json (69 cards, explainer, comparisons) | ~1.3k w | Plain, close |
| Stories | courses/*.json (5 stories, 64 slides, 11 Envie lines) | ~2.1k w, 10 em-dashes | Short, punchy, closest to target |

## Proposed batches (each presented for review before the next)

1. **Hub shell**: name screen, home lead cards and strip, Games/Stories tabs, story player chrome, profile, prompts, push, plus a naming pass so each game and each story has one name everywhere.
2. **Actions and credits**: actions screen, action sheet, credit screen, tour, FAQ (rewrite as Envie speaking, fix the email contradiction).
3. **Game screens**: intro, play, feedback, score, played gate, errors for all three games, one voice across them.
4. **Stories**: five course JSONs, slide text plus Envie lines where a slide earns one.
5. **actions.json**: 255 titles and descriptions, impact lines, plus partners.json.
6. **challenges.json part A**: intro_fact (120). Part B: question facts (360). Questions and options left alone unless unclear. Old action fields left as they are or removed, to decide.
7. **Card comments**: cards.csv and water-cards.json comments, explainer, comparisons.

## Found on the way (not tone, needs a decision or a fix)

- Sort It Out is broken: `startGame()` calls `hasPlayedToday()` and `saveProgress()` calls `markPlayedToday()`, `getStreakBonus()` and `_nk`, none defined in that file (they live in the quiz and water files). Start sorting throws.
- Quiz "Avg today: 140 pts" is hardcoded and shown to everyone. Quiz top tier still says "Now let's turn that knowledge into action".
- Water caveat still refers to "the list above" of actions that moved away; `water-cards.json` filename appears in player copy.
- Raw error text reaches players (HTTP codes, "Inline dataset missing", "Open the app through a web server, not as a file").
- Same game named up to four ways (Random challenge / Random quiz / Quiz; Sort it out / Carbon challenge / Sorting game; Which one drinks more? / Water challenge / Water game). Story titles Title Case in one list and sentence case in another; slide counts disagree with the JSON.
- Story metadata lives in three places (Stories tab markup, ALL_STORIES, course JSON).
- Profile Settings rows Account / Notifications / Privacy do nothing; ledger prints raw `pending`; lead card prints raw lowercase level.
- Streak bonus (+100 at 3 days, +250 at 5) is never explained anywhere.
- Dead: `✓ Done today` marking on Games tab, old story points toast, GAME_DEFS descriptions, ~200 lines of `.action-*` CSS in the water game, `#screen-action` in quiz.
- environmentle-app.html is an unlinked August duplicate.

---


# ===== hub =====

# Environmentle hub: text inventory (index.html, home.js, engine.js, tour.js, app.js, envie.js, sw.js, manifest.json)

Legend: `[short]` space-constrained (button, tab, chip, badge, pill, short toast). `[em-dash]` contains an em-dash. `[data]` composed from a data file field. `[dead]` written to an element that no longer exists, or never called. `[Envie]` spoken by Envie (speech bubble or toast bubble). `${...}` marks a runtime value.

Line numbers are approximate (file as of 13 Sep 2026).

---

## 0. App shell, manifest, service worker (met before any screen)

| # | Text | Locator |
|---|------|---------|
| 0.1 | `Environmentle, Play your part` | `<title>`, index.html:6 |
| 0.2 | `Environmentle` [short] | `<meta name="apple-mobile-web-app-title">`, index.html:27 |
| 0.3 | `Environmentle` [short] | manifest.json `name` |
| 0.4 | `Environmentle` [short] | manifest.json `short_name` |
| 0.5 | `Small games. Real climate action.` | manifest.json `description` |
| 0.6 | `Environmentle` [short] | sw.js:20, fallback push notification title |
| 0.7 | `Your daily challenge is ready 🌿` [short] | sw.js:22, fallback push notification body (real title/body come from the FCM payload, sent server-side) |
| 0.8 | `Environment` + `le` (wordmark) [short] | `.em-wordmark`, index.html:2761 (repeated 2854, 2934) |
| 0.9 | `Play. Learn. Act.` [short] | `.em-tagline`, index.html:2762 (repeated 2855, 2935) |
| 0.10 | `Profile` [short] | `aria-label` on `.profile-icon-btn`, index.html:2765 (repeated 2858, 2938) |
| 0.11 | Envie's tee prints: `BE ECO` / `FRIENDLY` / `NOT EGO` / `CENTRIC` (eco tee) and `MAKE` / `SCIENCE` / `GREAT` / `AGAIN` (science tee) [short] | `tees.eco`, `tees.science`, envie.js:59-70. Rendered inside the mascot SVG, aria-hidden; readable at large sizes (name screen, tour, story completion) |

## 1. Name screen (`#screen-name`)

| # | Text | Locator |
|---|------|---------|
| 1.1 | `Hi, I'm Envie. I'll tag along.` [Envie] | `says` attr on `.name-envie`, index.html:2718 |
| 1.2 | `Welcome` [short] | `.name-welcome-label`, index.html:2719 |
| 1.3 | `What do<br>we call <em>you?</em>` | `.name-title` h2, index.html:2720 |
| 1.4 | `No account needed. Just your name so we can make it feel a bit more personal.` | `.name-subtitle`, index.html:2721 |
| 1.5 | `Your first name` [short] | `placeholder` on `#name-input`, index.html:2728 |
| 1.6 | `No email, no password, no fuss.` | `.name-hint`, index.html:2737 |
| 1.7 | `Let's go` [short] | `#btn-start`, index.html:2742 |
| 1.8 | `Back` [short] [dead] | `.name-back` button, index.html:2710. Hidden (`hidden` attr); `closeName()` is a no-op ("no landing to go back to", index.html:3466) |

## 2. Home (`#screen-hub`)

### 2a. Top bar and header (static markup)

| # | Text | Locator |
|---|------|---------|
| 2.1 | `Good to see you,` | `#hub-greeting` placeholder, index.html:2774. Overwritten on every hub open by 2.2, never seen |
| 2.2 | `Good morning` / `Good afternoon` / `Good evening` + `, ${playerName}` | `openHub()`, index.html:3507-3508 |
| 2.3 | `Today, pick <em>one</em>` | `#hub-title` placeholder, index.html:2775; also the default in `renderHome()`, home.js:35 |
| 2.4 | `One more<em>?</em>` | `renderHome()`, home.js:35 (when at least one challenge is done) |
| 2.5 | `Done for <em>today</em>` | `renderHome()`, home.js:34 (when day cap reached) |
| 2.6 | `<b>${n}</b> day` / `days` [short] | streak chip `#hub-streak`, `renderStreakChip()`, home.js:52 |
| 2.7 | `Today's three challenges` | `aria-label` on `#hub-ways`, index.html:2836 |

### 2b. Notification prompt (`#notif-prompt`, shown after 1 session, installed, permission not yet decided)

| # | Text | Locator |
|---|------|---------|
| 2.8 | `✕` / `Close` [short] | `.notif-prompt-close` text + aria-label, index.html:2782 |
| 2.9 | `🔔 Never miss a daily challenge` | `.notif-prompt-title`, index.html:2783 |
| 2.10 | `Get a gentle nudge each morning when your new challenge is ready. One notification a day, nothing else.` | `.notif-prompt-desc`, index.html:2784 |
| 2.11 | `Not now` [short] | `.notif-btn-secondary`, index.html:2786 |
| 2.12 | `Yes please!` [short] | `.notif-btn-primary`, index.html:2787 |

### 2c. Install banner (`#install-banner`, mobile browser, not standalone, after 1 session)

| # | Text | Locator |
|---|------|---------|
| 2.13 | `✕` / `Close` [short] | `.install-banner-close`, index.html:2793 |
| 2.14 | `Add us to your home screen` | `.install-banner-title`, index.html:2794 |
| 2.15 | `No app install needed.` | `.install-banner-desc`, index.html:2795 |
| 2.16 | `Tap the <strong>Share</strong> icon in your browser bar` | `.install-step-text`, index.html:2805 |
| 2.17 | `→` | `.install-step-arrow`, index.html:2808 |
| 2.18 | `Select <strong>Add to Home Screen</strong>` | `.install-step-text`, index.html:2816 |
| 2.19 | `Dismiss` [short] | `.install-banner-cta` (dismissInstallBanner), index.html:2822 |
| 2.20 | `Done` [short] | `.install-banner-cta` (acceptInstallBanner), index.html:2826 |

### 2d. The three-way strip (`#hub-ways`, `renderWays()`, home.js:111-145)

| # | Text | Locator |
|---|------|---------|
| 2.21 | `Game` / `Story` / `Action` [short] | `.way-label`, home.js:115-119 |
| 2.22 | `3 min` / `6 min` / `2 min` [short] | `.way-meta` default, home.js:115-119 |
| 2.23 | `${d.game.name}` [data: challenges.json `title_line1 title_line2` for the daily quiz, else engine GAME_NAMES] | `.way-name` game tile, home.js:115 |
| 2.24 | `All played today` [short] | game tile fallback name, home.js:115 |
| 2.25 | `${d.unreadStory.title}` [data: ALL_STORIES title, index.html:3592-3624] | `.way-name` story tile, home.js:117 |
| 2.26 | `Read one again` [short] | story tile fallback name, home.js:117 |
| 2.27 | `${d.action.title}` [data: actions.json `title`] | `.way-name` action tile, home.js:119 |
| 2.28 | `Browse the list` [short] | action tile fallback name, home.js:119 |
| 2.29 | `Quiz` / `Carbon challenge` / `Water challenge` [short] | `GAME_NAMES` (done-tile name, joined with `, `), home.js:124 |
| 2.30 | `Story read` [short] | done story tile fallback, home.js:127 |
| 2.31 | `${st.actionsToday[0].title}` [data: actions.json title] | done action tile, home.js:128 |
| 2.32 | `Back tomorrow` [short] | locked tile name, home.js:134 |
| 2.33 | `Done today` [short] | `.way-badge.done`, home.js:137 |
| 2.34 | `Suggested` [short] | `.way-badge`, home.js:137 |
| 2.35 | (check icon) ` Done` [short] | `.way-meta` when done, home.js:138 |
| 2.36 | `Tomorrow` [short] | `.way-meta` when locked, home.js:138 |
| 2.37 | `Envie picked these for today. Choose your own game or story from the menu below.` | `.ways-hint`, home.js:140 |
| 2.38 | `Two challenges is the daily cap. The games and stories tabs stay open, and Envie is back tomorrow.` | `.ways-hint` when day done, home.js:140 |
| 2.39 | Toast: `That is two for today` / `One a day is the rhythm. Envie will have this one for you tomorrow.` [Envie] [short head] | `showToast(...)` on locked tile tap, home.js:143 |

### 2e. Lead card (`#hub-lead`, `renderLead()`, home.js:148-237). One variant shows per day.

Credit variant (`d.lead === 'credit'`):

| # | Text | Locator |
|---|------|---------|
| 2.40 | `Action · ${held} credit` / `credits` ` ready` [short] | `.pill.solid-sky`, home.js:159 |
| 2.41 | `Turn ${10,000} points into a real tree` | `.lead-title`, home.js:160 |
| 2.42 | `Your points became something that goes in the ground. Pick where it goes and we send it to the partner.` | `.lead-desc`, home.js:161 |
| 2.43 | `Spend my credit` [short] | `.cta.sky`, home.js:162 |
| 2.44 | `Others` [short] | `.cta.ghost.sm` (opens actions list), home.js:162 |

Action variant (`d.lead === 'action'`):

| # | Text | Locator |
|---|------|---------|
| 2.45 | `Suggested action` [short] | `.pill.sky`, home.js:173 |
| 2.46 | `${typeLabel} · ${level}` [short] [data: actions.json meta.types label + raw `level` value `easy`/`medium`, lowercase] | `.lead-sub`, home.js:173 |
| 2.47 | `${action.title}` [data] | `.lead-title`, home.js:174 |
| 2.48 | `${action.desc}` [data: actions.json desc; 48 of 255 descs contain em-dashes] | `.lead-desc`, home.js:175 |
| 2.49 | `Read more` [short] | `.cta.sky`, home.js:176 |
| 2.50 | `+${points} pts` [short] | `.lead-pts`, home.js:176 |
| 2.51 | `Not this one, show me another` [short] | `.lead-skip`, home.js:177 |

Story variant (`d.lead === 'story'`):

| # | Text | Locator |
|---|------|---------|
| 2.52 | `Today's story` [short] | `.pill.amber`, home.js:187 |
| 2.53 | `${s.tag} · ${s.meta}` [short] [data: ALL_STORIES] | `.lead-sub`, home.js:187 |
| 2.54 | `${s.title}` [data: ALL_STORIES] | `.lead-title`, home.js:188 |
| 2.55 | `Read` [short] | `.cta.amber`, home.js:189 |
| 2.56 | `+75 pts` [short] | `.lead-pts`, home.js:189 (hard-coded, not from STORY_POINTS) |

Done variant (`d.lead === 'done'`):

| # | Text | Locator |
|---|------|---------|
| 2.57 | `Streak kept` [short] | `.pill.sky`, home.js:199 |
| 2.58 | `Game, story and action. All three today.` | `.lead-title` (3 done), home.js:200 |
| 2.59 | `Two today. That is the rhythm.` | `.lead-title` (2 done), home.js:200 |
| 2.60 | `Envie keeps the rest for tomorrow. The companion is always open if you want to ask something.` | `.lead-desc`, home.js:201 |
| 2.61 | `Open the companion` [short] | `.cta.ghost`, home.js:202 |

Game variant (default):

| # | Text | Locator |
|---|------|---------|
| 2.62 | `Today's game` [short] | `.pill` when daily quiz, home.js:214 |
| 2.63 | `One more game` [short] | `.pill` when a game already played, home.js:214 |
| 2.64 | `Weekend game` [short] | `.pill` on weekends, home.js:214 |
| 2.65 | `Suggested game` [short] | `.pill` default, home.js:214 |
| 2.66 | `${g.name}` [data: challenges.json `title_line1 title_line2`, or engine GAME_NAMES `Random quiz` / `Carbon challenge` / `Water challenge`] | `.lead-title`, home.js:215, engine.js:258,261 |
| 2.67 | `${g.meta}`: `${category_badge} · 3 questions` [data: challenges.json category_badge] or `Quiz · 3 questions` / `Sorting game · 6 cards` / `Higher or lower · 5 rounds` [short] | `.lead-sub`, engine.js:261,269 |
| 2.68 | `${g.pts}`: `450 pts max` / `300 pts max` / `480 pts max` [short] | `.lead-pts`, engine.js:261,270 |
| 2.69 | `Play` [short] | `#hub-lead-cta`, home.js:216 |
| 2.70 | `All played today` [short] | `.pill` when no game left, home.js:220 |
| 2.71 | `Every game is done for today.` | `.lead-title`, home.js:221 |
| 2.72 | `Next challenge ${tomorrow|Monday...}` / `Back tomorrow` [short] | `.lead-sub`, home.js:222, `unlockLabel()` home.js:286-291 (`tomorrow` or weekday name, en-IE) |
| 2.73 | `Games` [short] | `#hub-lead-cta` when no game left, home.js:224 |

### 2f. Below the lead (`#hub-more`, `renderMore()`, home.js:240-274)

| # | Text | Locator |
|---|------|---------|
| 2.74 | `Or a smaller action today` [short] | `.eyebrow` (credit lead), home.js:247 |
| 2.75 | `Suggested action` [short] | `.eyebrow` (other leads), home.js:247. Same words as 2.45 |
| 2.76 | `${action.title}` [data] | `.row-title`, home.js:250 |
| 2.77 | `Pledge today · +${points} pts` [short] | `.row-meta`, home.js:250 |
| 2.78 | `Action pledged` [short] | `.eyebrow` when pledged today, home.js:254 |
| 2.79 | `${actionsToday[0].title}` [data] | `.row-title`, home.js:257 |
| 2.80 | `Pledged today · streak kept` [short] | `.row-meta`, home.js:257 |
| 2.81 | `${held} credit`/`credits` ` held · next at ${nextAt} pts` [short] | credit bar text, home.js:263 |
| 2.82 | `${total} / ${step} pts to your first impact credit` [short] | credit bar text, home.js:265 |
| 2.83 | `${total} / ${nextAt} pts to your next credit` [short] | credit bar text, home.js:266 |

### 2g. Community block (`#hub-community`, `renderCommunity()`, home.js:95-108). Only when Firestore reachable.

| # | Text | Locator |
|---|------|---------|
| 2.84 | `Together so far` [short] | `.eyebrow`, home.js:101 |
| 2.85 | `Players` / `Games played` / `Actions pledged` / `Credits used` [short] | `.progress-stat-label`, home.js:103-106 |

### 2h. Profile icon badge

| # | Text | Locator |
|---|------|---------|
| 2.86 | `${held}` (number only) [short] | `.credit-badge` on `.profile-icon-btn`, `renderProfileBadge()`, home.js:276-284 |

## 3. Bottom navigation (`#app-bottom-nav`, index.html:3340-3357)

| # | Text | Locator |
|---|------|---------|
| 3.1 | `Home` [short] | `.nav-tab-label`, index.html:3342 |
| 3.2 | `Games` [short] | index.html:3346 |
| 3.3 | `Stories` [short] | index.html:3350 (data-tab is `learn`; app.js:103 also force-relabels any `Learn` tab to `Stories`) |
| 3.4 | `Companion` [short] | index.html:3354 (icon is a magnifier, `search`) |

## 4. First-run tour (`tour.js`, shown once after name entry, again from Profile > Help)

| # | Text | Locator |
|---|------|---------|
| 4.1 | `How the app works` | `aria-label` on `.tour-card` dialog, tour.js:75 |
| 4.2 | `Hi ${name}, I'm Envie.` [Envie] | step 1 head, tour.js:46 (`name` falls back to `there`, tour.js:70) |
| 4.3 | `Every day I hand you three ways to play your part: a game, a story or an action. Pick one and it keeps your streak going. One a day is the rhythm, two is the cap.` [Envie] | step 1 sub, tour.js:47 |
| 4.4 | `Game` / `Story` / `Action` [short] | step 1 art `.tour-ways` (aria-hidden), tour.js:23-25 |
| 4.5 | `Four tabs, that is all.` [Envie] | step 2 head, tour.js:50 |
| 4.6 | `Home is where I hand you today's pick. Games and Stories hold the whole library if you would rather choose your own. Companion is me.` [Envie] | step 2 sub, tour.js:51 |
| 4.7 | `Home` `today's pick` / `Games` `the library` / `Stories` `short reads` / `Companion` `ask me` [short] | step 2 art `.tour-tabs` (aria-hidden), tour.js:29-32 |
| 4.8 | `Ask me anything about climate.` [Envie] | step 3 head, tour.js:54 |
| 4.9 | `I answer from our own checked wiki first, and I say so when I fall back to general AI. I can also take apart a claim you have heard and hand you what to say back.` [Envie] | step 3 sub, tour.js:55 |
| 4.10 | `Is nuclear power low carbon?` [short] | step 3 art `.tour-ask-field` sample query (aria-hidden), tour.js:36 |
| 4.11 | `Points become real things.` [Envie] | step 4 head, tour.js:58 |
| 4.12 | `Games, stories and actions all earn points. Every 10,000 turns into an impact credit you can spend on a native tree or a patch of meadow with our partners. Now, pick one.` [Envie] | step 4 sub, tour.js:59 |
| 4.13 | `0 / 10,000 pts to your first impact credit` [short] | step 4 art `.tour-credit` (aria-hidden), tour.js:41 |
| 4.14 | `Skip` [short] | `#tour-skip`, tour.js:94 (empty on last step) |
| 4.15 | `Next` [short] | `#tour-next`, tour.js:96 |
| 4.16 | `Let's go` [short] | `#tour-next` on last step, tour.js:96. Same label as name screen 1.7 |

## 5. Games tab (`#screen-games`, index.html:2846-2924)

| # | Text | Locator |
|---|------|---------|
| 5.1 | `4 games · Free to play` [short] | `.games-lib-eyebrow-text`, index.html:2873 (only 3 are playable) |
| 5.2 | `GAMES` [short] | `.games-lib-title`, index.html:2875 |
| 5.3 | `Small games. Real climate action. Pick one and go.` | `.games-lib-subtitle`, index.html:2876 |
| 5.4 | `Quiz` [short] | `.rim.quiz .rim-tag`, index.html:2885 |
| 5.5 | `Random challenge` | `.rim.quiz .rim-title`, index.html:2886 |
| 5.6 | `3 questions` / `450 pts` / `~2 min` [short] | `.rim.quiz .chip`, index.html:2887 |
| 5.7 | `Sorting game` [short] | `.rim.sort .rim-tag`, index.html:2896 |
| 5.8 | `Sort it out` | `.rim.sort .rim-title`, index.html:2897 |
| 5.9 | `6 cards` / `3 tries` / `~3 min` [short] | `.rim.sort .chip`, index.html:2898 |
| 5.10 | `Water game` [short] | `.rim.water .rim-tag`, index.html:2907 |
| 5.11 | `Which one drinks more?` | `.rim.water .rim-title`, index.html:2908 |
| 5.12 | `5 rounds` / `480 pts` / `~3 min` [short] | `.rim.water .chip`, index.html:2909 |
| 5.13 | `Greenwasher` | `.rim.soon .rim-title`, index.html:2917 |
| 5.14 | `In the workshop` [short] | `.rim.soon .rim-meta`, index.html:2917 |

## 6. Stories tab (`#screen-learn`, index.html:2926-3035)

| # | Text | Locator |
|---|------|---------|
| 6.1 | `Short stories · Free` [short] | `.learn-hero-eyebrow-text`, index.html:2953 |
| 6.2 | `STORIES` [short] | `.learn-hero-title`, index.html:2955 |
| 6.3 | `Short, visual stories on climate and sustainability. No jargon, no overwhelm.` | `.learn-hero-subtitle`, index.html:2956 |
| 6.4 | `Available now` [short] | `.learn-section-label`, index.html:2962 |
| 6.5 | `Good news` / `Climate wins in 2025` / `10 slides` / `5 min` | `.rim.topic-climate`, index.html:2967-2969 |
| 6.6 | `Tech & energy` / `Data centres in Ireland` / `11 slides` / `5 min` | `.rim.topic-data`, index.html:2978-2980 (course JSON has 12 slides) |
| 6.7 | `Finance` / `Climate & finance` / `10 slides` / `6 min` | `.rim.topic-finance`, index.html:2989-2991 (course JSON has 9 slides) |
| 6.8 | `Brands & activism` / `The Ben & Jerry's story` / `14 slides` / `7 min` | `.rim.topic-activism`, index.html:3000-3002 |
| 6.9 | `Land & nature` / `Ireland's forestry problem` / `19 slides` / `8 min` | `.rim.topic-forestry`, index.html:3011-3013 |
| 6.10 | `Coming soon` [short] | `.learn-section-label`, index.html:3018 |
| 6.11 | `Food waste and composting` / `Food & waste · 4 min read` | `.rim.soon`, index.html:3022 |
| 6.12 | `Talking about climate change` / `Society · 6 min read` | `.rim.soon`, index.html:3028 |

Story metadata duplicated in JS (`ALL_STORIES`, index.html:3592-3624), used by the home lead card, the ways strip, and the "Next story" button:

| # | Text | Locator |
|---|------|---------|
| 6.13 | `Finance` / `Climate & Finance` / `10 slides · 6 min` | ALL_STORIES[0], index.html:3597-3599 |
| 6.14 | `Tech & Energy` / `Data Centres in Ireland` / `12 slides · 5 min` | ALL_STORIES[1], index.html:3605-3607 |
| 6.15 | `Good News` / `Climate Wins in 2025` / `10 slides · 5 min` | ALL_STORIES[2], index.html:3613-3615 |
| 6.16 | `Brands & Activism` / `The Ben & Jerry's Story` / `14 slides · 7 min` | ALL_STORIES[3], index.html:3621-3623 |
| 6.17 | `Land & Nature` / `Ireland's Forestry Problem` / `19 slides · 8 min` | ALL_STORIES[4], index.html:3629-3631 |

## 7. Story player (`#course-player`, index.html:3166-3207; JS index.html:4180-4526)

| # | Text | Locator |
|---|------|---------|
| 7.1 | `✕` / `Close` [short] | `.cp-close`, index.html:3172 |
| 7.2 | `1 / 10` placeholder, then `${idx+1} / ${total}` [short] | `#cp-counter`, index.html:3200, `_renderSlide()` 4443, `_renderCompletionSlide()` 4498 |
| 7.3 | `swipe to navigate` [short] | `#cp-hint`, index.html:3203 |
| 7.4 | `Could not load this course. Please try again.` | `alert()` in `openCourse()`, index.html:4199. Only place the word "course" is user-facing |
| 7.5 | `Still funding the problem` [short] | compare slide default `badLabel`, index.html:4357 (every course sets its own; fallback only) |
| 7.6 | `Aligned with the transition` [short] | compare slide default `goodLabel`, index.html:4361 (fallback only) |
| 7.7 | All slide copy: `text`, `bold`, `callout`, `bullets`, `footer`, `stat`, `caption`, `note`, `source`, `tag`, `title`, `bad[]`, `good[]`, `badLabel`, `goodLabel`, `emoji`, `envie` [data: courses/course-*.json] | `_slideHTML()`, index.html:4311-4402 |
| 7.8 | Envie's slide lines [Envie] [data: course JSON `envie` field], e.g. `One condition, and it mattered.`, `Your money works while you sleep. The question is for whom.`, `Same account, much smaller footprint.`, `Records are nicer when they go this way.`, `Norway didn't wait for anyone.`, `Half the planet is ocean. About time.`, `One in four. On an island this size.`, `That's a lot of electricity for storing photos.`, `So everyone's grid is asking the same question.`, `Look up on your next walk. Not many trees, are there?`, `The appetite is there. Now the trees.` | `_cpEnvie()`, index.html:4308; data in courses/*.json |

### Story completion overlay (`#cp-completion`, `_renderCompletionSlide()`, index.html:4453-4499)

| # | Text | Locator |
|---|------|---------|
| 7.9 | `Story complete` | `<h2>`, index.html:4482 |
| 7.10 | `+75` / `pts added to your score` [short] | `.pts` / `.pts-sub` (first read), index.html:4485-4486 |
| 7.11 | `You've read this one before, no new points.` | `.pts-sub` (repeat read), index.html:4488 |
| 7.12 | `${n} slides read` [short] | `.chip`, index.html:4489 |
| 7.13 | `${mins}` e.g. `6 min` [short] [data: ALL_STORIES meta, split on `·`] | `.chip`, index.html:4489 |
| 7.14 | `Done` [short] | `.cta.white`, index.html:4490 |
| 7.15 | `Next story: ${next.title} →` [short] [data: ALL_STORIES title] | `.next` button, index.html:4491 |

## 8. Profile (`#screen-profile`, index.html:3037-3126; `openProfile()` home.js:566-626)

| # | Text | Locator |
|---|------|---------|
| 8.1 | `Back` [short] | `.profile-back` text + aria-label, index.html:3039-3041 |
| 8.2 | `Profile` [short] | `.profile-topbar-title`, index.html:3043 |
| 8.3 | `Sign out` [short] | `.profile-signout`, index.html:3046 |
| 8.4 | `Player` placeholder, then `${playerName}` (fallback `Player`) | `#profile-name`, index.html:3055, home.js:573 |
| 8.5 | `Player · Level 1` placeholder, then `Player · Level ${level}` [short] | `#profile-tag`, index.html:3056, home.js:574 (level = score/250 + 1) |
| 8.6 | `Score` / `Games` / `Pledged` / `Streak` [short] | `.progress-stat-label`, index.html:3062-3074 |
| 8.7 | Credit card, ready state: `Milestone reached` [short] / `${held} credit(s) ready to plant` / `${10,000} points became one real thing in the ground. Pick where it goes and we send it to the partner, you get the photo back.` / `Use my credit` [short] / `Save it` [short] | `#profile-credit`, home.js:588-592 |
| 8.8 | Credit card, progress state: `Impact credits` [short] / `Turn points into planting` / `${spent} planted` [short pill] / `${total}` `/ ${nextAt} pts` / `<strong>${toGo} points</strong> to your first|next credit` / `≈ ${n} game(s)` [short] / `1 credit funds a native tree, a metre of hedgerow or a pollinator patch with our partners.` | `#profile-credit`, home.js:596-601 |
| 8.9 | `Your milestones` [short] | `.profile-section-label`, index.html:3081 |
| 8.10 | `${n} pts` [short] (milestone labels, e.g. `1,000 pts`, `10,000 pts`, `20,000 pts`) | `#profile-milestones .ms-label`, home.js:609-619 |
| 8.11 | `Actions taken` [short] | `.profile-section-label`, index.html:3085 |
| 8.12 | `Browse actions` [short] | `.profile-link`, index.html:3086 |
| 8.13 | `Nothing pledged yet. Envie will suggest one when the week is far enough along.` | `.actions-history-empty`, `renderActionHistory()`, home.js:637 |
| 8.14 | `${a.title}` [data: actions.json title or partners.json title] / `${Today|Yesterday|N days ago|D Mon} · +${pts} pts` or ` · impact credit` [short] | `.actions-history-title` / `.actions-history-date`, home.js:644-645; `formatRelativeDate()` index.html:3390-3399 |
| 8.15 | `Help` [short] | `.profile-section-label`, index.html:3090 |
| 8.16 | `How the app works, with Envie` | `.settings-item-label` (restarts tour), index.html:3094 |
| 8.17 | `FAQ` [short] | `.settings-item-label`, index.html:3099 |
| 8.18 | `Settings` [short] | `.profile-section-label`, index.html:3104 |
| 8.19 | `Account` / `Notifications` / `Privacy` [short] | `.settings-item-label`, index.html:3108-3118. Buttons have no onclick: placeholder rows that do nothing |
| 8.20 | Toast: `Saved` / `Your credit stays on your profile until you use it.` [Envie] | `saveCredit()`, home.js:629 |

## 9. Actions screen (`#screen-actions`, index.html:3129-3148; `renderActions()` home.js:314-362)

| # | Text | Locator |
|---|------|---------|
| 9.1 | `Home` [short] (aria-label `Back`) | `.profile-back`, index.html:3131-3133 |
| 9.2 | `Take an action` [short] | `.profile-topbar-title`, index.html:3135 |
| 9.3 | `${left} left this week` / `Week done` [short] | `#actions-cap`, home.js:331 |
| 9.4 | Envie bubble, week full: `That is your week sorted.` / `You have pledged 2 this week. Browse for ideas, pledge again from Monday.` [Envie] | `#actions-bubble`, home.js:325 |
| 9.5 | Envie bubble, one pledged: `One more this week, if you like.` / `Small and specific beats big and vague. Pick something you would actually do.` [Envie] | home.js:326 |
| 9.6 | Envie bubble, none pledged: `Pick one you can actually do.` / `These come from what you have been playing and reading. Pledge it, and it counts as today.` [Envie] | home.js:327 |
| 9.7 | Filter chips: `For you` [short] then `Water` / `Food` / `Energy at home` / `Getting around` / `Circularity and waste` / `Nature and biodiversity` / `Community` / `Habits and learning` [short] [data: actions.json meta.types] | `#actions-filters .chip`, home.js:337 |
| 9.8 | Action card tags: `${typeLabel}` [data] / `Easy` / `Medium` [short] / `Community` [short] (scope tag) | `.act-tags`, `actionCard()`, home.js:371 |
| 9.9 | `${a.title}` / `${a.desc}` [data: actions.json; 3 titles and 48 descs carry em-dashes] | `.act-title` / `.act-desc`, home.js:372-375 |
| 9.10 | `+${points} pts` + ` · ${impact}` [short] [data: actions.json impact, e.g. `Up to 100 kg CO₂/year if acted on`, `Knowledge is the first step`] | `.act-pts`, home.js:377 |
| 9.11 | `Pledged` [short] | `.act-done`, home.js:378 |
| 9.12 | `Read more` [short] | `.act-btn`, home.js:379 |
| 9.13 | `Show ${n} more` [short] | `#actions-more`, home.js:353 |
| 9.14 | `The actions list could not be loaded. Open the app through a web server, not as a file.` | `.actions-empty`, home.js:355 (developer-facing wording shown to users) |
| 9.15 | `Nothing left in this list. You have pledged them all.` | `.actions-empty`, home.js:356 |
| 9.16 | Toast: `Pledged. +${pts} pts` / `${n}-day streak` or `Streak started` [Envie] [short] | `pledgeAction()`, home.js:394 |

## 10. Action sheet (`#action-sheet`, `openActionSheet()` home.js:419-467)

| # | Text | Locator |
|---|------|---------|
| 10.1 | `Close` [short] | `aria-label` on `.sheet-close`, home.js:440 |
| 10.2 | Tags: `${typeLabel}` / `Easy` / `Medium` / `Community` [short] [data] | `.act-tags`, home.js:444 |
| 10.3 | `${a.title}` / `${a.desc}` [data] | `.sheet-title` / `.sheet-desc`, home.js:445-448 |
| 10.4 | `Worth` / `+${points} pts` [short] | `.sheet-fact`, home.js:450 |
| 10.5 | `Impact` / `${a.impact}` [short] [data] | `.sheet-fact`, home.js:451 |
| 10.6 | `Why now` / source line [short label] | `.sheet-fact`, home.js:452 |
| 10.7 | Source lines: `From the challenge "${title_line1} ${title_line2}" · ${Weekday D Mon}` [data: challenges.json] / `From a daily challenge` / `From the carbon sorting game · ${card}` [data] / `From the water challenge` | `sourceLine()`, home.js:408-418 |
| 10.8 | Envie, already pledged: `You already pledged this one.` [Envie] | home.js:435 |
| 10.9 | Envie, week full: `Your two for this week are in. Come back Monday for this one.` [Envie] | home.js:436 |
| 10.10 | Envie, medium: `A bit more effort than most. Worth it if it fits your week.` [Envie] | home.js:437 |
| 10.11 | Envie, easy: `Small, specific, and done in a day. That is the kind that sticks.` [Envie] | home.js:438 |
| 10.12 | `Pledge it` [short] | `#sheet-pledge`, home.js:456 |
| 10.13 | `Not this one` [short] / `Close` [short] (when already pledged) | `#sheet-skip`, home.js:457 |
| 10.14 | `Do a game or story instead` [short] | `#sheet-alt`, home.js:459 |
| 10.15 | Toast: `Noted` / `Here is another one instead.` [Envie] [short] | `skipAction()`, home.js:482 |

## 11. Impact credit screen (`#screen-credit`, index.html:3152-3163; `renderCredit()` home.js:495-548)

| # | Text | Locator |
|---|------|---------|
| 11.1 | `Profile` [short] (aria-label `Back`) | `.profile-back`, index.html:3154-3156 |
| 11.2 | `Impact credit` [short] | `.profile-topbar-title`, index.html:3158 |
| 11.3 | Envie hero, credit held: `${held} credit(s) ready to plant.` / `Pick where it goes. We send it to the partner, you get the photo back.` [Envie] | home.js:504 |
| 11.4 | Envie hero, none: `No credit yet.` / `${toGo} points to go. Every game, story and action counts.` [Envie] | home.js:505 |
| 11.5 | `Where it can go` [short] / `${n} partners` [short] | `.credit-list-head`, home.js:535 |
| 11.6 | Partner rows: `${p.title}` / `${p.sub}` / `${credits} credit(s)` [short pill] [data: partners.json: `Plant a native tree` / `Woodland partner · Co. Wicklow`; `Pollinator meadow` / `10 m² of wildflower verge`; `Seagrass restoration` / `Coastal habitat · pilot site`] | `.row.partner`, home.js:510-513 |
| 11.7 | `${picked.desc}` [data: partners.json desc, e.g. `One native whip, planted this season. Oak, birch, rowan or hazel, depending on the site. Envie will show you the spot.`] | `.credit-confirm-desc`, home.js:521 |
| 11.8 | `Your email, so we can validate the credit and send you the photo` | `.credit-email span` (label), home.js:523 |
| 11.9 | `you@example.com` [short] | `placeholder` on `#credit-email`, home.js:524 |
| 11.10 | `That does not look like an email address.` | `#credit-email-err`, home.js:526 |
| 11.11 | `Plant it · ${n} credit(s)` [short] | `#credit-go`, home.js:527 |
| 11.12 | `We only use your email for this credit and for news about it. Partners are placeholders for now.` | `.credit-note`, home.js:528 |
| 11.13 | `Already planted` [short] | `.eyebrow`, home.js:531 |
| 11.14 | Ledger rows: `${title}` [data] / `${relative date} · ${n} credit(s) · ${status}` [short] (`status` is the raw value `pending`, engine.js:346) | `.row.done`, home.js:532 |
| 11.15 | `Next credit at ${nextAt} pts · <strong>${toGo} to go</strong>` [short] | `.credit-bar.static`, home.js:537 |
| 11.16 | Toast: `${partner.title}` / `Sent to the partner. Counts as today's action.` [Envie] [data] | `spendCredit()`, home.js:561 |

## 12. FAQ panel (`#faq-panel`, index.html:3212-3333)

| # | Text | Locator |
|---|------|---------|
| 12.1 | `Back` [short] | `aria-label` on `.faq-back`, index.html:3215 |
| 12.2 | `FAQ` [short] | `.faq-panel-title`, index.html:3218 |
| 12.3 | Q: `How does Environmentle work?` | `.faq-question-text`, index.html:3224 |
| 12.4 | A (7 paragraphs), index.html:3229-3235: `Every day Envie hands you three ways to play your part: a <strong>game</strong>, a <strong>story</strong> or an <strong>action</strong>. Pick one. That is your challenge for the day, and it keeps your streak going.` / `<strong>Games</strong> are short quizzes and sorting games built on real data. The daily quiz changes every weekday. On weekends the library is open and Envie suggests a game you have not played for a while.` / `<strong>Stories</strong> are short visual reads on one climate topic, five to eight minutes each.` / `<strong>Actions</strong> are small, specific things you can do this week, drawn from what you have been playing and reading. Envie suggests one later in the week, once you have done a couple of challenges. You read it, then pledge it or say no and get another. Two pledges a week is the limit.` / `<strong>One challenge a day is the rhythm.</strong> If you want more, you can do a second one. After that, the home page tells you the day is done and Envie waits for tomorrow. The Companion is always open.` / `<strong>Points.</strong> Games earn up to 450 or 480 points depending on how well you do, a story earns 75, and an action 50 to 100. Playing several days in a row adds a streak bonus.` / `<strong>Impact credits.</strong> Every 10,000 points becomes an impact credit. Your score is never reduced. A credit can be spent with one of our partners on something real, like a native tree or a patch of pollinator meadow. Your profile shows how far you are from the next one.` | `.faq-answer-inner p` |
| 12.5 | Q: `What is the environmental impact of the AI used on this app, and what do we do about it?` | index.html:3242 |
| 12.6 | A (9 paragraphs), index.html:3247-3255: `The rapid growth of AI, and the data centres needed to support it, is a real and important issue. It can affect energy grids and local water supplies, and we take that seriously.` / `That said, we believe AI has a genuine role to play in making climate and sustainability information more accessible. The challenge is doing it responsibly.` / `Here is how we try to get that balance right:` / `<strong>We use Infomaniak as our AI provider.</strong> Infomaniak is a Swiss-based company widely recognised as one of the most ethical cloud and AI providers in the world. They run their infrastructure on renewable energy and are transparent about their environmental commitments.` / `<strong>We use lightweight, efficient models.</strong> Not all AI models are equal when it comes to resource use. The most energy-intensive part of AI is training large models from scratch, not running them day to day. We use Mistral and Apertvs, two models specifically built to be compact and efficient.` / `Apertvs is fully open-source, developed by ETH Zurich and EPFL, two of Europe's leading research universities. It is released under an open licence, which means its training, architecture, and data are publicly documented. No black box.` / `<strong>Your data stays in a controlled environment.</strong> Because we use Infomaniak's infrastructure, your queries are processed in a closed system, not sent to large commercial cloud platforms.` / `We are not perfect, and the field is evolving fast. But we believe choosing the right partners and tools makes a real difference.` | `.faq-answer-inner p` |
| 12.7 | Q: `Where does the information in the AI Companion come from?` | index.html:3261 |
| 12.8 | A (4 paragraphs), index.html:3266-3269: `The AI Companion draws on two sources, and it always tells you which one it is using.` / `<strong>First: our own curated knowledge base.</strong> We have built and maintain a wiki of climate and sustainability information, sourced, reviewed, and validated by us. When you ask a question, the Companion first searches this wiki using MistralAI. If a clear answer is found there, that is what you get.` / `<strong>Second: a fallback to broader AI knowledge.</strong> If the answer cannot be found in our wiki, the Companion falls back to Apertvs, an open-source model developed by Swiss research institutions ETH Zurich and EPFL, to provide a response based on its training data. When this happens, the answer is clearly labelled so you know it is coming from general AI knowledge rather than our curated sources.` / `This approach means that, as much as possible, answers are grounded in verified, local information, and you always know where they come from.` | |
| 12.9 | Q: `How does this app approach digital wellbeing?` | index.html:3277 |
| 12.10 | A (3 paragraphs), index.html:3282-3284: `This app is not trying to capture your attention or keep you scrolling. We are aware of the pressure that comes from constant digital noise, and from the weight of negative climate narratives. That is not what we want to add to your day.` / `Our research shows that people want simple, trustworthy ways to stay informed, and that short, playful challenges are a good way to do that without feeling overwhelmed.` / `With that in mind, Environmentle is built around one challenge a day, with a cap of two. A game takes about three minutes, a story about six, an action two. That is intentional. We want you to get something useful out of it, and then get on with your day.` | |
| 12.11 | Q: `What do you keep about me?` | index.html:3291 |
| 12.12 | A (3 paragraphs), index.html:3296-3298: `We only ask for your first name, so Envie knows what to call you. No email, no password, no phone number, nothing else.` / `Your points, streak, games played, stories read and actions pledged are kept in a database against that name and nothing else, so they follow you between devices. We use those figures in aggregate, to see how many games were played, how many actions were pledged and how many points were earned across everyone. They are never sold or shared.` / `If you turn on notifications we store the token your browser gives us for that purpose only, and you can switch it off at any time.` | (contradicts 12.14: email is asked for when spending a credit) |
| 12.13 | Q: `What is a web app, and why not a normal app?` | index.html:3305 |
| 12.14 | A (3 paragraphs), index.html:3310-3312: `Environmentle is a web app. It runs in your browser, and you can add it to your home screen so it opens like any other app, but there is nothing to download from an app store.` / `That makes it lighter. It takes almost no space on your phone, it updates on its own, and it works on any device with a browser. There is no account to create and no store to go through.` / `For simple game dynamics like ours, a few minutes a day, that is the right fit. A native app would add weight and friction without adding anything you would notice.` | |
| 12.15 | Q: `Why am I asked to log in to use my credit?` | index.html:3320 (there is no login; it is an email field) |
| 12.16 | A (3 paragraphs), index.html:3325-3327: `Playing never needs more than your first name. Spending a credit is different: it funds a real project in the ground, so at that point we ask for an email address.` / `We use it to validate the credit and pass it on to the partner you chose, so the tree or the meadow actually gets funded. It also lets us send you news about your impact, like the photo when it is planted.` / `That is the only time we ask, and it is the only thing we use it for.` | |

## 13. Shared helpers producing user-facing fragments

| # | Text | Locator |
|---|------|---------|
| 13.1 | `Today` / `Yesterday` / `${n} days ago` / `${D Mon}` [short] | `formatRelativeDate()`, index.html:3390-3399 (profile history, credit ledger) |
| 13.2 | `tomorrow` / weekday name (en-IE) [short] | `unlockLabel()`, home.js:286-291 |
| 13.3 | `${Weekday}, ${D} ${Mon}` (en-IE) [short] | `sourceLine()` date, home.js:412 |
| 13.4 | `Stories` [short] | app.js:103, forced relabel of any `learn` tab |

## 14. Copy that lives in data files (not in these source files)

- **actions.json** (255 actions): `title`, `desc`, `impact`, `level` (`easy`/`medium`, printed raw in lowercase on the home lead sub-line, home.js:173), `meta.types` labels (filter chips and type tags), `meta.levels` (`Easy`/`Medium`, though home.js hard-codes those two words instead of reading them), `meta.scopes` (`Individual action` / `Community action`, unused: home.js prints `Community` only). 48 descs and 3 titles contain em-dashes [em-dash]. `impact` values mix units and tone (`~50–100 kg CO₂ saved over April`, `Good for your mental health`, `Awareness step`).
- **challenges.json** (120): `title_line1` + `title_line2` (daily quiz name on the ways strip and lead card; source line in the action sheet), `category_badge` (lead sub-line). Title case throughout (`The Recycling` / `Label Trick`). 242 em-dashes in the file, mostly in intro/action copy used by the quiz game, not the hub.
- **partners.json** (3, flagged as placeholders in its own `meta.note`): `title`, `sub`, `desc`. `sub` uses the middle dot (`Woodland partner · Co. Wicklow`).
- **courses/course-*.json** (5): every slide field (see 7.7) plus `envie` lines (7.8), `badLabel`/`goodLabel`, `title` (with HTML entities: `The Ben &amp; Jerry's Story`, `Ireland&rsquo;s Forestry Problem`). 10 em-dashes across data-centres, climate-finance and climate-wins courses [em-dash].
- **ALL_STORIES** (index.html:3592-3624) is a hand-maintained copy of story tag/title/meta, separate from both the Stories tab markup and the course JSON. Three places to keep in sync.

## 15. Dead strings (rendered to elements that no longer exist, or never invoked)

| # | Text | Locator | Why dead |
|---|------|---------|----------|
| D1 | `Back` (name screen back button) | `.name-back`, index.html:2710-2716 | `hidden` attribute; `closeName()` is a no-op |
| D2 | `Story complete!` / `+75 pts` / `Added to your score` | `#cp-points-toast`, index.html:3189-3196 | No JS ever adds `.visible`; the completion overlay (7.9-7.15) replaced it |
| D3 | `✓ Done today` | `refreshGameCards()`, index.html:3564 | Targets `.row.quiz`, `.row.sort`, `.row.water`; the Games tab now uses `.rim.*`, so nothing matches. Games played today are not marked on the Games tab at all |
| D4 | `Read · no new points` | `renderHomeStories()`, index.html:3677 | Function never called; `#recent-stories-section` / `#recent-stories-list` do not exist |
| D5 | `Recently played` / `Your games` / `Games to try` | `renderRecentGames()`, index.html:3809-3811 | Never called; `#recent-games-list` / `#recent-games-label` do not exist |
| D6 | GAME_DEFS `name` / `desc` / `tag` / `meta`: `Random challenge` / `A random quiz from our library of daily challenges. Three questions, real data.` / `Carbon challenge` / `6 everyday items. Drag them into order by carbon footprint, lowest to highest. 3 tries.` / `Water challenge` / `Higher or lower, water edition. Guess which everyday thing takes more water to make. Five rounds, one wrong answer ends the run.` / `Quiz` / `Sorting game` / `Water game` / `3 questions` / `450 pts max` / `6 cards` / `3 tries` / `5 rounds` / `480 pts max` | `GAME_DEFS`, index.html:3708-3733 | Only consumed by `gameCardHTML()` (dead) and `recordRecentGame()` (uses keys only). Note the `desc` strings are the only prose descriptions of the games anywhere in the hub and are currently unseen |
| D7 | `just now` / `${n} min ago` / `${n}h ago` / `yesterday` / `${n} days ago` / `last week` / `${n} weeks ago` / `last month` / `${n} months ago` / ` · played ${ago}` | `timeAgo()`, `gameCardHTML()`, index.html:3736-3762 | Only called from dead `renderRecentGames()` |
| D8 | `tomorrow` / `${D Mon}` | `_unlockLabel()`, index.html:3766-3771 | Never called (home.js has its own `unlockLabel`) |
| D9 | `🌱 🍃 🌊 🌿 💚 ⚡ 🌍` | `getCategoryIcon()`, index.html:3485-3495 | Never called |
| D10 | (no text) `refreshHubProgress()` | index.html:3630-3641 | Never called; `#hub-progress` does not exist |
| D11 | `reason` strings: `Day done` / `A credit is waiting` / `Weekend: time to try something` / `You have learnt; now act` / `Today's challenge` / `A story you have not read` / `An action fits today` / `All three done today` / `Free games` / `Read one again` | `Engine.decide()`, engine.js:292-308 | Returned in `d.reason` but never rendered by home.js. Reads like debug labels; treat as internal unless a "why this" line is planned |
| D12 | `Good to see you,` | `#hub-greeting`, index.html:2774 | Placeholder overwritten before the screen is visible |
| D13 | `1 / 10` | `#cp-counter`, index.html:3200 | Placeholder overwritten on open |
| D14 | `Player` / `Player · Level 1` | `#profile-name` / `#profile-tag`, index.html:3055-3056 | Placeholders overwritten on open (`Player` also serves as the runtime fallback name, home.js:573) |
| D15 | `Account` / `Notifications` / `Privacy` | index.html:3108-3118 | Visible but inert: buttons with no handler. `disableNotifications()` (index.html:4081) exists but nothing calls it |
| D16 | `Still funding the problem` / `Aligned with the transition` | index.html:4357, 4361 | Fallbacks; all five courses set their own labels |
| D17 | `pledgeFromHome()` / `showEnvieToast` | home.js:397, 673 | Exported, never called from index.html (may be used by game pages, out of scope) |

## 16. Skip list (internal, not user-facing)

- `console.warn(...)`: `loadPlayerProfile failed:`, `syncPlayerToFirestore failed:`, `SW registration failed:`, `Notification permission error:`, `Token save failed:`, `Unsubscribe failed:` (index.html:3945, 3975, 3996, 4056, 4077, 4092)
- File header comments in home.js, engine.js, tour.js, app.js, envie.js, sw.js (three carry em-dashes in the comment line: engine.js:2, envie.js:2, app.js:2)
- HTML comments in index.html (`<!-- Hub header — navy ... -->` etc., 12 with em-dashes) and CSS comments (9 with em-dashes). No user-facing string in index.html, home.js, engine.js, tour.js, app.js, envie.js, sw.js or manifest.json contains an em-dash.
- Firebase config keys, VAPID key, Firestore field names (`sessionsPlayed`, `actionsPledged`, `creditsUsed`), localStorage key prefixes
- `partners.json meta.note` (`Placeholder partners. Names, places and costs are illustrative...`) is a file note, not rendered
- `actions.json meta.generated_from`
- Ledger `status: 'pending'` (engine.js:346) is a data value but IS printed raw in the credit ledger row (11.14), so it belongs in the rewrite
- `d.reason` strings (D11) unless surfaced
- Material Symbols ligature names in markup (`person`, `close`, `check_circle`, `swipe`, `arrow_back`, `chevron_right`, `home`, `sports_esports`, `auto_stories`, `search`): icon names, replaced by SVG at boot where app.js touches them; `swipe` and `arrow_back`/`chevron_right`/`person`/`close`/`check_circle` remain as icon font glyphs
- Envie web component attribute docs (envie.js:4-18)


# ===== games-quiz-sort =====

# Text inventory 2: Daily Quiz and Sort It Out

Files covered (HTML/JS only, no JSON/CSV content):

- `/Users/fabienmossiere/_Environmentle/app/environmentle-quiz-game.html` (1873 lines)
- `/Users/fabienmossiere/_Environmentle/app/environmentle-sort-it-out.html` (2095 lines)

Tags: `[short]` = space-constrained (badge, pill, nav label, button in a tight row). `[em-dash]` = contains a literal em-dash. `[data]` = composed from a data field at runtime. `[static-placeholder]` = hardcoded text in the HTML that JS overwrites before the user can see it (still worth rewriting if JS ever fails to run). `[dead]` = present in the file but not reachable by a user.

Line numbers are approximate (from `cat -n` on 2026-09-13).

---

## Game 1: Daily Quiz (`environmentle-quiz-game.html`)

### 1.1 Chrome (always present)

| # | Text | Locator |
|---|------|---------|
| Q1 | `Environmentle, Daily Quiz` | `<title>` l.6 |
| Q2 | `Environmentle logo` | `img[alt]` in `a.back-to-hub` l.1128 |
| Q3 | `Environment` + `le` (wordmark, two spans) | `.em-wordmark` / `.em-le` l.1130 [short] |
| Q4 | `Play. Learn. Act.` | `.em-tagline` l.1131 [short] |
| Q5 | `Home` | `.app-nav-label` in `nav#game-bottom-nav` l.1296 [short] [dead] |
| Q6 | `Games` | `.app-nav-label` l.1300 [short] [dead] |
| Q7 | `Learn` | `.app-nav-label` l.1304 [short] [dead] (app.js `tabbar()` rewrites this to `Stories` at runtime) |
| Q8 | `Companion` | `.app-nav-label` l.1308 [short] [dead] |

Notes: `nav#game-bottom-nav` is `display:none` in CSS (l.887) and `showScreen()` forces it hidden on every transition (l.1599-1600). Its four labels are never visible in this game. Q1 title is a browser-tab string, not on-screen in PWA mode.

### 1.2 Played-today gate (`#screen-played`, l.1138-1154)

Reached via `boot()` l.1861 when `hasPlayedToday()` is true, and via `startQuiz()` l.1626 as a hard guard.

| # | Text | Locator |
|---|------|---------|
| Q9 | `🌿` (emoji only) | inline `div` l.1140 |
| Q10 | `You've already played today` | `p.intro-label` l.1141 |
| Q11 | `Come back` / `tomorrow` (h1, second word italic/green) | `h1.intro-title` l.1142 |
| Q12 | `One challenge per day, that's part of the fun. Your score from today:` | inline `p` l.1143-1145 |
| Q13 | `—` (score placeholder) | `#played-score` l.1146 [em-dash] [static-placeholder]; also JS fallback `'—'` in `boot()` l.1863 [em-dash] when no saved score |
| Q14 | `pts` | inline `p` under score l.1147 [short] |
| Q15 | `← Back to games` | `a.btn-play` l.1151 |

### 1.3 Intro (`#screen-intro`, l.1159-1190)

Populated by `initIntro()` l.1614-1619 from `challenge.*` (from challenges.json via `adaptJsonChallenge()` l.1462, or `FALLBACK_CHALLENGE`).

| # | Text | Locator |
|---|------|---------|
| Q16 | `Food & Water` | `#intro-category` l.1162 [short] [static-placeholder] -> `challenge.introBadge` [data] |
| Q17 | `Daily Challenge` | `.sub-badge` l.1163 [short] |
| Q18 | `Today's Topic` | `p.intro-label` l.1165 [short] |
| Q19 | `Irish` / `Coffee Day` (h1, line 2 italic) | `#intro-title` l.1166 [static-placeholder] -> `challenge.titleDisplay` = `title_line1 + '<br><em>' + title_line2 + '</em>'` [data] |
| Q20 | Envie speech bubble (pose `point`, hat `cap`): `Irish Coffee was first made in **1943 by Joe Sheridan** at Foynes Port, Co. Limerick. Today, coffee shops outnumber pubs in Ireland, and each cup carries a hidden environmental cost.` | `#intro-fact` inside `.bb.r2.tail-low` l.1170-1172 [static-placeholder] -> `challenge.introFact` (innerHTML) [data]. When `?random=1`, `adaptJsonChallenge()` l.1465 strips a leading `Today ... .` sentence. |
| Q21 | `3 questions` | `.meta-item` l.1179 [short] (hardcoded 3; questions array length is not used here) |
| Q22 | `Under 3 minutes` | `.meta-item` l.1180 [short] (hardcoded; actual max is 3 x 20 s = 1 min) |
| Q23 | `450 pts max` | `#meta-max-pts` l.1181 [short]; JS rewrites as `` `${TOTAL_MAX} pts max` `` l.1618 (TOTAL_MAX = 450) |
| Q24 | `Start challenge` (+ arrow svg) | `button.btn-play` l.1183-1188, `onclick=startQuiz()` |

### 1.4 Play (`#screen-question`, l.1196-1227)

Populated by `loadQuestion()` l.1632-1659.

| # | Text | Locator |
|---|------|---------|
| Q25 | `Question 1 of 3` | `#q-progress-label` l.1199 [short] [static-placeholder] -> `` `Question ${currentQ + 1} of ${challenge.questions.length}` `` l.1636 |
| Q26 | `0 pts` | `#q-score-badge` l.1200 [short] [static-placeholder] -> `` `${score} pts` `` l.1637 and l.1712 |
| Q27 | `Food & Water, Coffee` | `#q-category` l.1210 [short] [static-placeholder] -> `` `${challenge.category}, Q${currentQ + 1}` `` l.1638 [data]. Note the placeholder pattern (`, Coffee`) does not match the JS pattern (`, Q1`). |
| Q28 | `How many cups of coffee do Irish adults drink on average each day?` | `#q-text` l.1211 [static-placeholder] -> `q.text` [data] |
| Q29 | `20` (timer seconds) | `#timer-text` l.1217 [short] -> `timeLeft` in `updateTimerUI()` l.1675 |
| Q30 | `1`, `3`, `2`, `4` (option buttons) | `#options-grid .option-btn` l.1221-1224 [static-placeholder] -> `q.options[i]` [data] via `btn.textContent = opt` l.1653 |

### 1.5 Feedback toast, correct / wrong / time-up (`#feedback-toast`, l.1230-1241)

Set by `showFeedback(correct, title, fact)` l.1723-1733. Envie `#feedback-envie` pose = `celebrate` (correct) or `think` (wrong/time-up); bubble `#feedback-bubble` gets class `sky` only when correct.

| # | Text | Locator |
|---|------|---------|
| Q31 | `✓` / `✗` | `#feedback-icon` l.1231 (static `✓`), JS l.1724 |
| Q32 | `Well done!` | `#feedback-title` l.1235 [static-placeholder]; always overwritten by Q33-Q35 |
| Q33 | `` `+${pts} points!` `` (correct) | `selectAnswer()` l.1705; pts = max(30% of base, base x timeLeft/20), base = 100/150/200 by question index |
| Q34 | `Not quite…` (wrong; literal ellipsis character U+2026) | `selectAnswer()` l.1710 |
| Q35 | `Time's up!` (timer expired) | `timeUp()` l.1685 |
| Q36 | Feedback fact | `#feedback-fact` l.1236 (empty in HTML) -> `q.fact` via innerHTML l.1727 [data] |
| Q37 | `Continue` | `#btn-continue` l.1240 [static-placeholder]; always overwritten by Q38/Q39 |
| Q38 | `Next question` | `showFeedback()` l.1732 (questions 1-2) |
| Q39 | `Review answers →` | `showFeedback()` l.1732 (last question) |

### 1.6 Recap / reveal (`#screen-review`, l.1271-1282)

Built by `showReview()` l.1745-1799 from `userAnswers[]`.

| # | Text | Locator |
|---|------|---------|
| Q40 | `Quick recap` | `p.review-label` l.1273 [short] |
| Q41 | `Your answers` | `h2.review-title` l.1274 |
| Q42 | `` `Question ${i + 1}` `` | `.review-q-num` template l.1778 [short] |
| Q43 | `` `✓ +${a.pts} pts` `` (right) | `.review-result.correct` template l.1779 [short] |
| Q44 | `✗ Missed` (wrong or timed out) | `.review-result.wrong` template l.1779 [short] |
| Q45 | `` `${a.q}` `` question text | `.review-question` l.1782 [data] |
| Q46 | `⏱` + `Time's up, no answer` | `.review-answer-pill.timed-out` l.1753-1755 |
| Q47 | `✓` + `` `${a.correct}` `` (correct option reveal, shown after time-out and after wrong) | `.review-answer-pill.correct-reveal` l.1756-1758 and l.1769-1771 [data] |
| Q48 | `✓` + `` `${a.choice}` `` (user's right answer) | `.review-answer-pill.user-correct` l.1760-1763 [data] |
| Q49 | `✗` + `` `${a.choice}` `` (user's wrong answer) | `.review-answer-pill.user-wrong` l.1765-1768 [data] |
| Q50 | `` `${a.fact}` `` | `.review-fact` l.1784 [data] (same string as Q36) |
| Q51 | `Useful resources` | `h4` inside `.resources-block`, `renderResourcesHTML()` l.1504; block appended after the cards l.1789-1796 only if `challenge.resources` is non-empty |
| Q52 | `` `<strong>${label}:</strong> <a href=…>${url}</a>` `` or plain `` `${item}` `` | `renderResourcesHTML()` l.1500-1502 [data] (split on `\|`, `Label: domain.tld` pattern) |
| Q53 | `See my score →` | `button.btn-action` in `.review-footer` l.1280, `onclick=showResults()` |

### 1.7 Score (`#screen-score`, l.1247-1265)

Set by `showResults()` l.1801-1820, which also calls `markQuizDone()` (locks the day). Envie `#score-envie` pose set per tier.

| # | Text | Locator |
|---|------|---------|
| Q54 | `Your score` | `p.score-label` l.1249 [short] |
| Q55 | `0` | `#final-score` l.1250 [static-placeholder] -> `score` l.1804 |
| Q56 | `out of 300` | `#score-max` l.1251 [short] [static-placeholder] -> `'out of ' + TOTAL_MAX` = `out of 450` l.1805. Placeholder number (300) is stale. |
| Q57 | `Nice one!` | `#score-message` l.1255 [static-placeholder]; overwritten by tier msg (see 1.10) |
| Q58 | Tier sub-line | `#score-sub` l.1256 (empty in HTML) -> tier sub (see 1.10) |
| Q59 | `2 correct` | `#score-pill-correct` l.1261 [short] [static-placeholder] -> `` `${correctCount} correct` `` l.1806 |
| Q60 | `Avg today: 140 pts` | `#score-pill-avg` l.1262 [short]. **Never updated by JS. This hardcoded fake average is shown to every player.** |

### 1.8 Finish

| # | Text | Locator |
|---|------|---------|
| Q61 | `Finish` | `button.btn-action` in `#score-bottom` l.1289, `onclick=finishAndGoHub()` l.1825 (saves progress + streak bonus, then `index.html?hub=1`). `#score-bottom` is revealed by `showResults()` l.1818. |

### 1.9 Errors / empty states

There are none that a user sees. `loadChallenge()` l.1507-1526 swallows any fetch/JSON error and silently uses `FALLBACK_CHALLENGE`; no toast, no message. Streak bonus (`getStreakBonus()` l.1551-1573: 100 pts at 3-day streak, 250 pts at 5+) is added silently in `saveProgress()` l.1575-1585 with no user-facing copy anywhere in this file.

### 1.10 Quiz score-tier logic (`showResults()` l.1808-1812)

`pct = score / TOTAL_MAX` where `TOTAL_MAX = 450` (`Q_BASE_POINTS = [100, 150, 200]` l.1317-1318). Per-question points = `max(round(base * 0.3), round(base * timeLeft / 20))` l.1701, so a correct answer always earns at least 30% of base.

| Threshold | `#score-message` | `#score-sub` | Envie pose |
|-----------|------------------|--------------|------------|
| pct >= 0.85 (383+ pts) | `Outstanding!` | `You really know your stuff. Now let's turn that knowledge into action.` | `celebrate` |
| pct >= 0.55 (248-382 pts) | `Good effort!` | `` `You got ${correctCount} out of ${challenge.questions.length} right. Every game teaches something new.` `` | `flag` |
| else (0-247 pts) | `Good start!` | `Don't worry, the goal is to learn something surprising. You definitely did today.` | `think` |

Note: "Now let's turn that knowledge into action" still points at the removed action/pledge step (the pledge moved to the app home per the HTML comment at l.1285).

### 1.11 Inline data still in the file

| Table | Lines | Reachable? |
|-------|-------|------------|
| `FALLBACK_CHALLENGE` (Irish Coffee Day: title, badge, intro fact, `actionIntro`, 3 questions with facts, 4 `actions`) | l.1323-1356 | **Yes.** Used when `challenges.json` fails to load, or when no `?challenge=` id matches and no challenge has `date === today`. The `actionIntro` and `actions[]` fields are mapped but never rendered now that the action screen is gone (dead fields inside a live object). |
| `CHALLENGES` legacy object (`irish-coffee-day`, `earth-day`, each with questions + actions) | l.1361-1435 | **No.** Never referenced by any function. Pure dead weight; contains en-dashes (`40–50%`, `10–25%`). |
| `adaptJsonChallenge()` still maps `action_intro` and `actions[]` from JSON | l.1472, l.1480-1486 | Mapped but unused. |

### 1.12 Quiz skip list (internal, not user-facing)

- HTML comments: `SCREEN 0 — ALREADY PLAYED TODAY` l.1136, `SCREEN 1 — INTRO` l.1157, `SCREEN 2 — QUESTION` l.1194, `SCREEN 3 — SCORE` l.1245, `SCREEN 3b — REVIEW ANSWERS` l.1269, `The pledge step moved to the app home (Take an action).` l.1285, `Score bottom — lives OUTSIDE all screens…` l.1287, `Bottom nav — shown on score/action screens only` l.1292 (stale: nav is never shown), `injected by showReview()` l.1277.
- JS comments: constants block l.1313-1318, `FALLBACK CHALLENGE — hardcoded "Coffee" (last resort)` l.1321, `LEGACY DATA (kept for reference — challenges.json is primary)` l.1359, `Resources block at the end of the recap (May 2026 onwards)` l.1789, and section banners.
- CSS comments l.39, 54, 828 (`Finish button — dark with orange border, matches Sort It Out`), 845, 864, 880-881, 887, 1074.
- No `console.*` calls in this file.
- Dead JS: `goBack()` l.1839-1856 (never called; its `screen-action` case calls undefined `goBackToScore()`), `signOut()` l.1834-1837 (never called).
- Dead CSS: `#screen-action`, `.action-*`, `.btn-commit`, `.btn-action-back`, `.action-card-nothing`, `#screen-share`, `.share-*`, `.btn-finish`, `.btn-secondary` (no matching markup).

---

## Game 2: Sort It Out (`environmentle-sort-it-out.html`)

**Blocking note before the inventory.** The inline script calls `hasPlayedToday()` (l.1576), `markPlayedToday()` (l.2029), `getStreakBonus()` (l.2030) and the variable `_nk` (l.2030-2031). None of these are defined in this file, in `app.js`, or in `envie.js` (the only scripts the page loads besides gtag). The only definitions in the project are inside `environmentle-water-challenge.html`. As written, `startGame()` throws `ReferenceError: hasPlayedToday is not defined` on its first line, so the "Start sorting" button does nothing, and `finishAndGoHub()` would throw inside `saveProgress()` before navigating. The inventory below documents the copy as designed, but stages 2.2 onward are currently unreachable in the shipped file until those helpers are restored.

### 2.1 Chrome

| # | Text | Locator |
|---|------|---------|
| S1 | `Environmentle, Carbon Challenge` | `<title>` l.6 (does not say "Sort It Out") |
| S2 | `Environmentle logo` | `img[alt]` in `a.back-to-hub` l.1289 |
| S3 | `Environment` + `le` | `.em-wordmark` / `.em-le` l.1291 [short] |
| S4 | `Play. Learn. Act.` | `.em-tagline` l.1292 [short] |
| S5 | `Home` | `.app-nav-label` in `nav#game-bottom-nav` l.2079 [short] [dead] |
| S6 | `Games` | l.2083 [short] [dead] |
| S7 | `Learn` | l.2087 [short] [dead] (app.js rewrites to `Stories`) |
| S8 | `Companion` | l.2091 [short] [dead] |

Nav is `display:none` (CSS l.983) and forced hidden by `showScreen()` l.1562-1563.

### 2.2 Played-today gate (`#screen-played`, l.1299-1315)

Identical copy to the quiz. Only reachable via `startGame()` guard l.1576 (there is no `boot()` in this file, so nothing shows this screen on page load and nothing ever populates `#played-score`; it would always show `—`).

| # | Text | Locator |
|---|------|---------|
| S9 | `🌿` | l.1301 |
| S10 | `You've already played today` | `p.intro-label` l.1302 |
| S11 | `Come back` / `tomorrow` | `h1.intro-title` l.1303 |
| S12 | `One challenge per day, that's part of the fun. Your score from today:` | l.1304-1306 |
| S13 | `—` | `#played-score` l.1307 [em-dash] (never replaced in this file) |
| S14 | `pts` | l.1308 [short] |
| S15 | `← Back to games` | `a.btn-play` l.1312 |

### 2.3 Intro (`#screen-intro`, l.1320-1351)

All static; nothing is injected here.

| # | Text | Locator |
|---|------|---------|
| S16 | `Carbon Footprint` | `.category-badge` l.1323 [short] |
| S17 | `Sort It Out` | `.sub-badge` l.1324 [short] |
| S18 | `The Game` | `p.intro-label` l.1326 [short] |
| S19 | `Sort it` / `out` (h1, `out` italic/green) | `h1.intro-title` l.1327 |
| S20 | Envie speech bubble (pose `point`, hat `cap`), how-it-works: `You get **6 cards**, everyday things like a cheeseburger, a flight, an email. Drag them into order with the **highest CO₂ at the top**, lowest at the bottom. You have 3 tries.` | `p.dyk-text` inside `.bb.r2.tail-low` l.1331-1333 |
| S21 | `6 cards to sort` | `.meta-item` l.1340 [short] (hardcoded; `CARDS_PER_ROUND = 6`) |
| S22 | `3 tries` | `.meta-item` l.1341 [short] (hardcoded; `MAX_TRIES = 3`) |
| S23 | `Faster = more points` | `.meta-item` l.1342 [short] (only hint at the time bonus anywhere) |
| S24 | `Start sorting` (+ arrow svg) | `button.btn-play` l.1344-1349, `onclick=startGame()` |

### 2.4 Play (`#screen-game`, l.1357-1392)

Cards rendered by `renderCards()` l.1646-1735 from `currentCards[]` (6 random rows of `ALL_CARDS`).

| # | Text | Locator |
|---|------|---------|
| S25 | `0 pts` / `` `${roundScore} pts` `` | `#score-badge` l.1359; JS l.1596, 1902, 1917. **Invisible**: CSS `display:none` l.246-248 and `aria-hidden="true"`. |
| S26 | `Highest → Lowest CO₂` | `.game-title-label` l.1361 [short] |
| S27 | `3 tries left` | `.tries-label` l.1366 [short] [static-placeholder] -> `` `${triesLeft} ${triesLeft === 1 ? 'try' : 'tries'} left` `` in `updateTriesUI()` l.1619 |
| S28 | `① Highest CO₂` | `.arrow-label` l.1374 [short] |
| S29 | `↕` | `.arrow-icon` l.1375 |
| S30 | `Lowest CO₂ ⑥` | `.arrow-label` l.1376 [short] |
| S31 | `High` / `Low` | `.scale-label.top` / `.bot` l.1381, 1383 [short] (7px vertical text, `aria-hidden`) |
| S32 | `` `${card.emoji}` `` | `.card-emoji` l.1667 [data] |
| S33 | `` `${card.title}` `` | `.card-title` l.1669 [data] (single line, ellipsis) |
| S34 | `` `${card.sub}` `` | `.card-sub` l.1670 [data] (uppercase) |
| S35 | `` `${formatImpact(card.impact)} ` `` + `kg CO₂` | `.card-impact` + `.card-impact-unit` l.1673 [short] [data]; hidden until card is `revealed` |
| S36 | `` `${card.category}` `` | `.card-cat-label` l.1674 [short] [data] (uppercase; hidden once revealed) |
| S37 | `✓` / `✗` | `.card-result-icon` l.1697 (locked), l.1877 / l.1883 in `submitAnswer()` |
| S38 | `Close` | `button.card-back-close[aria-label]` l.1687 (back panel, only on final reveal) |
| S39 | `Check my order` | `#btn-submit` l.1390; reset in `startGame()` l.1601 and `retryAttempt()` l.1936 |
| S40 | (empty) | `#result-message` l.1389; `:empty` is hidden. Cleared l.1597, l.1933. |

### 2.5 Feedback after "Check my order" (`submitAnswer()` l.1864-1922)

| # | Text | Locator |
|---|------|---------|
| S41 | `🎉 Perfect! All 6 correct.` (all correct, any try) | `#result-message.correct-msg` l.1900 (hardcoded 6) |
| S42 | `` `${correct} of 6 correct, ${triesLeft} ${triesLeft === 1 ? 'try' : 'tries'} left. Move the wrong ones and try again.` `` (partial, tries remain) | `#result-message.wrong-msg` l.1905 (hardcoded 6) |
| S43 | `Try again` | `#btn-submit.retry` l.1907, `onclick=retryAttempt` |
| S44 | `` `${correct} of 6 correct. See the right order below.` `` (out of tries) | `#result-message.wrong-msg` l.1918 (hardcoded 6) |

Correct cards lock in place with `✓`; wrong ones get `✗`. No Envie on this screen.

### 2.6 Reveal (`showFinalReveal()` l.1942-1998)

Cards re-sorted into the true order, CO₂ values revealed, a `.card-detail` strip injected under each card that has `comparison` or `comment`.

| # | Text | Locator |
|---|------|---------|
| S45 | `` card.comparison `` | `.card-fact-text` l.1961-1964 [data] (textContent) |
| S46 | `🌿 Quick extra fact` | `button.card-info-btn` l.1970 [short]; flips card to back panel |
| S47 | `` `${card.comment}` `` | `p.card-back-text` in `.card-back-panel` l.1686 [data] (innerHTML) |
| S48 | `See my score` | `#btn-submit` l.1994, `onclick=showResults` (quiz uses `See my score →`, with arrow) |

### 2.7 Score (`#screen-score`, l.1398-1419)

Set by `showResults()` l.2003-2026. Envie `#score-envie` pose per tier.

| # | Text | Locator |
|---|------|---------|
| S49 | `Your score` | `p.score-label` l.1400 [short] |
| S50 | `0` | `#final-score` l.1401 [static-placeholder] -> `roundScore` l.2004 |
| S51 | `pts this round` | `p.score-max` l.1402 [short] |
| S52 | `Nice one!` | `#score-message` l.1406 [static-placeholder]; overwritten by tier msg (2.10) |
| S53 | Tier sub-line | `#score-sub` l.1407 (empty in HTML) -> tier sub (2.10) |
| S54 | `` `${correctCount}/6 correct` `` | `#score-pill-correct` l.1412 (empty in HTML) -> l.2021 [short] (hardcoded 6) |
| S55 | `` `${triesUsed} ${triesUsed === 1 ? 'try' : 'tries'} used` `` | `#score-pill-tries` l.1413 (empty in HTML) -> l.2022 [short] |

### 2.8 Finish

| # | Text | Locator |
|---|------|---------|
| S56 | `Finish` | `button.btn-action` in `.score-bottom` l.1417, `onclick=finishAndGoHub()` l.2041 |

### 2.9 Errors / empty states

None user-facing. `loadCards()` l.1461-1472 logs to console and silently falls back to `FALLBACK_CARDS`. No copy for "no cards", "fetch failed", or the undefined-helper crash described above.

### 2.10 Sort It Out score-tier logic (`showResults()` l.2007-2015)

Score per round (`calcScore()` l.1924-1929): `base = correct * 100`; `multiplier = 3 / 2 / 1` for 1st / 2nd / 3rd attempt; `timeBonus = 300` if the round took 30 s or less, else `max(0, 300 - (seconds - 30) * 8)`. Theoretical max = 6 x 100 x 3 + 300 = 2100. On a full-correct submit the multiplier uses `triesUsed`; on running out of tries it uses `MAX_TRIES` (3). Tiers are by `correctCount` and `triesUsed`, not by score.

| Condition | `#score-message` | `#score-sub` | Envie pose |
|-----------|------------------|--------------|------------|
| 6/6 and `triesUsed === 1` | `First try!` | `You nailed it on the first attempt. Impressive.` | `celebrate` |
| 6/6, tries 2-3 | `Well done!` | `` `You got all 6 right in ${triesUsed} ${triesUsed === 1 ? 'try' : 'tries'}.` `` (the singular branch can never fire here) | `celebrate` |
| 4-5 correct | `Almost there!` | `` `You got ${correctCount} out of 6 right. Keep playing to sharpen your instincts.` `` | `flag` |
| 0-3 correct | `Good start!` | `Carbon footprints are surprisingly tricky. Every game teaches you something new.` | `think` |

### 2.11 Inline data still in the file

| Table | Lines | Reachable? |
|-------|-------|------------|
| `FALLBACK_CARDS` (44 cards: title, sub, category, impact, emoji, comparison, comment) | l.1475-1520 | **Yes.** Used when `fetch('cards.csv')` throws or returns non-OK (e.g. opened as `file://`, or 404). Contains en-dashes in titles/comparisons (`Dublin–Holyhead Ferry`, `Dublin–Madrid Flight`, `NY–London Flight`, `Dublin–Belfast`). Several `comment` fields are terse note-style fragments rather than sentences (`Lower than Tokyo; half of London Olympics impact`, `Waste generates methane; a major impact-reduction opportunity`). Not itemised here per the brief (data, not UI). |
| `ACTION_THRESHOLD_KG = 10` | l.1522-1524 | **No.** Constant is never read. Leftover from the removed action step. |
| Per-card action table | (removed) | Comment at l.1526 says `Per-card actions moved to actions.json`. No inline action table remains. |

### 2.12 Sort It Out skip list (internal)

- `console.log` l.1467 `` `✅ Loaded ${ALL_CARDS.length} cards from cards.csv` `` and l.1469 `ℹ️ cards.csv not found, using built-in card data`.
- HTML comments: `SCREEN 0 — ALREADY PLAYED TODAY` l.1297, `SCREEN 1 — INTRO` l.1318, `SCREEN 2 — GAME` l.1355, `SCREEN 3 — SCORE` l.1396, `The pledge step moved to the app home (Take an action).` l.1422, `Bottom nav — shown on score/action screens only` l.2075 (stale).
- JS comments: data banner l.1428-1431, `Full card list — used when the game is opened locally as a file` l.1474, action-threshold comment l.1522-1523, `Per-card actions moved to actions.json` l.1526, drag/touch comments, `Hard guard — already played today → cannot restart` l.1575, `Inject card-detail sibling divs for comparison text + "Did you know?" CTA` l.1947 (button actually says `Quick extra fact`), and section banners.
- CSS comments l.79-81, 218-220, 224, 247, 306, 358, 387, 395, 405, 414, 441, 481, 498, 515, 527, 576, 625, 665-667, 757 (`Finish button — dark with orange border`; the Finish button actually uses `.btn-action`, orange fill), 774 (`Primary CTA — Take further action`, stale), 807-809 (`SCREEN 4 — ACTIONS`), 976-977, 983, 1041-1043 (`SCREEN 5 — SHARE`), 1149, 1166, 1187, 1235, 1561.
- Dead JS: `goBack()` l.2054-2071 (never called; has a `screen-action` case), `signOut()` l.2049-2052 (never called), `totalScore` l.1541 (accumulated, never displayed), `ACTION_THRESHOLD_KG`.
- Dead CSS: `#screen-action`, `.action-*`, `.btn-commit`, `.btn-action-back`, `.btn-finish`, `.btn-secondary`, `#screen-share`, `.share-*`, `.bottom-nav*` (no matching markup).

---

## Cross-game notes for the rewrite

1. **Envie voice.** Only three places are literally inside an Envie bubble: the intro fact (quiz, data-driven), the intro rules (Sort, static), the feedback toast (quiz), and both score screens. The score-tier lines already read as Envie talking (`You nailed it…`, `Don't worry…`). Everything else (result messages in Sort, review pills, meta rows, played gate) is neutral app voice.
2. **Copy that outlived the trim.** Quiz `Outstanding!` sub says `Now let's turn that knowledge into action` (action step removed). `#score-pill-avg` `Avg today: 140 pts` is a hardcoded fake statistic still rendered. `FALLBACK_CHALLENGE.actionIntro` and `.actions[]` are dead fields. `ACTION_THRESHOLD_KG` and both `goBack()` `screen-action` branches are dead.
3. **Hardcoded numbers in copy.** Quiz: `3 questions`, `Under 3 minutes` (real max is 60 s of timer), `450 pts max` (JS-driven, fine), `out of 300` placeholder (stale vs 450). Sort: `6` appears literally in S20, S21, S41, S42, S44, S54, and the tier subs; `3 tries` in S20, S22, S27 placeholder.
4. **Inconsistencies.** `See my score →` (quiz) vs `See my score` (Sort). Quiz `Review answers →` uses an arrow, `Next question` and `Continue` do not. `#q-category` placeholder pattern (`Food & Water, Coffee`) vs runtime (`Food & Water, Q1`). Page `<title>` says `Carbon Challenge` while badges say `Sort It Out` / `Carbon Footprint`. Sort's `.score-max` says `pts this round` (implies multiple rounds; there is one round per day). Both games share the identical played-gate copy (`One challenge per day, that's part of the fun.`) even though Sort is not a "challenge".
5. **Irish references in HTML/JS copy proper**: none in the UI strings themselves; all Irish flavour (Foynes, Joe Sheridan, Bewley's, Tayto, Guinness, Dublin routes) lives in the fallback data tables. No Irish idioms (`grand`, `sound`, etc.) in the chrome copy.
6. **Em-dashes.** Only the `—` score placeholder (both played gates, plus the quiz JS fallback). Ellipsis `…` in `Not quite…`. En-dashes exist only inside the dead `CHALLENGES` table and `FALLBACK_CARDS` data.
7. **Streak / bonus copy.** Both games silently add a streak bonus (100 at 3 days, 250 at 5+ in the quiz's `getStreakBonus()`); there is no on-screen mention of streaks or bonuses in either game file. `Faster = more points` is the only nod to the Sort time bonus.
8. **Accessibility strings.** Only two: `alt="Environmentle logo"` (both) and `aria-label="Close"` (Sort back panel). Drag handles, result icons, order scale and score badge are `aria-hidden`; the option buttons and cards have no aria labels.


# ===== game-water =====

# Text inventory: Water Challenge (`environmentle-water-challenge.html`)

File: `/Users/fabienmossiere/_Environmentle/app/environmentle-water-challenge.html` (4115 lines).
Covers HTML markup and the inline JS (lines 3029-4092). The inlined dataset (`<script id="water-data">`, lines 1510-3027) is a copy of `water-cards.json` and is NOT inventoried, but strings that the JS pulls from it are flagged as `[data]`.

Tags: `[short]` space-constrained, `[em-dash]` contains an em-dash, `[data]` composed from dataset fields, `[dead]` never shown or always overwritten, `[stale]` references removed content, `[hardcoded]` number typed in copy that has a JS constant (`ROUNDS_PER_RUN = 5`, `MAX_POINTS = 480`).

Run constants (lines 3042-3067): `ROUNDS_PER_RUN = 5`, `REALITY_AT = 3`, `POINTS_PER_CORRECT = 60`, `PERFECT_BONUS = 90`, `TIME_BONUS_MAX = 90`, `TIME_BONUS_FREE = 45` s, `TIME_BONUS_DECAY = 3` pts/s, `MAX_POINTS = 480`.

---

## 1. Chrome (every screen)

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 1.1 | `Environmentle, Water Challenge` | `<title>`, line 6 | browser tab |
| 1.2 | `A one-tap game about the fresh water hidden in everyday things. Higher or lower, water edition.` | `<meta name="description">`, line 7 | |
| 1.3 | `Environmentle logo` | `.back-to-hub img[alt]`, line 1234 | alt text |
| 1.4 | `Environment` + `le` | `.em-wordmark` / `.em-le`, line 1236 | wordmark, brand kit, do not rewrite |
| 1.5 | `Play. Learn. Act.` | `.em-tagline`, line 1237 | [short] brand tagline |
| 1.6 | (empty) | `#live` sr-only aria-live region, line 1493 | filled by `announce()` at each stage, see below |
| 1.7 | `Home` / `Games` / `Learn` / `Companion` | `#game-bottom-nav .app-nav-label`, lines 4099-4111 | [short] [dead] `.app-nav { display:none }` (line 1178) and `showScreen()` forces `display:none` (line 3624). Never visible. HTML comment above it ("shown on the score screen only", line 4095) is [stale]. |

Chrome: 5 live strings, 1 dynamic region, 4 dead.

---

## 2. Played-today gate (`#screen-played`, lines 1246-1265)

Shown instead of the intro when the day's run is used (`showPlayedScreen()`, line 3821), and again if a stale tab tries `newGame()` (line 3637).

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 2.1 | `💧` | first `div` in `.intro-top`, line 1248 | emoji, decorative |
| 2.2 | `You have already played today` | `p.intro-label`, line 1249 | |
| 2.3 | `Come back` / `tomorrow` | `h1.intro-title` with `<em>tomorrow</em>`, line 1250 | [short] display heading |
| 2.4 | `One run a day, that is part of the fun. Your score from today:` | inline-styled `p`, line 1252 | |
| 2.5 | `—` | `#played-score`, line 1254 | [em-dash] markup placeholder. JS (line 3823) also writes `'—'` when no score stored, so the em-dash is live in the no-score case. |
| 2.6 | `pts` | inline-styled `p`, line 1255 | [short] |
| 2.7 | `A fresh run unlocks at midnight.` | `#played-when`, line 1257 | [dead] always overwritten by 2.8 |
| 2.8 | `Your next run unlocks at midnight, ` + hoursToMidnight() + `.` | `showPlayedScreen()`, line 3824 | composed |
| 2.9 | `in under an hour` | `hoursToMidnight()`, line 3817 | fragment of 2.8 |
| 2.10 | `in about ` + N + ` hour` / ` hours` | `hoursToMidnight()`, line 3818 | fragment of 2.8 |
| 2.11 | `← Back to games` | `a.btn-play`, line 1262 | [short] link to `index.html?hub=1`; arrow is a literal `←` |
| 2.12 | `You have already played today. Your score was ` + (score or `not recorded`) + ` points.` | `announce()` in `showPlayedScreen()`, lines 3826-3827 | screen-reader only |

Played gate: 10 live strings (+2 fragments), 1 dead.

---

## 3. Intro (`#screen-intro`, lines 1271-1310)

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 3.1 | `Water Footprint` | `.category-badge`, line 1274 | [short] uppercase pill |
| 3.2 | `Higher or Lower` | `.sub-badge`, line 1275 | [short] uppercase |
| 3.3 | `The Game` | `p.intro-label`, line 1277 | [short] uppercase eyebrow |
| 3.4 | `Which one` / `drinks more?` | `h1.intro-title` with `<em>drinks more?</em>`, line 1278 | [short] display heading |
| 3.5 | `Every card shows the fresh water it takes to make **one thing**. Say whether the new item needs **more or less** than the one next to it. A run is **five rounds**. Get all five and you have a perfect run.` | Envie speech bubble `.bb.r2.tail-low > p.dyk-text`, lines 1283-1285. Envie: `<envie-mascot pose="point" hat="cap">` (line 1280) | How-it-works rules. Bold via `<strong>`. [hardcoded] "five rounds", "all five". |
| 3.6 | `That first one caught you out, so here is one more go today.` | `#retry-note` (markup empty + `hidden`, line 1290); set in `init()` line 4000 | shown only when the mulligan is available |
| 3.7 | `Your best run so far: ` + best + ` out of ` + ROUNDS_PER_RUN + `.` | `#start-best` (markup empty, line 1291); set in `init()` lines 3992-3994 | empty string when best is 0 (`:empty` hides it) |
| 3.8 | `5 rounds` | `.meta-item`, line 1295 | [short] [hardcoded] |
| 3.9 | `Once a day` | `.meta-item`, line 1296 | [short] |
| 3.10 | `480 pts max` | `.meta-item`, line 1297 | [short] [hardcoded] (MAX_POINTS) |
| 3.11 | `Start guessing` | `#btn-start-label` inside `#btn-start`, line 1300 | [short] primary CTA; overwritten by 3.12 in the mulligan case |
| 3.12 | `One more go` | `init()`, line 4001 | replaces 3.11 when `retryAvailable()` |
| 3.13 | `Figures are global averages, mostly from the Water Footprint Network. Real numbers move with the country, the farm and the season. Treat them as a compass, not a GPS.` | `p.micro`, lines 1306-1307 | sources footnote / caveat |
| 3.14 | `Round one of ` + ROUNDS_PER_RUN + `. ` + question text | `announce()` in `newGame()`, line 3664 | screen-reader only, fired on Start |

Intro: 14 strings.

---

## 4. Play (`#screen-game`, lines 1316-1403)

### 4a. Header

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 4.1 | `More or less water?` | `.game-title-label`, line 1319 | [short] |
| 4.2 | `Streak` | `.streak-label`, line 1322 | [short] uppercase 9px |
| 4.3 | `0` | `#streak`, line 1323 | [dead] default, JS writes the streak (3467, 3572) |
| 4.4 | `Best` | `.streak-label`, line 1326 | [short] uppercase 9px |
| 4.5 | `0` | `#best`, line 1327 | [dead] default, JS writes best (3468, 3573) |
| 4.6 | `Round 1 of 5` | `#progress-label`, line 1333 | [short] [dead] always overwritten by 4.7 |
| 4.7 | `Round ` + round + ` of ` + ROUNDS_PER_RUN | `renderProgress()`, line 3461 | [short] |
| 4.8 | `End this run and see your score` | `#btn-exit[aria-label]`, line 1339 | aria-label |
| 4.9 | `End run` | `#btn-exit`, line 1340 | [short] 11px pill; hidden until a run is active (`showScreen()` line 3628). Escape key is its twin (3935). |

### 4b. Question bubble (Envie `#game-envie pose="think" hat="hat"`, line 1347)

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 4.10 | `Does ` + right.short_title + ` need more or less water than ` + left.short_title + `?` | `renderRound()` writes `#question`, lines 3474-3475 | [data] short_title x2. Markup `#question` is empty. |
| 4.11 | `Both figures are for one kilogram of the product.` | `basisSentence('per-kg')`, line 3416, writes `#basis-note` | |
| 4.12 | `Both figures are for one of the thing, as labelled on the card.` | `basisSentence('per-unit')`, line 3417 | "one of the thing" reads oddly |
| 4.13 | `Both figures share the same basis.` | `basisSentence()` fallback, line 3418 | [dead] pool is always per-kg or per-unit (`POOL_ORDER`, line 3051) |

### 4c. Cards

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 4.14 | `Known item` | `#card-left[aria-label]`, line 1356 | aria-label |
| 4.15 | `Item to guess` | `#card-right[aria-label]`, line 1371 | aria-label |
| 4.16 | `litres` | `.card-impact-unit`, lines 1364 and 1379 | [short] 10px, static, both cards, never overwritten |
| 4.17 | `?` | `#card-right .card-value`, line 1379 | [short] placeholder; JS also writes `'?'` (line 3451) |
| 4.18 | `vs` | `p.versus`, line 1369 | [short] aria-hidden, uppercase letter-spaced |
| 4.19 | card.short_title / card.unit_label / card.category / card.emoji / litres(card.impact) | `renderCard()`, lines 3439-3454 | [data] Emoji is replaced by `EA.icon(...)` from app.js when present (line 3442); `catIcon()` map on 3436 (`beef`, `sprout`, `coffee`, `shirt`, `droplet`, `leaf`) are icon names, not copy. |

### 4d. Answer bar

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 4.20 | `Less` | `#btn-less .btn-word`, line 1394 | [short] uppercase |
| 4.21 | `left arrow` | `#btn-less .btn-hint`, line 1395 | [short] 9px keyboard hint. Keys also accept `L` (line 3950), not mentioned. |
| 4.22 | `More` | `#btn-more .btn-word`, line 1398 | [short] uppercase |
| 4.23 | `right arrow` | `#btn-more .btn-hint`, line 1399 | [short] 9px. Keys also accept `M` (line 3951). |
| 4.24 | `Next` | `#btn-next`, line 1401 (markup) and `setControls()` line 3520 | [short] shown after a correct answer that does not end the run |
| 4.25 | `See how you did` | `setControls()`, line 3519 | [short] replaces Next when phase is `lost` or `won` |

Play: 21 live strings, 4 dead/defaults.

---

## 5. Feedback after an answer (`answer()`, lines 3531-3589)

### 5a. Verdict card (`#verdict`, lines 1385-1389, hidden until answered)

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 5.1 | `Correct` | `#verdict-title`, line 3539 (class `correct-msg`, green) | [short] |
| 5.2 | `Not quite` | `#verdict-title`, line 3539 (class `wrong-msg`, red) | [short] |
| 5.3 | capitalise(right.sub) + ` takes about ` + litres(right.impact) + ` litres of fresh water.` | `#verdict-line`, lines 3541-3542 | [data] e.g. "One cotton t-shirt takes about 2,495 litres of fresh water." |
| 5.4 | right.comment | `#verdict-fact`, line 3543 | [data] card comment verbatim |

### 5b. Envie reacts in the question bubble

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 5.5 | `Spot on. ` + capitalise(thirsty) + ` is the thirsty one.` | `#question` on correct, line 3552; bubble gets class `sky`; Envie pose `celebrate` (3555) | [data] thirsty = short_title of the higher card. In Envie's voice. |
| 5.6 | `Not quite. ` + capitalise(thirsty) + ` is the thirsty one.` | `#question` on wrong, line 3552; Envie pose `think` | [data]. Note "Not quite" then appears twice on screen (bubble + verdict title 5.2). |
| 5.7 | (basis note cleared to empty) | line 3553 | |

### 5c. Screen-reader announcement (line 3575-3580)

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 5.8 | `Correct. ` / `Not quite. ` | `announce()` prefix, line 3575 | |
| 5.9 | right.short_title + `, ` + litres + ` litres ` + right.unit_label + `. ` | line 3575-3576 | [data] |
| 5.10 | `That is all five. Perfect run.` | line 3578 | [hardcoded] "five" |
| 5.11 | `Round ` + streak + ` of ` + ROUNDS_PER_RUN + ` complete.` | line 3579 | |
| 5.12 | `Run over. Final score ` + streak + ` of ` + ROUNDS_PER_RUN + `.` | line 3580 | |

Feedback: 11 strings.

---

## 6. Reality check overlay (`#reality`, lines 1482-1491; `showReality()` lines 3667-3679)

Modal, once per run, after the 3rd correct answer (`REALITY_AT = 3`, never on the last round). Converts the most recent non-household card into a household yardstick (`realityCheck()`, lines 3354-3380).

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 6.1 | `Reality check` | `p.reality-eyebrow`, line 1484 | [short] uppercase eyebrow |
| 6.2 | capitalise(subject.sub) + ` is ` + litres(subject.impact) + ` litres.` | `#reality-lead`, lines 3669-3670 | [data] e.g. "1 kg of beef is 15,415 litres." |
| 6.3 | `About ` + roughly(count) + ` ` + plural + `.` | `#reality-big`, line 3671 | [data] plural from `DATA.yardsticks[].plural` e.g. "About 193 full baths." |
| 6.4 | capitalise(yardstick.sub) + ` is ` + litres(yardstick.impact) + ` litres.` | `#reality-sub`, lines 3672-3673 | [data] |
| 6.5 | `Round ` + streak + ` of ` + ROUNDS_PER_RUN | `#reality-streak`, line 3674 | [short] uppercase |
| 6.6 | `Keep going` | `#reality-btn.btn-play`, line 1489 | [short] CTA |
| 6.7 | `Reality check. ` + lead + ` ` + big | `announce()`, line 3677 | screen-reader |

Reality check: 7 strings. No Envie on this overlay.

---

## 7. Score (`#screen-score`, lines 1409-1473; `showResults()` lines 3751-3811)

### 7a. Score top

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 7.1 | `Your score` | `#score-label`, line 1411 (markup) and line 3760 (non-perfect) | [short] uppercase |
| 7.2 | `Perfect run` | `#score-label`, line 3760 (perfect) | [short] |
| 7.3 | `0` | `#end-points`, line 1412 | [dead] default, JS writes total (3759) |
| 7.4 | `pts this round` | `#score-max`, line 1413 (markup) and line 3761 (JS writes the identical string) | [short] Wording clash: "round" here means the whole run, while "Round 1 of 5" means one comparison. |
| 7.5 | `Nice one!` | `#score-message`, line 1417 | [dead] always overwritten by 7.6-7.9 |
| 7.6 | `All five!` | `#score-message`, line 3770 | [short] [hardcoded] tier: perfect |
| 7.7 | `So close!` | line 3773 | [short] tier: 4 correct |
| 7.8 | `Good going!` | line 3776 | [short] tier: 2-3 correct |
| 7.9 | `Good start!` | line 3779 | [short] tier: 0-1 correct |
| 7.10 | `Five out of five. You have a real feel for this.` | `#end-lead`, line 3785 | [hardcoded] perfect lead |
| 7.11 | `You stopped after ` + streak + ` of ` + ROUNDS_PER_RUN + `. Your score is safe.` | line 3787-3788 | outcome `exit` (End run / Escape) |
| 7.12 | `That one caught you out on round ` + (streak+1) + ` of ` + ROUNDS_PER_RUN + `.` | line 3790-3791 | outcome `wrong` |
| 7.13 | ` Your best yet.` | appended to lead, line 3794 | fires when streak > 0 and streak >= best. Because `state.best` was already raised to this run's streak (line 3559), this triggers on a tie with the previous best too, not only a new record. |
| 7.14 | streak + ` of ` + ROUNDS_PER_RUN + ` correct` | `#score-pill-streak` (highlight), line 3762 | [short] |
| 7.15 | `Best ` + best + ` of ` + ROUNDS_PER_RUN | `#score-pill-best`, line 3763 | [short] |
| 7.16 | `Speed bonus ` + score.speed | `#score-pill-bonus`, line 3765 | [short] perfect only |
| 7.17 | `Max ` + MAX_POINTS + ` pts` | `#score-pill-bonus`, line 3766 | [short] non-perfect |

Envie on score: `#score-envie pose="flag" hat="hat"` (line 1415), pose set per tier (see section 10). Speech bubble `.bb.r5.tail-low` holds 7.1-7.13 message + lead.

### 7b. Recap panels (`.score-body`, lines 1429-1464)

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 7.18 | `From what you just saw` | `.panel-title`, line 1431 | panel 1 heading |
| 7.19 | comparison.line x2-3 | `#end-facts li`, `renderFacts()` line 3746 | [data] from `DATA.comparisons[].line`, ranked by cards met this run; 3 lines, or 2 if none matched |
| 7.20 | `Green, blue and grey water` | `#explainer-title`, line 1436 | [dead] overwritten by `DATA.explainer.title` (line 3834), which is currently the same text |
| 7.21 | explainer.intro | `#explainer-intro`, line 3835 | [data] |
| 7.22 | `Green` / `Blue` / `Grey` | `dt` labels built in `buildStaticContent()`, line 3836 | [short] JS literals (with colour swatches) |
| 7.23 | explainer.green / .blue / .grey | `dd`, line 3842 | [data] |
| 7.24 | `One thing to keep in perspective` | `.panel-caveat .panel-title`, line 1442 | caveat panel heading |
| 7.25 | `Farming uses somewhere between 70 and 87 per cent of the fresh water people consume, depending on whether you count what is withdrawn or what is used up. Everything you do at home sits inside the small remainder. That is why the list above starts with a food choice rather than a shorter shower.` | `.panel-caveat p`, lines 1444-1447 | [stale] "the list above starts with a food choice" refers to the removed actions list (was rank 1: swap a beef meal). Nothing above it now is a list of actions. |
| 7.26 | `It does not make your choices pointless. It does mean water is mostly a systems problem: what gets grown, where it gets grown, and who is allowed to pump. Shorter showers will not fix that on their own, and no game should pretend otherwise.` | `.panel-caveat p`, lines 1450-1452 | |
| 7.27 | `Every figure here is a global average with real uncertainty behind it. A few are disputed between sources. <code>water-cards.json</code> records which ones, and why.` | `.panel-caveat p.panel-intro`, lines 1455-1456 | sources footnote; exposes a filename in user-facing copy |
| 7.28 | `Read the longer version on the wiki` | `a.wiki-link`, line 1460 | href `https://wiki.the-uptake.com`, opens new tab |

### 7c. Finish

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 7.29 | `Finish` | `#btn-finish.btn-action` (orange primary), line 1469 | [short] goes to `index.html?hub=1` (line 4010). CSS class name still says `btn-action` and the CSS comment reads "Primary CTA — Take further action" (line 779), leftover from the removed pledge step. |
| 7.30 | `Play again` | `#end-replay.btn-finish`, line 1470 | [short] [dead] element is hidden unless the mulligan is available, and then JS writes 7.31 (line 3805). Never shown as "Play again". |
| 7.31 | `One more go` | `#end-replay`, line 3805 | [short] mulligan CTA |
| 7.32 | `Run over. ` + streak + ` of ` + ROUNDS_PER_RUN + ` correct, ` + total + ` points.` | `announce()`, lines 3809-3810 | screen-reader |

Score + recap + finish: 28 live strings, 4 dead.

---

## 8. Errors (`fail()`, lines 4078-4085; loader, 3152-3172; `init()`, 3983)

Rendered into the intro top (`#screen-intro .intro-top`) if the dataset cannot load or a pool is too small. Uses the legacy `.did-you-know` card, not the Envie bubble.

| # | Text | Locator | Notes |
|---|------|---------|-------|
| 8.1 | `Problem` | `fail()` -> `p.intro-label`, line 4081 | [short] |
| 8.2 | `The data` / `would not load` | `fail()` -> `h1.intro-title` with `<em>`, line 4082 | [short] |
| 8.3 | message (see below) | `fail()` -> `.did-you-know p.dyk-text`, line 4083 | raw error message shown to user |
| 8.4 | `Unknown error.` | DOMContentLoaded catch, line 4089 | fallback message |
| 8.5 | `Inline dataset missing` | `readInline()` throw, line 3154 | can surface via 8.3 |
| 8.6 | `HTTP ` + status | `loadCards()` throw, line 3166 | caught and falls back to inline; only surfaces if inline also fails |
| 8.7 | `Pool ` + pool + ` is too small to play` | `init()` throw, line 3983 | can surface via 8.3 |

Errors: 7 strings (developer-flavoured, would show to a user).

---

## 9. Skip list (internal, not user-facing)

- All `//` and `/* */` JS comments and `<!-- -->` HTML comments, including the dataset-refresh Python snippet (lines 1495-1509), the "pledge step moved" note (line 1476), and the stale "Bottom nav — shown on the score screen only" note (line 4095).
- CSS comments, including "Primary CTA — Take further action" (line 779), "SCREEN 4 — ACTIONS" (line 979) and "Take further action" (line 1140).
- Dead CSS for the removed action screen: `#screen-action`, `.action-*`, `.level-*`, `.btn-commit`, `.btn-action-back` (lines 978-1175). No markup uses them.
- `window.waterChallenge` debug surface (lines 4023-4041).
- localStorage keys `wc_*`, `env_player_name`, `env_progress_*`, `streak_*` (lines 3107-3113, 3877, 3907).
- `catIcon()` icon names (line 3436) and `getCatClass()` class names (line 3421).
- Google Analytics snippet (lines 8-15), font links, `hat`/`pose` attribute values (`cap`, `hat`, `point`, `think`, `celebrate`, `flag`).
- No `console.*` calls in this file.

---

## 10. Score-tier logic

Computed in `calcScore()` (lines 3693-3707) and rendered in `showResults()` (lines 3751-3811).

### Points
- Base: `correct * 60`.
- Perfect bonus: `+90` when `correct >= 5`.
- Speed bonus (perfect runs only): `90` if the run took `<= 45` s, then decays `3 pts/s`, hitting `0` at 75 s. Not paid on an early exit.
- Max: `5 * 60 + 90 + 90 = 480`.
- A separate daily-streak bonus (`dailyStreakBonus()`, lines 3873-3897) adds `+100` at 3 consecutive days and `+250` at 5 to the hub's `env_progress` total. It is never mentioned on screen in this game.

### Outcome states (`state.outcome`)
- `perfect`: 5 correct (line 3562).
- `wrong`: a wrong answer ends the run (line 3568).
- `exit`: End run button or Escape (line 3604).

### Tier table (`#score-message`, Envie `#score-envie` pose, `#score-label`)

| Condition | `#score-label` | `#score-message` | Envie pose | `#end-lead` |
|-----------|----------------|------------------|------------|-------------|
| outcome perfect (streak 5) | `Perfect run` | `All five!` | `celebrate` | `Five out of five. You have a real feel for this.` |
| streak = 4 | `Your score` | `So close!` | `flag` | exit: `You stopped after 4 of 5. Your score is safe.` / wrong: `That one caught you out on round 5 of 5.` |
| streak = 2 or 3 | `Your score` | `Good going!` | `flag` | same two patterns with N |
| streak = 0 or 1 | `Your score` | `Good start!` | `think` | same two patterns; on streak 0 wrong: `That one caught you out on round 1 of 5.` |

- ` Your best yet.` is appended to the lead when `streak > 0 && streak >= best` (line 3793). Since `best` already includes this run, equalling a previous best also triggers it.
- Bonus pill: `Speed bonus N` on perfect, else `Max 480 pts`.
- Streak pill: `N of 5 correct`. Best pill: `Best N of 5`.
- Mulligan: if the run died on round 1 with 0 correct and the retry has not been used today, `#end-replay` appears as `One more go` (lines 3803-3805) and gets focus; otherwise focus goes to `Finish`.

### Envie poses across the game
- Intro: `point` + cap (line 1280), static.
- Play: `think` + hat (line 1347); `celebrate` on a correct answer, `think` on a wrong one (line 3555); reset to `think` each new round (line 3472).
- Score: `flag` + hat default (line 1415), then per tier above.
- Reality check overlay, played gate and error state have no Envie.

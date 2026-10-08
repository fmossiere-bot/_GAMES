# Environmentle app

Daily climate habit app (environmentle.org). Vanilla HTML/CSS/JS PWA, no build step. Hub in `index.html` + `home.js` + `engine.js` + `tour.js`, games in `environmentle-*.html`, companion in `companion.php` + `api-proxy.php`, content in `challenges.json`, `actions.json`, `partners.json`, `cards.csv`, `water-cards.json`, `courses/*.json`.

## Voice: every user-facing word is Envie's

Envie is the mascot, a young astronaut who came to Earth to find out why the planet is changing, starting with Ireland. Anything a player reads, in the UI, the games, the challenge content, the stories, the actions, is written as Envie speaking. Read `_DESIGNS/envie-voice-and-text-rewrite-guide.md` before writing or changing any copy.

The short version:
- First person where Envie is on screen. "I picked these three for today", not "Envie picked these".
- He is finding things out, not teaching. "I checked the numbers, Ireland recycles 41% of its plastic. Not bad, but I've seen better."
- He reacts honestly to good and bad numbers, and notices ordinary Irish things with an outsider's eye.
- Short plain sentences, contractions welcome, no jargon, no corporate tone, no "gamified", no AI-generic filler.
- A little Irish now and then ("Dia duit", "maith thú", "fair play", "go on then"), never explained, never on every screen.

Hard rules, no exceptions:
- No em-dashes anywhere in copy or data. Use a comma, a full stop, or a new sentence.
- European English spelling (colour, organise, litre, programme).
- No emoji in the UI. Envie's reaction or a Lucide icon does that job.
- Facts, numbers and sources never change to fit the voice. Only how they are introduced or reacted to.
- Short strings stay short. If the voice does not fit a button, shorten it and flag it.
- The taglines "Play. Learn. Act." and "One step a day for the planet." stay as they are.

One name per thing: the games are "Daily quiz" (library version "Random quiz"), "Sort it out", "Water challenge" and "Bin day". Stories use sentence case.

Adding a game is an entry in `games.json` (the games index, twin of stories.json) plus its `environmentle-<id>.html`, `GAME_URLS` in index.html and `reconcileGames()` in engine.js. `games.json` drives the Games tab, so name, type, detail, artwork, icon and tone are written once. `playedKey` is the localStorage prefix the game writes its last-played date to; `art` is an SVG in `_images/games`.

Adding a story is two files: an entry in `stories.json` (the library index: title, category, cover, slides, minutes, published) and its `courses/course-<id>.json`. The Stories tab, the home lead card and engine.js all read `stories.json`, so nothing is duplicated in markup. Keep `slides` and `minutes` matching the course file. A story with no `cover` falls back to a tinted plate carrying its category icon, so it is fine to add one before the artwork exists.

**Categories are Fabien's call, always.** A story's `category` is also the label shown on it, so the filter chips and the card tags can never drift apart. Before adding a story, ask him which existing category it belongs to, or whether it needs a new one. Never invent a category, never rename one, and never force a story into a loose fit to avoid asking. The list is: Brands & Activism, Climate & Finance, Land & Nature, Circular Economy, Energy & Consumption, Positive Stories. Category labels are title case (each significant word capitalised), unlike story titles, which stay sentence case.

Run `python3 check-voice.py` before committing. It flags em-dashes, emoji and American spellings in user-facing files and data.

## Working notes
- Two challenges a day is the cap, and it counts plays: each game finished, each story read for the first time, each action pledged (`Engine.state().plays`). Re-reads are free. The hub enforces it, and `day-guard.js` enforces it on game pages opened by direct link. A new game page must load `engine.js` then `day-guard.js` with its own `data-played-key`, and be added to `reconcileGames()` in engine.js so it counts.
- Bin day (`environmentle-bin-day.html`) has a tester mode that is off for players. Opening the game once with `?tester=1` turns it on for that device (stored as `bd_tester`), `?tester=0` turns it off. It shows a tier picker on the intro, allows `?tier=N`, and lifts the once-a-day lock and the day cap for Bin day on that device. Tune belt speed and spacing in the `TIERS`, `SPEED` and `GAP` tables at the top of its script. Every rule in its `ITEMS` list comes from mywaste.ie (`_DESIGNS/bin-day/data/bin-day-items.json`), so change the voice, never the fact. The file was assembled once by `_DESIGNS/bin-day/make_game.py`; edit the game file directly from now on.
- The companion (companion.php, api-proxy.php system prompt, claims.json) has not had the Envie voice pass yet.
- The Games tab is the "Games library" canvas, screen 23 (`_DESIGNS/exports/games-screen/`). It reuses the Stories `.lib-*` classes plus a `.game-card` modifier for the taller 104px art plate. Rendered by `refreshGameCards()` from `games.json`: header with a live count, an "All types" drawer and Most recent / Quickest sort, 2-up cards, a dashed Soon card and a "More games on the way" tile.
- `.lib-foot` is the bottom spacer that keeps the last row clear of the tab bar on both library tabs. The tab screens are flex columns, so it needs `flex: none` plus `min-height`: a plain `height` gets shrunk to zero and the last cards hide behind the nav.
- Game artwork lives in `_images/games/*.svg` (320x200, flat, navy outline). The canvas README names them after the design's own game roster, which is not ours: `sort.svg` is two kerbside bins, so it belongs to **Bin day**, and `carbon.svg` is the 1/2/3 podium, so it belongs to **Sort it out**. `justice.svg` has no matching game yet.
- Card badges: New (newest `added` date, 21 days), Today (the game `Engine.suggestGame` would pick next), Played, Soon. The canvas also shows a "Best N" badge, which needs a per-game best score that nothing records yet.
- The Stories tab is the library layout from the "Stories, once there are a hundred" canvas (`_DESIGNS/stories-tab/screen-21.html` is the artboard). Rendered by `renderStoriesLibrary()` in index.html, styled by the `.lib-*` block at the end of app.css. The canvas had the category panel permanently open, which cost too much of the first screen: the categories now live in a drawer behind the "All categories" trigger (`_libCatsOpen`, `toggleStoryCats()`), while the sort pills stay visible. The trigger carries the active category's name and colour, and picking one folds the drawer away.
- `published` dates in stories.json came from the course file timestamps, not a real publishing log. Correct them when you know the real dates: they drive the "most recent" sort, the "5 days ago" line and which story wears the "New" label.
- Sort offers "Most recent" and "Shortest". The canvas also shows "Most read", which needs per-story read counts that nothing collects yet.
- `_DESIGNS/envie-text-inventory.md` lists every user-facing string by screen; `_DESIGNS/envie-rewrite-batch-*.md` are the reviewed rewrites.
- Only change text values, never keys, logic or variable names, unless asked.
- Sort it out (`environmentle-sort-it-out.html`) was reskinned on 27 Sep 2026 from the Carbon game handoff (`_DESIGNS/carbon-game/`). Its cards live in `cards.csv` (114 rows; `icon` is a Lucide name, new rows carry `source`, `source_url`, `confidence`). The page also embeds `FALLBACK_CARDS` and the Lucide `ICONS` it needs, generated from the CSV: after editing `cards.csv`, run `python3 _DESIGNS/carbon-game/sync_cards.py` to regenerate both (it fetches any new Lucide icon). A hand never deals two cards within 15% of each other (`MIN_GAP`) or more than two from one category. Scoring stays at 150 max. The day is stamped when the run ends, not on Finish. `carbon-sort/` is an old copy that nothing links to.
- Actions (1 Oct 2026): weekly cap is 3 (`ACTIONS_PER_WEEK`). `actions.json` has a `brands` type ("Brands and labels", 35 entries, hand-written, source kind `new`) about noticing certifications, packaging and claims; it is boosted by Sort it out, Bin day and two stories in `GAME_TYPES`/`STORY_TYPES`. Pledged actions take a private note (`Engine.setActionNote`, 280 chars): added from the action sheet, opened by tapping an entry in Profile > Actions pledged. Notes live in the local history only and are stripped before the Firestore upload (the privacy FAQ promises no free text is stored).

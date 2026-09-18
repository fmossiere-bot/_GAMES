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

One name per thing: the games are "Daily quiz" (library version "Random quiz"), "Sort it out" and "Water challenge". Stories use sentence case.

Adding a story is two files: an entry in `stories.json` (the library index: title, tag, category, topic, cover, slides, minutes, published) and its `courses/course-<id>.json`. The Stories tab, the home lead card and engine.js all read `stories.json`, so nothing is duplicated in markup. Keep `slides` and `minutes` matching the course file. A story with no `cover` falls back to a tinted plate carrying its topic icon, so it is fine to add one before the artwork exists.

Run `python3 check-voice.py` before committing. It flags em-dashes, emoji and American spellings in user-facing files and data.

## Working notes
- The companion (companion.php, api-proxy.php system prompt, claims.json) has not had the Envie voice pass yet.
- The Stories tab is the library layout from the "Stories, once there are a hundred" canvas (`_DESIGNS/stories-tab/screen-21.html` is the artboard). Rendered by `renderStoriesLibrary()` in index.html, styled by the `.lib-*` block at the end of app.css.
- `published` dates in stories.json came from the course file timestamps, not a real publishing log. Correct them when you know the real dates: they drive the "most recent" sort, the "5 days ago" line and which story wears the "New" label.
- Sort offers "Most recent" and "Shortest". The canvas also shows "Most read", which needs per-story read counts that nothing collects yet.
- `_DESIGNS/envie-text-inventory.md` lists every user-facing string by screen; `_DESIGNS/envie-rewrite-batch-*.md` are the reviewed rewrites.
- Only change text values, never keys, logic or variable names, unless asked.

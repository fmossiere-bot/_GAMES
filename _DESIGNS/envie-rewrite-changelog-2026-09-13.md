# Envie voice rewrite, what changed on 13 September 2026

One day's work: every player-facing word in the app now sounds like Envie, plus the companion redesign asks. Facts, numbers and sources unchanged throughout, checked by script at every merge. Detail per batch in `envie-rewrite-batch-1..8-*.md`, the string inventory in `envie-text-inventory.md`, the voice rules in `envie-voice-and-text-rewrite-guide.md` and `../CLAUDE.md`.

## Committed (a240cfd, "Rewrite all player-facing text in Envie's voice")

1. **Hub** (index.html, home.js, engine.js, sw.js, manifest.json): first person everywhere Envie is on screen, "Dia duit, I'm Envie. And you are?" on the name screen, one name per game (Random quiz, Sort it out, Water challenge), stories in sentence case with correct slide counts, "Sign out" became "Change name", tab lock notes and toasts in his voice.
2. **Tour, actions, action sheet, credit screen, FAQ** (tour.js, home.js, index.html): Envie introduces himself as an astronaut, FAQ rewritten as him speaking, the email contradiction fixed, "Apertvs" corrected to "Apertus", developer hints out of player copy, ledger no longer prints raw "pending".
3. **Three games**: intros, feedback ("That's it. +150 pts", "Ah, close but no."), score tiers ("Maith thú!", "I got most of them wrong when I first landed"), played-today gates with Envie waving instead of an emoji, the fake "Avg today: 140 pts" removed, stale action copy removed, water caveat fixed, raw errors replaced.
4. **Five stories** (courses/*.json): shorter sentences, 24 Envie asides (from 11), em-dashes out, one bold-phrase bug fixed.
5. **actions.json** (255) and **partners.json**: descriptions in his voice, 30 titles tightened to 70 characters, 57 em-dashes out.
6. **challenges.json**: 120 intros and 360 question facts rewritten, 242 em-dashes out, the "Today ..." opener kept where the quiz strips it on random plays.
7. **cards.csv, water-cards.json** and the inline copies in the two game pages: comments in his voice, explainer as Envie, fallback tables regenerated.

Guardrails added: `CLAUDE.md` (voice rules, one-name rule), `check-voice.py` (em-dashes, emoji, American spellings), a `voice` note in the meta of every content file.

## Not yet committed (companion pass, batch 8)

Files: companion.php, api-proxy.php, app.css, environmentle-quiz-game.html, environmentle-sort-it-out.html, environmentle-water-challenge.html.

- **Claims "Say this"**: Envie on the left beside the bubble, source chips and Copy and "Ask the Companion" removed, one CTA "Why it sounds right, and what's true" opens the detail with sources, share icon kept.
- **System prompt** (api-proxy.php): persona is Envie with a Voice section; output format, markers, sourcing and safety rules untouched; answers capped at two or three short paragraphs; AI-only answers name the organisation or report behind each figure in plain text.
- **Ask view**: "Dia duit, I'm Envie" empty state, "Ask Envie anything…" placeholder, "Let me have a look…" while thinking, plain errors with detail logged to the console, "Start again".
- **Wiki chips** under an answer open the article in place with "Back to the answer"; the Wiki tab is hidden; the proxy now returns the page slug with each source.
- **Claim chip** on the empty state opens the Claims tab filtered to Cars & flying instead of asking the AI.
- **Favicons**: companion.php pointed at old March icon files, now the same set as the hub; the three game pages had none and now carry them.

## Upload list for the companion pass

companion.php, api-proxy.php, app.css, environmentle-quiz-game.html, environmentle-sort-it-out.html, environmentle-water-challenge.html.

## Still open

- claims.json voice: generated from the wiki myth pages by the build action, so it belongs in the wiki repo (the live say line with an em-dash comes from there).
- Push notification copy in functions/index.js (file has other uncommitted changes).
- Emoji still used as icons on story visual slides and sort-game cards (a player change, not copy).
- Streak bonus is added silently in the games.
- Model em-dashes: the prompt forbids them, but a one-line strip in the proxy would make it certain.
- cards.csv is fetched with default caching, so returning players may see old comments until their cache refreshes.

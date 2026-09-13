# Envie rewrite, batch 1: the hub shell

Applied 2026-09-13 (Fabien: "go ahead"). Covers the name screen, home, Games tab, Stories tab, story player, profile, prompts and push fallbacks (index.html, home.js, engine.js, sw.js, manifest.json). Tour, actions screen, action sheet, credit screen and FAQ are batch 2. Games are batch 3.

Rules followed: Envie speaks in first person wherever he is on screen. Contractions are fine, he talks like a person. No em-dashes. European spelling. Short strings stay short. Facts and numbers untouched. "Keep" means the current text already works.

## A. Naming pass (applied as proposed)

Each game is named up to four ways today. Proposed single names, used everywhere in the hub and later in the game pages:

| Game | Today | Proposed name | Short tag |
|---|---|---|---|
| Quiz | Random challenge / Random quiz / Quiz | **Daily quiz** (the day's challenge title stays as the card title when there is one; the library and weekend version is "Random quiz") | Quiz |
| Sort | Sort it out / Carbon challenge / Sorting game / the carbon sorting game | **Sort it out** | Carbon |
| Water | Which one drinks more? / Water challenge / Water game / the water challenge | **Water challenge** ("Which one drinks more?" can stay as the intro line inside the game, it is a good Envie question) | Water |

Stories: sentence case everywhere ("Climate wins in 2025", not "Climate Wins in 2025"), and the slide counts corrected to match the JSON (Data centres 12, Climate and finance 9). Today the title, tag and count live in three places (Stories tab markup, ALL_STORIES, course JSON). I will align all three by hand in this batch and note it as a later cleanup.

Credit button: one verb, **Use my credit** (today: Spend my credit / Use my credit / Plant it).

## B. Shell, manifest, push

| # | Now | Proposed |
|---|---|---|
| 0.1 | Environmentle, Play your part | Keep |
| 0.5 manifest description | Small games. Real climate action. | One step a day for the planet, with Envie. |
| 0.7 push fallback body | Your daily challenge is ready 🌿 | Today's challenge is in. Envie is waiting on the home screen. |
| 0.11 tee prints | BE ECO FRIENDLY NOT EGO CENTRIC / MAKE SCIENCE GREAT AGAIN | Keep, design asset |

## C. Name screen (Envie with bubble)

| # | Now | Proposed |
|---|---|---|
| 1.1 bubble | Hi, I'm Envie. I'll tag along. | Dia duit, I'm Envie. And you are? |
| 1.2 | Welcome | Keep |
| 1.3 | What do we call you? | What do I call you? |
| 1.4 | No account needed. Just your name so we can make it feel a bit more personal. | No account, just a first name. I like to know who I'm exploring with. |
| 1.5 placeholder | Your first name | Keep |
| 1.6 | No email, no password, no fuss. | Keep |
| 1.7 | Let's go | Keep |

## D. Home

Header and title (2.2 to 2.5): keep. "Good morning, Name", "Today, pick one", "One more?", "Done for today" already read as Envie.

Notification prompt:

| # | Now | Proposed |
|---|---|---|
| 2.9 | 🔔 Never miss a daily challenge | Want a nudge in the morning? |
| 2.10 | Get a gentle nudge each morning when your new challenge is ready. One notification a day, nothing else. | I'll send one message a day when the new challenge is in. Nothing else, promise. |
| 2.11 | Not now | Keep |
| 2.12 | Yes please! | Go on then |

Install banner:

| # | Now | Proposed |
|---|---|---|
| 2.14 | Add us to your home screen | Keep me on your home screen |
| 2.15 | No app install needed. | No app store, nothing to download. |
| 2.16, 2.18 steps | Tap the Share icon... / Select Add to Home Screen | Keep |
| 2.19 | Dismiss | Not now |
| 2.20 | Done | Keep |

Three-way strip:

| # | Now | Proposed |
|---|---|---|
| 2.24 | All played today | Keep |
| 2.26 | Read one again | Keep |
| 2.28 | Browse the list | Pick your own |
| 2.29 done tile names | Quiz / Carbon challenge / Water challenge | Quiz / Sort it out / Water challenge |
| 2.37 hint | Envie picked these for today. Choose your own game or story from the menu below. | I picked these three for today. Or choose your own from the tabs below. |
| 2.38 hint, day done | Two challenges is the daily cap. The games and stories tabs stay open, and Envie is back tomorrow. | Two a day is the cap. Games and Stories stay open, and I'll have new picks tomorrow. |
| 2.39 toast | That is two for today / One a day is the rhythm. Envie will have this one for you tomorrow. | That's two for today / One a day is the rhythm. I'll keep this one for tomorrow. |

Lead card, credit variant:

| # | Now | Proposed |
|---|---|---|
| 2.40 | Action · N credit(s) ready | Keep |
| 2.41 | Turn 10,000 points into a real tree | Keep |
| 2.42 | Your points became something that goes in the ground. Pick where it goes and we send it to the partner. | Your points just became something real. Pick where it goes and I'll pass it to the partner. |
| 2.43 | Spend my credit | Use my credit |
| 2.44 | Others | Other actions (flag: ghost button, check it fits beside the main one) |

Lead card, action variant:

| # | Now | Proposed |
|---|---|---|
| 2.45 | Suggested action | Keep |
| 2.46 | Type · easy (raw lowercase) | Type · Easy (read the label from actions.json meta.levels, tiny code change) |
| 2.49 | Read more | Keep |
| 2.51 | Not this one, show me another | Keep |

Lead card, story variant: keep all (Today's story / Read / +75 pts). Flag: +75 is hardcoded, should read STORY_POINTS.

Lead card, done variant:

| # | Now | Proposed |
|---|---|---|
| 2.57 | Streak kept | Keep |
| 2.58 | Game, story and action. All three today. | Game, story and action. The full set today. |
| 2.59 | Two today. That is the rhythm. | Two today. That's the rhythm. |
| 2.60 | Envie keeps the rest for tomorrow. The companion is always open if you want to ask something. | I'll keep the rest for tomorrow. If something's on your mind, come and ask me. |
| 2.61 | Open the companion | Keep for now (companion pass later) |

Lead card, game variant:

| # | Now | Proposed |
|---|---|---|
| 2.62 to 2.65 pills | Today's game / One more game / Weekend game / Suggested game | Keep |
| 2.66 engine names | Random quiz / Carbon challenge / Water challenge | Random quiz / Sort it out / Water challenge |
| 2.67 meta | Quiz · 3 questions / Sorting game · 6 cards / Higher or lower · 5 rounds | Quiz · 3 questions / Carbon · 6 cards / Higher or lower · 5 rounds |
| 2.69 | Play | Keep |
| 2.70 | All played today | Keep |
| 2.71 | Every game is done for today. | All three games played. Fair play. |
| 2.72, 2.73 | Next challenge tomorrow / Games | Keep |

Below the lead and community:

| # | Now | Proposed |
|---|---|---|
| 2.74 | Or a smaller action today | Keep |
| 2.75 eyebrow | Suggested action (same as the pill above it) | One small action |
| 2.77 | Pledge today · +N pts | Keep |
| 2.78, 2.80 | Action pledged / Pledged today · streak kept | Keep |
| 2.81 to 2.83 credit bar | N credits held · next at X pts / X / Y pts to your first impact credit | Keep |
| 2.84, 2.85 | Together so far / Players, Games played, Actions pledged, Credits used | Keep |

## E. Bottom nav: keep (Home / Games / Stories / Companion).

## F. Games tab (Envie pointing, in the hero)

| # | Now | Proposed |
|---|---|---|
| 5.1 | 4 games · Free to play | Three games · Free to play (the fourth is still in the workshop) |
| 5.2 | GAMES | Keep |
| 5.3 | Small games. Real climate action. Pick one and go. | Short games, real numbers. Pick one, I'll keep score. |
| 5.4, 5.5 | Quiz / Random challenge | Quiz / Random quiz |
| 5.7, 5.8 | Sorting game / Sort it out | Carbon / Sort it out |
| 5.10, 5.11 | Water game / Which one drinks more? | Water / Water challenge (see naming decision) |
| 5.6, 5.9, 5.12 chips | 3 questions · 450 pts · ~2 min etc. | Keep |
| 5.13, 5.14 | Greenwasher / In the workshop | Keep |

## G. Stories tab (Envie waving, eco tee)

| # | Now | Proposed |
|---|---|---|
| 6.1 | Short stories · Free | Keep |
| 6.2 | STORIES | Keep |
| 6.3 | Short, visual stories on climate and sustainability. No jargon, no overwhelm. | Things I've found out since I landed, written down short. Five to eight minutes each, no jargon. |
| 6.4 | Available now | Ready to read |
| 6.5 to 6.9 cards | tags, titles, counts | Sentence case titles, counts fixed (12 and 9), otherwise keep |
| 6.10 | Coming soon | Still writing |
| 6.11, 6.12 | Food waste and composting / Talking about climate change | Keep |
| 6.13 to 6.17 ALL_STORIES | Title Case copies | Same values as the tab, sentence case, counts fixed |

## H. Story player and completion (Envie with flag on completion)

| # | Now | Proposed |
|---|---|---|
| 7.3 | swipe to navigate | swipe to turn the page |
| 7.4 alert | Could not load this course. Please try again. | I couldn't load that story. Give it another go in a moment. |
| 7.9 | Story complete | Keep |
| 7.10 | +75 / pts added to your score | Keep |
| 7.11 | You've read this one before, no new points. | You've read this one before. No new points, still a good read. |
| 7.14 | Done | Keep |
| 7.15 | Next story: Title → | Keep |

## I. Profile (Envie peeking as avatar)

| # | Now | Proposed |
|---|---|---|
| 8.2 | Profile | Keep |
| 8.3 | Sign out | Change name (that is what the button does: clears the name and returns to the name screen). Icon changed to a pencil. |
| 8.5 | Player · Level N | Keep |
| 8.6 stats | Score / Games / Pledged / Streak | Keep |
| 8.7 ready | Milestone reached / N credit(s) ready to plant / 10,000 points became one real thing in the ground. Pick where it goes and we send it to the partner, you get the photo back. / Use my credit / Save it | Milestone reached / N credit(s) ready to plant / 10,000 points, one real thing in the ground. Pick where it goes, I'll pass it to the partner, and you get the photo. / Use my credit / Keep it for now |
| 8.8 progress | Impact credits / Turn points into planting / N planted / N points to your next credit / ≈ N games / 1 credit funds a native tree, a metre of hedgerow or a pollinator patch with our partners. | Keep the first five. Last line: One credit plants a native tree, a metre of hedgerow or a patch for pollinators, through our partners. |
| 8.9 | Your milestones | Keep |
| 8.11 | Actions taken | Actions pledged |
| 8.12 | Browse actions | Keep |
| 8.13 empty | Nothing pledged yet. Envie will suggest one when the week is far enough along. | Nothing pledged yet. I'll suggest one later in the week, once you've played a bit. |
| 8.15, 8.16 | Help / How the app works, with Envie | Keep |
| 8.17, 8.18 | FAQ / Settings | Keep |
| 8.19 | Account / Notifications / Privacy | Left as they are, still inert. Decision pending: hide, or wire Notifications up. |
| 8.20 toast | Saved / Your credit stays on your profile until you use it. | Kept / Your credit stays here until you want it. |

## J. Shared helpers (dates, "tomorrow", weekday): keep.

## K. Not copy, but found in this batch (your call)

- Games played today are no longer ticked on the Games tab (the code targets old class names). Small fix.
- Home lead "+75 pts" for stories is hardcoded.
- The three story lists should become one. I align them by hand now; a later cleanup can make ALL_STORIES the single source.
- Dead code with copy in it (old recent-games list, old story toast, GAME_DEFS descriptions) can go whenever convenient. GAME_DEFS descriptions are the only prose about each game and are never shown; worth reusing on the Games tab cards if you want a line under each title.

## M. Added after the inventory (new share feature and tab lock notes)

| Now | Proposed |
|---|---|
| Two challenges done today. The games open again tomorrow. | Two done today. I'll open the games again tomorrow. |
| Two challenges done today. The stories open again tomorrow. | Two done today. I'll open the stories again tomorrow. |
| Share today / Share my progress / Share what you planted | Keep |
| Share texts ("I just turned 10,000 Environmentle points into something real in the ground...") | Keep. These are the player speaking, not Envie, since the player is the one posting. |
| Copied / Paste it wherever you like. | Keep |

## L. Guardrails added with this batch

- `CLAUDE.md` in the app folder: the voice in ten lines, hard rules, one-name-per-thing, pointer to the guide. Loaded by every Claude session in this folder.
- A `voice` note in the meta block of actions.json, partners.json, challenges.json, water-cards.json and each course JSON.
- `check-voice.py`: flags em-dashes, emoji and American spellings in user-facing source and content data. Hub files are clean; the games and content files show the debt the later batches will clear.

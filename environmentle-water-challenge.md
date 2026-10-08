# Water challenge

A hyper-casual browser game about the fresh water hidden inside everyday things.
No build step, no framework, no dependencies. Laid out the same way as Sort it
out (the Carbon Challenge): one self-contained HTML file in `app/`, one data
file beside it.

| File | What it is |
|---|---|
| `environmentle-water-challenge.html` | The whole game: markup, styles and logic in one file, plus the inline dataset fallback |
| `water-cards.json` | 146 cards (138 in play), the explainer, the reality-check yardsticks, 10 everyday dilemmas, 6 actions (no longer read by the game, see below), 11 comparison lines |
| `environmentle-water-challenge.md` | This file |

It is reachable from the hub in three places, like every other game:

- `games.json` has the `water` entry that draws its card on the Games tab
  (`playedKey: "wc_last_played_"`, detail "5 rounds").
- `GAME_URLS.water` in `index.html` points at the game file.
- `engine.js` reads `wc_last_played_<nk>` to count it towards the day cap and
  to decide whether to suggest it ("Higher or lower · 5 rounds").

The page loads `engine.js` then `day-guard.js` with
`data-played-key="wc_last_played_"`, so opening it by direct link still
respects the two-plays-a-day cap.

## The mechanism, in three sentences

Two cards sit one above the other. The top card shows an item with its water
footprint revealed, the bottom card shows a new item with the number hidden, and
you say whether the new one needs more or less water than the one beside it. Get
it right and the bottom card slides up to become the new top card and the run
moves on; get it wrong and the run ends there.

Two things are layered on top. Once per run, after the third correct answer, the
game interrupts with a full-screen **reality check** that converts something you
just met into household terms, for example one kilogram of roasted coffee against
days of home water use. The end screen shows your points, then the comparison
lines for the items you actually saw, the green/blue/grey explainer and a caveat
panel to read, and a **Finish** button back to the hub. There is no pledge step
in the game any more: pledging an action now happens on the app home (see
"Actions moved to the home screen").

Envie is on every screen: pointing on the intro, thinking in the question bubble
during a round (switching to `celebrate` or `think` when the answer lands), and
on the score screen with a pose that follows the result.

## A run is five rounds, once a day

`ROUNDS_PER_RUN` at the top of the game script is the only knob. It is 5.

A run ends in exactly one of three ways:

| Ending | What happens |
|---|---|
| Five correct | Perfect run. The score screen says so and pays the perfect and speed bonuses. |
| One wrong | The run stops there, with whatever you had scored. |
| **End run** pressed | The run stops there, with whatever you had scored. |

The reality check fires after `REALITY_AT` correct answers, which is 3, and
`realityDue()` refuses to fire it on the final round: a run ending and a modal
opening in the same beat reads as a bug. That same boundary is where the card
pool switches, so a run is three rounds of one basis and two of the other. Which
basis leads is drawn per run by `shuffledPoolOrder()`, so two runs in a row do
not open on the same kind of question. The pool never changes mid-chain, so the
units rule below is untouched.

### One play a day, with a first-question mulligan

Same gate as Sort it out, same key shape, same `YYYY-MM-DD` local-day boundary:

| Key | Meaning |
|---|---|
| `wc_last_played_<nk>` | Day of the last completed run. Mirrors `sio_last_played_<nk>`. |
| `wc_last_score_<nk>` | Points from that run, shown on the played screen. |
| `wc_first_loss_<nk>` | Set when the last run today ended on round one with nothing scored. |
| `wc_retry_used_<nk>` | Set the moment the extra go is taken. |

The exception the owner asked for: go out on the very first question with a score
of zero and you get **one** more attempt that day. `retryAvailable()` checks
for it, and the score screen then shows a **One more go** button next to
Finish. Taking it spends `wc_retry_used_<nk>` immediately, so closing the tab
does not buy a third go, and going out on question one of the retry does not
either. Losing on round two or later never grants a retry.

Coming back after the day is spent shows `#screen-played`: Envie waving, "Come
back tomorrow", today's score and roughly how many hours until a fresh run
unlocks at midnight, with a **Back to games** button. The hub reads the same
keys to put the Played badge on the Water challenge card, and it accounts for
the mulligan so the card stays playable while the spare attempt is still on
the table.

A missing, unreadable or corrupt stored value always means "you may play". A
storage failure must never lock anyone out.

### Points

A perfect run is worth **150**. The intro screen says so ("150 pts max"), and
the score screen shows "Max 150 pts" on any run that is not perfect.

```
5 correct × 20             =  100
+ perfect bonus                25
+ speed bonus, under 45s       25
                            -----
maximum                       150
```

The constants live in the SCORING block of the game script:
`POINTS_PER_CORRECT = 20`, `PERFECT_BONUS = 25`, `TIME_BONUS_MAX = 25`,
`TIME_BONUS_FREE = 45`, `TIME_BONUS_DECAY = 1`. `MAX_POINTS` is derived from
them. The run was earlier scaled to 480 (60 / 90 / 90); it was cut to 150 so a
water run sits on the same scale as the app's home points, where a credit is
earned every 1,500 points.

The speed bonus is paid on perfect runs only. It pays the full 25 up to and
including 45 seconds, then drops by 1 point a second, so it empties in 25
seconds and reaches zero at 70. A perfect run therefore scores 150 at its
fastest and 125 from 70 seconds on, with everything between on a straight
line. Paying it on an early exit would make quitting on round one worth
points, which is silly.

Worked examples, for anyone changing the constants again:

| Run | Score |
| --- | --- |
| 5 correct in 45s or less | `5×20 + 25 + 25` = **150** |
| 5 correct in 60s | `5×20 + 25 + 10` = **135** |
| 5 correct in 70s or more | `5×20 + 25 + 0` = **125** |
| 3 correct | `3×20` = **60** |
| 0 correct | **0** |

`saveProgress()` writes the run once into the shared `env_progress_<nk>`:
`totalScore` goes up by the run's points plus the shared daily streak bonus,
and `sessionsPlayed` goes up by one. `actionsPledged` is carried over
untouched. The daily streak bonus (`dailyStreakBonus()`, key `streak_<nk>`)
is shared with Sort it out and idempotent within a day: **25** from a three-day
streak, **50** from five.

`wc_best_streak_<nk>` survives, but "best" means best score out of five. A
value left behind by the earlier endless build can be larger than a run can ever
be, so `loadBest()` clamps it to `ROUNDS_PER_RUN` and writes the clamped value
back once. "Best 23 of 5" would be nonsense.

### Actions moved to the home screen

The score screen used to end with **Take further action**, opening a pledge
screen (`#screen-action`) with four of the six water actions. That screen is
gone. The markup now carries a single comment where it was ("The pledge step
moved to the app home (Take an action)"), and the game no longer reads
`DATA.actions`.

Where the water actions went:

- They were copied into the app-wide `actions.json`, whose `meta.generated_from`
  lists `environmentle-water-challenge.html DATA.actions` as a source. There they
  carry the `water` type and are pledged from the home screen's Take an action,
  under the home engine's rules (each pledge counts as a play towards the day
  cap).
- Playing the Water challenge nudges which actions the home suggests:
  `GAME_TYPES.water = ['water', 'food']` in `engine.js` bumps both types in the
  player's affinities.

The `actions` array (with its `rank` and `basePoints` 250 to 100) is still in
`water-cards.json` and the inline copy. It is harmless but no longer used by
this game; it is reference data for `actions.json`. If the two ever disagree,
`actions.json` wins.

### Score screen, top to bottom

| Part | Varies between runs? |
|---|---|
| Label ("Your score" or "Perfect run"), points, Envie's pose and message | Yes, with the run |
| Pills: `N of 5 correct`, `Best N of 5`, and either `Speed bonus N` (perfect) or `Max 150 pts` | Yes |
| "From what you just saw" comparison lines | Yes. Up to three, ranked by how many of the cards you actually met they mention, ties broken by a stored rotation counter; falls back to two when none match |
| Green, blue and grey explainer | No. The dataset holds one version of it |
| "One thing to keep in perspective" caveat, with a link to the wiki | No. Fixed copy |
| Bottom: **Finish** (orange `.btn-action`, back to `index.html?hub=1`) and, only when the mulligan applies, **One more go** (outlined `.btn-finish`) | Only the second button |

The buttons sit **below** the reading, not above it: the score first, then what
it meant. `app-nav` stays hidden on every screen, as in the other games.

The explainer is deliberately left alone: there is one version of it in
`water-cards.json` and rotating it would mean inventing copy that no source
backs. If more explainer framings are wanted, they belong in the dataset first.

### Answering: swipe the card

Since 27 Sep 2026 the main way to answer is to **swipe the mystery card**:
right for more, left for less. `initSwipe()` handles it with pointer events,
so it works for touch, pen and mouse.

- A drag only counts once it is clearly sideways (8 px, and more sideways than
  vertical). A mostly vertical drag is left to the browser, so the card area
  still scrolls. The card carries `touch-action: pan-y` for the same reason.
- While dragging, the card follows the finger with a slight tilt (no tilt under
  reduced motion), and a full-card tint fades in: navy "← Less" or blue
  "More →". Its opacity is `--swipe`, from 0 to 1 as the drag nears the
  threshold.
- Letting go past `SWIPE_THRESHOLD` (28 per cent of the card's width), or a
  quick flick (`SWIPE_FLICK`, 0.5 px/ms over at least 40 px), commits. The
  card springs back and the answer is revealed on it. Short of that, it just
  springs back and nothing is answered.
- The card is only draggable while a question is open: `setControls()` toggles
  the `swipeable` class.

The footer keeps compact **← Less** and **More →** buttons either side of a
"Swipe the card" hint. They are the fallback for anyone who cannot or would
rather not drag, and they keep the game usable with switch access and screen
readers. The app skin (`.skin.g-water .btn-submit` in `app.css`) gives them
their navy pill; the game's own `.btn-submit.alt` only sizes them. Once the
answer is shown, a full-width orange **Next** replaces all three. Next moves on
to the next round, opens the reality check, or goes to the score screen,
depending on where the run is (`proceed()`).

### Difficulty: the gap closes as the run goes on

Pairs used to be drawn at random from the whole pool, above the 10 per cent
near-tie floor. The typical question compared things about 5 times apart per
kg and 12 times apart per item, and half the per-item questions were 10 times
apart or more (a car against a toilet flush). Nobody got those wrong.

Each round now only pairs cards whose ratio (bigger over smaller) sits inside a
band, `RATIO_BAND_BY_ROUND` in the game script:

| Round | Ratio band | Feels like |
|---|---|---|
| 1 | 1.6 to 3 | a fair warm-up |
| 2 | 1.4 to 2.5 | |
| 3 | 1.3 to 2 | |
| 4 | 1.2 to 1.7 | |
| 5 | 1.1 to 1.5 | properly tight |

The floor matters as much as the ceiling: one wrong answer ends the run, so a
near coin flip on round two would feel unfair.

How it fits the existing rules:

- `comparable()` still gates everything (same pool, same basis, over 10 per
  cent apart). The band only narrows what passes it.
- `drawOpponent()` runs the usual unseen, not recent, any ladder **inside the
  band** first. If the band is empty, it tries anything under the band's
  ceiling, then the closest valid opponent.
- `pickStarter()` only starts a chain on a card that has at least one partner
  in the band (`hasBandOpponent()`), so a chain never opens on a card that can
  only be asked as an easy question.

**Coverage.** After the 27 Sep 2026 data expansion there are 91 per-kg cards
(median 10 to 19 partners per band) and 45 per-item cards in play. Only the
extremes lack a partner in the tighter rounds: vanilla, cloves and cocoa butter
among the per-kg cards, and among the per-item cards the car (65,000 L, next
is 25,000), "brushing teeth, tap off" (1 L), the flushes, the steak, the
untreated-tannery boots and the car wash by hose. They still come up through
the looser early bands or the dilemmas.

### Everyday dilemmas

Once per run, the per-item half of the run opens with an **everyday dilemma**:
two choices that do the same job, compared on the same basis. "Does washing up
by hand need more or less water than a dishwasher cycle?" They live in
`water-cards.json` under `dilemmas`, each naming a `known` card (shown), a
`guess` card (hidden) by title, the `basis` they share, Envie's `comment` and a
`caveat`.

- `pickDilemma()` walks the list from the stored rotation counter, so it
  changes run to run, and skips any pair that is missing, on another basis or
  inside the near-tie floor. `state.dilemmaUsed` keeps it to one per run.
- The question bubble adds "Everyday dilemma. Both figures are <basis>." After
  the reveal, the verdict shows the dilemma's comment and caveat instead of the
  card's own comment.
- The difficulty band does not apply to dilemmas: they are curated.
- Normally the chain carries on from the dilemma's hidden card. If that card is
  not in play (the milk pair measures only freshwater withdrawn), the next
  round starts a fresh chain instead, so it is never compared with anything
  else.

| id | Known | Guess | Source |
|---|---|---|---|
| dishes | Dishwasher, 20 L | Washing up by hand, 103 L | Uisce Éireann; Stamminger et al. 2003 (Bonn) |
| shower-bath | Bath, 80 L | 7-minute shower, 49 L | Uisce Éireann |
| power-shower | Bath, 80 L | 10-minute power shower, 150 L | Waterwise flow rate x minutes (medium) |
| teeth | Tap off, 1 L | Tap on, 12 L | Uisce Éireann / Waterwise |
| toilets | Modern full flush, 6 L | Old flush, 13 L | Waterwise, European Commission |
| car-wash | Bucket, 32 L | Hose, 440 L | RAC Drive citing Uswitch (medium) |
| coffee-tea | Cup of tea, 27 L | Cup of coffee, 132 L | Chapagain & Hoekstra 2007 |
| instant-coffee | Brewed coffee, 132 L | Instant, 80 L | Chapagain & Hoekstra 2007, Table 9 |
| burgers | Soy burger, 158 L | Beef burger, 2,350 L | WFN Report 49 |
| milks | Oat milk, 48 L | Cow's milk, 628 L | Poore & Nemecek 2018 via Our World in Data (withdrawal only) |

Researched but left out: two half loads against one full load (the answer
depends on whether the machine senses the load), watering can against a
sprinkler (the can side had no sourced figure; the sprinkler is a plain card,
1,000 L an hour, from Uisce Éireann), bottled against tap water, polyester
against cotton, second-hand jeans, paper cups and shopping bags (no credible
per-use figure).

### The exit control

`#btn-exit` sits in the game header next to the progress bar, in low-opacity
white chrome. It is a real button, so it is keyboard reachable, it carries an
`aria-label`, and Escape does the same thing. There is no confirmation
dialogue, because the game is hyper-casual and the score is preserved either
way, so an accidental tap costs nothing but the rest of the run. `showScreen()`
sets `hidden` on it whenever the active screen is not the game screen: inactive
screens here are only faded out, not removed, so without that the control would
still be focusable from the intro and score screens.

### What the player has already seen

`wc_seen_<nk>` holds `{ "per-kg": [title, ...], "per-unit": [...] }` per player
and persists across sessions. On a new run the starter card and every opponent
prefer titles the player has not met.

This is a preference, never a rule. `drawOpponent()` and `pickStarter()` each
walk a three-tier ladder (unseen and not recent, then not recent, then anything
valid) and **every** tier is gated by `comparable()` first. Preferring an unseen
card can therefore never produce a cross-pool, cross-basis or near-tie pairing.
When the preferred tiers are empty the code drops back to a valid pairing rather
than bending the rule. `RECENT_MEMORY` (10) is how many cards count as recent.

When a pool's unseen count falls below `MIN_UNSEEN` (8), `recycleSeen()` forgets
everything except what the current run has already used. The next chain draws
from the whole pool again, in a fresh random order, without repeating what is
still on screen. It cannot deadlock, because the final tier of every picker is
the full pool.

## How it fits the rest of the repository

Everything here follows Sort it out, the Carbon Challenge (`environmentle-sort-it-out.html`):

- **Same shell.** `back-to-hub` header, `.screen` / `.screen.active` manager,
  `app-nav` markup kept but hidden throughout as the others keep it, 460 px
  centred frame, the same `gtag` snippet, Comfortaa for display and Montserrat
  for body text, Material Symbols for icons.
- **Same palette shape.** `:root` carries `--water-dark / --water-mid /
  --water-light / --water-pale` where the Carbon Challenge carries `--green-*`,
  plus the shared `--orange`, `--cream`, `--text`, `--text-soft`, `--radius`,
  `--radius-sm`, `--nav-h`. `--water-dark` is the same navy as the Carbon
  Challenge's `--green-dark`, so the two games share their chrome; the blue ramp
  is the blue `index.html` already gives the Water Challenge card.
- **Same player conventions.** `?player=` wins, then `localStorage`
  `env_player_name`; the name is lowercased and underscored into `_nk` the same
  way. Progress is written once per run into the shared `env_progress_<nk>`
  (`totalScore` and `sessionsPlayed`; `actionsPledged` is carried over, since
  pledges now happen on the home screen), so the hub counts this game like any
  other.
  The water keys sit alongside the Carbon Challenge's `sio_*` keys and mirror
  their shape: `wc_best_streak_<nk>`, `wc_last_played_<nk>`,
  `wc_last_score_<nk>`, `wc_seen_<nk>`.
- **Same once-a-day gate.** One run a day per player, with `#screen-played` as
  the water twin of the Carbon Challenge's played screen. See above for the
  first-question mulligan, which is the one place the two games differ.
- **Every read and write is wrapped.** `storeGet` / `storeSet` fall back to an
  in-memory object when `localStorage` throws or is unavailable, which it is in
  some private-browsing modes. The game then forgets between sessions; it never
  breaks.

## How to run it

Either way works.

```bash
# a local server, the normal way
cd app
python3 -m http.server 8000
# then open http://localhost:8000/environmentle-water-challenge.html
```

```
# or just double-click environmentle-water-challenge.html
```

Opening the file straight off disk works because the dataset is also inlined in
the page inside `<script id="water-data" type="application/json">`. Chrome blocks
`fetch()` on `file://` URLs, so the game skips the fetch entirely when the
protocol is `file:` and reads the inline copy instead. Over HTTP it fetches
`water-cards.json` and falls back to the inline copy if that fails. This is the
same shape as the Carbon Challenge's `loadCards()` and its `FALLBACK_CARDS`.

**If you edit `water-cards.json`, refresh the inline copy**, otherwise the two
will drift:

```bash
cd app
python3 - <<'PY'
import re
d = open('water-cards.json').read().rstrip()
h = open('environmentle-water-challenge.html').read()
new, n = re.subn(r'(?s)(<script id="water-data" type="application/json">\n).*?(\n</script>)',
                 lambda m: m.group(1) + d + m.group(2), h)
assert n == 1
open('environmentle-water-challenge.html', 'w').write(new)
PY
```

## The data format

`water-cards.json` uses the Carbon Challenge's field names wherever the two
datasets mean the same thing, so `cards.csv` and `water-cards.json` read as
siblings:

| `cards.csv` (carbon) | `water-cards.json` | Notes |
|---|---|---|
| `title` | `title` | Canonical label, and the lookup key |
| `sub` | `sub` | The quantity the figure is for |
| `category` | `category` | UPPERCASE display label |
| `emoji` | `emoji` | |
| `impact` | `impact` | kg CO₂ there, litres of water here |
| `comment` | `comment` | The "quick extra fact" |
| — | `short_title` | Short label used on the card face |
| — | `impact_unit`, `unit_label`, `basis`, `pool` | Water-only, see below |
| — | `in_play`, `excluded_reason` | Reference-only rows |
| — | `source`, `source_url`, `confidence`, `note` | Provenance |

Two verbatim examples:

```json
{
  "title": "Beef",
  "short_title": "Beef",
  "sub": "1 kg of beef",
  "category": "ANIMAL FOOD",
  "category_key": "food-animal",
  "emoji": "🥩",
  "impact": 15415,
  "impact_unit": "L/kg",
  "unit_label": "per kg",
  "basis": "per-kg",
  "pool": "per-kg",
  "in_play": true,
  "comment": "Only about 1% is water the cow drinks; almost all of it grows the feed.",
  "source": "Mekonnen & Hoekstra 2012, WFN Report 48",
  "source_url": "https://link.springer.com/article/10.1007/s10021-011-9517-8",
  "confidence": "high"
}
```

```json
{
  "title": "Shower (typical, 8 minutes)",
  "short_title": "An 8-minute shower",
  "sub": "one 8-minute shower",
  "category": "AT HOME",
  "category_key": "household",
  "emoji": "🚿",
  "impact": 72,
  "impact_unit": "L per unit",
  "unit_label": "per 8-minute shower",
  "basis": "per-unit",
  "pool": "per-unit",
  "in_play": true,
  "comment": "Showers are the single biggest water use in a typical home, about a quarter of the total.",
  "source": "Derived from Waterwise UK flow rate; Energy Saving Trust 'At Home with Water'",
  "source_url": "https://database.waterwise.org.uk/wp-content/uploads/2019/09/Energy-Saving-Trust_At-Home-With-Water.pdf",
  "confidence": "high",
  "unit_detail": "per one 8-minute shower at 9 L/min"
}
```

The file also carries `meta`, `explainer`, `yardsticks` (household rows used as
the reality-check denominators, referenced by `title`), `actions` and
`comparisons` (whose `relates_to` arrays reference cards by `title`).

## The units rule

This is the part that makes the game correct rather than merely fun. Water figures
in the source data come in three different units, and comparing across them would
be nonsense. So every card carries an explicit `basis` and `pool`, and **a round
only ever pairs two cards from the same pool with the same basis**.

| Pool | Basis | Cards | Played? |
|---|---|---|---|
| `per-kg` | one kilogram of product | 91 | yes |
| `per-unit` | one of the thing: one egg, one cup, one shower, one flush, one pair of jeans | 47 | yes |
| `per-litre` | one litre of drink | 4 | no, see below |

- Each card states its unit on screen (`per kg`, `per 125 ml cup`, `per 8-minute
  shower`), and a line under the question repeats the basis for the round.
- The pool switches only at the reality check, and the chain restarts with a fresh
  top card, so the basis never changes underneath you mid-comparison.
- The unseen-item preference described above sits strictly *inside* this rule. It
  reorders candidates that have already passed `comparable()`; it never widens the
  candidate set. If preferring unseen cards would leave no valid opponent, the code
  falls back to a valid pairing rather than breaking the rule.
- If two cards are within 10 per cent of each other the pairing is dropped and
  another is drawn. A near-tie is not a fair question.
- Household rows sit in the `per-unit` pool and are also the yardsticks for the
  reality checks. A shower is never compared against a kilogram of beef.

### Home use is measured in Irish terms

Since 27 Sep 2026 the household comparisons are Irish first, then European,
never American:

- New card **Household water use per person per day (Ireland)**: 133 L
  (Uisce Éireann, reported by The Irish Times, July 2025). It is first in
  `yardsticks`, so the reality check says, for example, "About 12 days of home
  water use in Ireland".
- The **USA** row (310 L) is `in_play: false` with an `excluded_reason` and is
  no longer a yardstick. It is kept for reference.
- The Europe row (124 L, EEA) stays in play. It is within 10 per cent of the
  Irish row, so the two are never paired.
- The three comparison lines that used a daily household figure (beef,
  t-shirt, jeans) now use the Irish one: 116, 19 and 60 days.
- `meta.version` is 1.1.

Later the same day the remaining household rows were moved onto Uisce
Éireann's own figures (water.ie conservation page and tips):

| Row | Was | Now |
|---|---|---|
| Shower | 8 minutes at 9 L/min (Waterwise UK), 72 L | **7 minutes at 7 L/min, 49 L**. Title is now `Shower (typical, 7 minutes)` |
| Shower per minute (reference) | 9 L | **7 L** |
| Dripping tap | 15 L a day (USGS drip calculator) | **21 L a day** (fixing one saves about 150 L a week; 5,000 to 10,000 L a year) |
| Dishwasher cycle | 10 L (APPLiA, eco) | **20 L** |
| Washing machine cycle | 50 L (APPLiA) | **65 L** |

What moved with them: the shower yardstick ("7-minute showers"), the burger
and t-shirt comparison lines (47 and 51 showers), the cheese line (78
washing-machine cycles), the shower and dripping-tap actions in
`water-cards.json` (5,110 and 7,665 L a year, and the tap now ranks above the
shower), and the matching text in `actions.json` and `challenges.json`. One
home action claimed a minute off the shower saves "over 5,000 litres a year";
that is about 2,555 L at 7 L a minute, so it now says "over 2,500".

The old toilet flush (13 L) still co-cites USGS alongside the European
Commission and Waterwise, which give the same figure.

**Where each home figure comes from is shown on the card.** Since 27 Sep 2026
every home row carries `origin` in `water-cards.json`: `IE` for an Uisce
Éireann figure (shower, bath, brushing teeth, dishwasher, washing machine,
dripping tap, a day at home in Ireland, sprinkler) and `EU` for a European one
(toilet flushes, a day at home in Europe, washing up by hand, power shower, car
wash). `renderCard()` adds a small neutral tag after the unit, "Irish" or
"Europe". "Europe" rather than "EU" because the power shower and car wash come
from UK sources. Food and goods rows carry no tag: they are global averages
from the Water Footprint Network, and the intro line and the closing caveat
panel now say so instead of calling every figure a global average. The tag is
grey on purpose: it labels the source, it is not praise.

**Nothing was dropped from the source data.** 8 of the 146 rows are excluded from
play and each carries an `in_play: false` flag with an `excluded_reason`:

- **Milk, orange juice, beer, wine** (the four `L/L` rows). They are priced by
  volume, so they sit on a per-litre basis of their own. Four cards is too thin a
  pool to draw fair pairings from, so rather than fudge them onto the per-kg basis
  they are reference only. Their per-serving equivalents (a glass of milk, a glass
  of beer, a glass of wine) do play, in the `per-unit` pool.
- **Shower per minute.** A flow rate rather than a single act, and the 7-minute
  shower row already represents showering. The figure is still used by the actions
  list and the reality checks.
- **Household use per person per day, USA.** Replaced by the Irish figure; see
  above.
- **A litre of cow's milk and a litre of oat milk.** These measure only
  freshwater withdrawn (Poore & Nemecek 2018), not a full green, blue and grey
  footprint, so they appear only as their own dilemma.

### Categories

Each card's `category` is the label printed on it, and `category_key` picks its
colour bar and icon (`getCatClass()` and `catIcon()` in the game script). Since
27 Sep 2026:

| Label | `category_key` | Icon | What goes there |
|---|---|---|---|
| FRUIT | `food-fruit` | leaf | fruit, per kg and per piece |
| VEG | `food-veg` | sprout | vegetables, potatoes |
| FOOD | `food-plant` | utensils | grains, pulses, oils, bread, pasta, chocolate, prepared food |
| NUTS & SEEDS | `food-plant` | utensils | nuts, seeds, coconut |
| MEAT & DAIRY | `food-animal` | beef | meat, dairy, eggs, burgers, steak |
| DRINK | `drink` | coffee | cups, glasses, pints |
| COMMODITIES | `commodity` | globe | traded raw crops and materials: coffee and cocoa beans, tea leaves, palm oil, cotton lint, rubber, vanilla, leather, tobacco |
| CLOTHES | `clothes` | shirt | t-shirt, jeans, shoes, boots |
| MANUFACTURING | `goods` | wrench | paper, bed sheet, car |
| TECHNOLOGY | `tech` | smartphone | smartphone, microchip |
| AT HOME | `household` | droplet | showers, baths, flushes, appliances, daily use |

Technology stays thin on purpose. Most tech figures online are either factory
water only (Lenovo's desktop declaration) or come from commercial lists with no
method (the widely repeated 190,000 L laptop), so they were left out.

### Data added on 27 Sep 2026

- **45 per-kg foods** (15 fruit, 12 veg, 11 grains, pulses and oils, 6 nuts
  and seeds, goat, milk powder), all read from Mekonnen & Hoekstra 2011 Table
  3 and Report 48 Table 4, opened directly, with the green, blue and grey split
  in each `note`.
- **8 commodities per kg**: rubber, vanilla, leather, tobacco, hops, cloves,
  cocoa butter, jute.
- **Per-item cards** that fill the old gaps: pizza, beef and soy burgers,
  chocolate bar, banana, orange, glasses of orange and apple juice, a pint,
  a steak, a chicken curry, a bed sheet, two pairs of leather boots (tannery
  waste treated and untreated), a microchip, plus the dilemma cards.
- The WFN Product Gallery (`tools.waterfootprint.org/product-gallery/`) is
  readable in a browser: each product's text comes from
  `details.php?product=N`, 41 products in all. That is how the cheese
  discrepancy was settled.
- Every new row carries `"added": "2026-09-27"`.

## Where the data came from

Compiled 2026-08-06 from a research pass over published water-footprint work. Full
citations, per-card, live in `water-cards.json` (`source` and `source_url` on
every row). The primary sources:

- **Mekonnen & Hoekstra 2010/2011**, "The green, blue and grey water footprint of
  crops and derived crop products", Value of Water Research Report No. 47,
  UNESCO-IHE; peer-reviewed as *Hydrol. Earth Syst. Sci.* 15: 1577-1600.
  <https://hess.copernicus.org/articles/15/1577/2011/>
- **Mekonnen & Hoekstra 2010/2012**, "The green, blue and grey water footprint of
  farm animals and animal products", Value of Water Research Report No. 48,
  UNESCO-IHE; peer-reviewed as *Ecosystems* 15: 401-415.
  <https://link.springer.com/article/10.1007/s10021-011-9517-8>
- **Water Footprint Network, Product Gallery.**
  <https://www.waterfootprint.org/resources/interactive-tools/product-gallery/>
- **Chapagain & Hoekstra 2007**, *Ecological Economics* (coffee and tea per cup).
  <https://ayhoekstra.nl/pubs/Chapagain-Hoekstra-2007.pdf>
- **Gerbens-Leenes & Hoekstra 2009**, WFN Report 38 (sweeteners).
  <https://www.waterfootprint.org/resources/Report38-WaterFootprint-sweeteners-ethanol.pdf>
- **Chapagain, Hoekstra et al. 2006**, WFN Report 18 (cotton).
  <https://www.waterfootprint.org/resources/Report18.pdf>
- **Van Oel & Hoekstra 2010**, WFN Report 46 (paper).
  <https://waterfootprint.org/resources/Report46-WaterFootprintPaper.pdf>
- **Berger et al. 2012**, *Environ. Sci. Technol.*, "Water Footprint of European
  Cars". <https://pubs.acs.org/doi/10.1021/es2040043>
- **Friends of the Earth**, "Mind Your Step" (smartphone).
- **Waterwise UK**, **Energy Saving Trust** "At Home With Water", **APPLiA Europe**
  statistical report 2022-2023, **Uisce Éireann** (the Irish daily figure),
  **European Environment Agency**, **WHO** emergency water guidance (the
  household rows). **USGS** and **US EPA WaterSense** are behind the
  reference-only USA row and are co-cited on two others.

## Data caveats, please read before quoting any of this

**The research pass could not open the primary PDFs.** Direct page fetching was
blocked for every domain in that environment, including waterfootprint.org,
hess.copernicus.org and usgs.gov, so every figure was verified through search
result summaries rather than by opening the source document. The `confidence`
field on each row records what that means in practice:

- **45 of the original 69 rows are high confidence**, meaning the exact figure came back
  verbatim in search results and matches the canonical Water Footprint Network
  value.
- **21 are medium**, meaning corroborated approximately, or by a single source, or
  the sources disagree.
- **3 are low**: peanuts, olives, and refined cane sugar. Not confirmed online.
  Treat those three as the weakest numbers in the set.

**Documented source discrepancies**, all recorded in the `note` field of the row
concerned:

| Card | Used here | Also published |
|---|---|---|
| Cheese | **5,060 L/kg** since 27 Sep 2026 (Report 48 Table 4, and the WFN gallery text) | 3,178 L/kg, the WFN gallery's headline figure, which its own text contradicts. The game used 3,178 until 27 Sep 2026 |
| Car | 65,000 L per car (Berger et al. 2012, peer-reviewed LCA, range 52,000 to 83,000) | the popular 400,000 L figure, which comes from broader virtual-water accounting and is not supported by the LCA |
| Leather shoes | 8,000 L per pair (commonly cited WFN figure) | 14,000 to 16,600 L per pair, depending on how much leather weight is allocated |
| Coffee | 18,900 L/kg (roasted, 18,925 exactly) | 15,897 L/kg for green beans |
| Rice | 2,497 L/kg (milled, white) | 1,673 L/kg for paddy rice |
| Cotton | 10,000 L/kg (processed lint and fabric) | around 3,600 L/kg for unginned seed cotton |
| Lettuce | 237 L/kg | around 130 L/kg in older Chapagain & Hoekstra (2004) compilations |
| Cabbage | 280 L/kg | around 200 L/kg in older WFN compilations |
| Wine, one glass | 109 L per 125 ml | around 120 L in older Chapagain & Hoekstra (2004) figures |

Everything here is a **global average**. Real water footprints move a great deal
with the country, the farm, the irrigation method and the season. The game says so
on the start screen and again at the end. A compass, not a GPS.

### One inconsistency in the wiki worth fixing

Fabien's climate-action wiki carries a figure of **500 to 700 litres per kilogram
of beef** on `wiki/solutions/food/lab-grown-meat.md`. That conflicts with the
Water Footprint Network figure of **15,415 L/kg** used throughout this game, by a
factor of roughly 25. The same wiki quotes "around 15,400 litres" on
`wiki/biodiversity-land/Water - A Finite Resource We Cannot Afford to Ignore.md`,
so the two pages contradict each other. Neither figure carries a source on the
page. The 15,415 number is the standard peer-reviewed one; the 500 to 700 figure
looks like a blue-water-only number or a transcription error.

This is flagged here only. **Nothing in the wiki repository was edited.**

## Accessibility

- Swipe is never the only way to answer: the Less and More buttons stay in the
  footer, and the game is fully keyboard playable: left arrow or `L` for Less,
  right arrow or `M` for More, `Enter` or `Space` to continue and to dismiss a reality check, `Escape`
  to end the run and go to the score screen.
- Every control is a real `<button>` with a visible label. The exit control adds
  an `aria-label` because "End run" alone does not say where it takes you.
- The progress indicator is plain text, "Round 3 of 5", not colour or shape
  alone. The bar beside it is `aria-hidden`, since it repeats that text.
- An `aria-live` region announces the result of each round, the reality checks and
  the final score.
- The count-up animation is skipped entirely under
  `prefers-reduced-motion: reduce`, along with every other transition.
- Tap targets are at least 48 pixels tall, and the Less/More buttons sit in a
  fixed footer at the bottom of the screen where a thumb can reach them.
- Layout tested from 360 pixels wide up to desktop, inside the 460 px frame the
  Carbon Challenge also uses.

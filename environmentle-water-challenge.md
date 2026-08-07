# Water Challenge

A hyper-casual browser game about the fresh water hidden inside everyday things.
No build step, no framework, no dependencies. Laid out the same way as the Carbon
Challenge: one self-contained HTML file at the repository root, one data file
beside it.

| File | What it is |
|---|---|
| `environmentle-water-challenge.html` | The whole game — markup, styles and logic in one file, plus the inline dataset fallback |
| `water-cards.json` | 69 cards, the explainer, 6 actions, 11 comparison lines |
| `environmentle-water-challenge.md` | This file |

It is reachable from the hub: `index.html` lists it on the Games tab and
`GAME_URLS.water` points at it, exactly as `GAME_URLS.sort` points at
`environmentle-sort-it-out.html`.

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
lines for the items you actually saw and the green/blue/grey explainer to read,
and then a **Take further action** button leading to a rotating slice of the
actions, always ranked biggest lever first, which you can pledge for extra
points on top of the run.

## A run is five rounds, once a day

`ROUNDS_PER_RUN` at the top of the game script is the only knob. It is 5.

A run ends in exactly one of three ways:

| Ending | What happens |
|---|---|
| Five correct | Perfect run. The score screen says so and pays the full bonus. |
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

Same gate as the Carbon Challenge, same key shape, same `YYYY-MM-DD` local-day
boundary:

| Key | Meaning |
|---|---|
| `wc_last_played_<nk>` | Day of the last completed run. Mirrors `sio_last_played_<nk>`. |
| `wc_last_score_<nk>` | Points from that run, shown on the played screen. |
| `wc_first_loss_<nk>` | Set when the last run today ended on round one with nothing scored. |
| `wc_retry_used_<nk>` | Set the moment the extra go is taken. |

The exception the owner asked for: go out on the very first question with a score
of zero and you get **one** more attempt that day. Taking it spends
`wc_retry_used_<nk>` immediately, so closing the tab does not buy a third go, and
going out on question one of the retry does not either. Losing on round two or
later never grants a retry.

Coming back after the day is spent shows `#screen-played`, the water twin of the
Carbon Challenge's played screen: today's score and when the next run unlocks,
rather than a dead button. `index.html` reads the same keys to put the
`✓ Done today` badge on the Water Challenge card, exactly as it does for the
Carbon Challenge, and it accounts for the mulligan so the card stays playable
while the spare attempt is still on the table.

A missing, unreadable or corrupt stored value always means "you may play". A
storage failure must never lock anyone out.

### Points

A perfect run is worth 480. The owner set that ceiling; it is this game's own
number and deliberately not the Carbon Challenge's 2100, which is unchanged.

```
5 correct × 60             =  300
+ perfect bonus                90
+ speed bonus, under 45s       90
                            -----
maximum                       480
```

The three parts keep the shape they had at the old scale: the correct answers
carry most of the run, and the two bonuses are equal to each other and smaller.

The speed bonus is not all-or-nothing past the free window. It pays the full 90
up to and including 45 seconds, then decays by 3 a second, so it empties in
thirty seconds and reaches zero at 75. A perfect run therefore scores 480 at its
fastest and 390 once it passes 75 seconds, with everything between the two on a
straight line: 477 at 46 seconds, 435 at 60, 390 from 75 on. The decay was
scaled with the bonus it eats, so the taper keeps its old shape rather than
suddenly biting harder at a smaller ceiling.

Worked examples, for anyone changing the constants again:

| Run | Score |
| --- | --- |
| 5 correct in under 45s | `5×60 + 90 + 90` = **480** |
| 5 correct in 60s | `5×60 + 90 + 45` = **435** |
| 5 correct in 75s or more | `5×60 + 90 + 0` = **390** |
| 3 correct | `3×60` = **180** |
| 0 correct | **0** |

The speed bonus is paid on completed runs only. Paying it on an early exit would
make quitting on round one worth 90 points, which is silly. Points go into the
shared `env_progress_<nk>.totalScore` alongside the same `streak_<nk>` daily
bonus the Carbon Challenge maintains, so any hub or profile total picks the water
game up without special-casing it. Both are plain addition and neither assumes
anything about how large a water run can be, so the smaller numbers need no
change there — a water run simply contributes less to the hub total than it did,
which is what a lower ceiling means. The shared daily streak bonus (100 from
three days, 250 from five) is Carbon's to set and is left alone, so it is now
worth more relative to a water run than before.

`wc_best_streak_<nk>` survives, but "best" now means best score out of five. A
value left behind by the earlier endless build can be larger than a run can ever
be, so `loadBest()` clamps it to `ROUNDS_PER_RUN` and writes the clamped value
back once. "Best 23 of 5" would be nonsense.

### Actions are a pledge, not a list

The score screen used to print four actions as text you could not do anything
with. They are now the Carbon Challenge's pledge flow, reproduced rather than
approximated, so a water pledge behaves and looks like a carbon one.

**The flow, side by side.**

| Step | Carbon Challenge | Water Challenge |
|---|---|---|
| 1 | Score screen: number, message, pills | Same, plus the panels of comparisons, explainer and caveat to read |
| 2 | Bottom buttons: `Take further action` (orange) then `Finish` (outlined) | The same two, in the same order and treatment, sitting **below** the reading rather than above it |
| 3 | `showActions()` opens `#screen-action` | `showActions()` opens `#screen-action` |
| 4 | Header "Take some action", an intro line, a dashed "Nothing for now" opt-out, then 2 action cards | Identical, with 4 action cards, because a water run offers four |
| 5 | Each card: title, level chip, description, `+N pts`, a saving line, a round tick | Identical, same classes |
| 6 | Footer: `Commit & collect +N pts` and a text `Back to my score` | Identical |
| 7 | `commitActions()` adds the bonus, writes progress and history, goes to the hub | Same, except the run itself was already written (see below) |

**Where a pledge is stored.** Exactly where carbon puts it, so nothing
downstream needs to know which game produced it.

| Key | Shape | Written by |
|---|---|---|
| `env_progress_<nk>.actionsPledged` | integer, incremented by the number committed | both games |
| `env_progress_<nk>.totalScore` | integer, incremented by the pledge bonus | both games |
| `actions_history_<nk>` | `[{ title, date }]`, most recent last, capped at 20 | both games |

`index.html` reads all three already. `renderActionHistory()` lists
`actions_history_<nk>` on the profile screen and `openProfile()` shows
`actionsPledged`, both keyed only by player, so a water pledge appears in the
shared list alongside carbon's with no special-casing.

**One deliberate difference from carbon.** Carbon defers writing the run until
you press Finish or Commit. This game cannot: the daily gate is stamped the
moment the score screen appears, or a reload would buy a second run. So the run
and the pledge are two separate writes. `saveProgress()` writes the run once,
`savePledge()` adds the bonus on top once, each guarded by its own flag, so
committing twice cannot collect twice.

**The 480 cap is the run's cap.** Pledge points sit on top of it, as they do in
carbon, and the pledge screen's header says the run's score out loud so the two
are never confused.

### Bottom controls, matched to the other games

The owner asked for the buttons at the bottom to be the same as the other games.
They mostly already were, sharing `.btn-action`, `.btn-finish` and `.app-nav`
verbatim. Four things had drifted, and one has no counterpart at all.

| Screen | Control | Carbon and quiz | Water before | Water now |
|---|---|---|---|---|
| Intro | `.btn-play` | orange, 18px/24px, 17px text | identical already | unchanged |
| Mid-run | `.btn-submit` | one full-width button | two `flex: 1` buttons plus an orange Next | unchanged, see below |
| Score | `.score-bottom` | `16px 20px 16px` | `4px 20px 20px`, and **above** the reading panels | `16px 20px 16px`, **below** them |
| Score | primary orange | `Take further action` opening the action screen | `Play again`, only ever visible on a mulligan | `Take further action`, same label and target |
| Score | `Finish` | a `<button>` calling a handler | an `<a href>` styled to look like one | a `<button>`, same as theirs |
| Score | `.app-nav` | hidden on every screen | shown on the score screen | hidden, same as theirs |
| Action | `.btn-commit` | game's dark tone, 17px/24px, 16px text | did not exist | identical bar the palette |
| Action | `.btn-action-back` | text and a back arrow | did not exist | identical |

Two deliberate non-matches:

- **The mid-run footer.** Carbon reveals answers with one full-width button.
  This game asks a two-way question, so it needs two buttons side by side and a
  full-width Next after them. Matching carbon's single button would mean
  removing a choice the game is built on, so the pair stays.
- **The exit control and the mulligan.** `#btn-exit` and the `One more go`
  button are water-only. The exit control keeps its own header chrome. The
  mulligan button now uses the outlined `.btn-finish` treatment rather than the
  orange one, so the orange stays reserved for `Take further action` exactly as
  it is in the other two games, and the score screen never shows two orange
  buttons competing.

### What a pledged action is worth, and why there is no size bonus

Carbon scales its action bonus with the card's footprint:
`basePoints + max(0, round(log10(impact_kg / 10) * 50))`. Water saves litres,
not CO2, so the obvious move is the same log shape on litres. It does not work,
and it is worth writing down why.

The six actions in `water-cards.json` do each carry a quantified saving, but not
on a comparable basis:

| Rank | Action | Figure the dataset states | Basis |
|---|---|---|---|
| 1 | Swap a beef meal | about 115,000 L | a year |
| 2 | Eat what you buy | 3,178 L / 1,286 L | per wasted item |
| 3 | One more year from your clothes | 10,495 L | per replacement avoided |
| 4 | Tap off while brushing | 8,030 L | a year |
| 5 | Two minutes off the shower | 6,570 L | a year |
| 6 | Fix the dripping tap | 5,475 L | a year |

Four are annual, two are per-event. Putting all six on one basis means deciding
how often a household wastes a kilo of cheese or replaces a pair of jeans, and
the dataset holds neither number. Worse, using the figures as they stand
inverts the dataset's own ranking: food waste is authored as the second biggest
lever, but its stated 3,178 L would score it below shorter showers, which is
ranked fifth. That is exactly the thing the ranking exists to prevent.

So there is no size bonus. Each action carries a flat `basePoints`, the same
convention carbon uses for the base half of its number, and the value follows
the dataset's own `rank`, which is its explicit statement about comparability:

```
points = 250 - (rank - 1) x 30

rank 1  250      rank 4  160
rank 2  220      rank 5  130
rank 3  190      rank 6  100
```

**Calibration against carbon.** Computing carbon's own per-pledge totals from
`CARD_ACTIONS` and `FALLBACK_CARDS` gives 18 possible pledges ranging 59 to 370,
median 174, mean 176. Water's ramp gives 100 to 250, median and mean both 175.
A water pledge is therefore worth almost exactly what a carbon pledge is worth,
with a narrower spread, which is honest: water's actions are closer together in
size than carbon's cards are.

One difference to flag rather than hide: carbon offers 2 actions per run and
water offers 4, so a player who commits to everything can collect more in water
(820) than in carbon (about 600 at the top end). Per pledge the two match; per
run water is more generous. Narrowing that means showing fewer actions, which
would cost the rotation, so it is left as it is for the owner to call.

### The exit control

`#btn-exit` sits in the game header next to the progress bar, in the same
low-opacity white chrome as the Carbon Challenge's tries row. It is a real
button, so it is keyboard reachable, it carries an `aria-label`, and Escape does
the same thing. There is no confirmation dialogue, because the game is
hyper-casual and the score is preserved either way, so an accidental tap costs
nothing but the rest of the run. `showScreen()` sets `hidden` on it whenever the
active screen is not the game screen: inactive screens here are only faded out,
not removed, so without that the control would still be focusable from the intro
and score screens.

### What the player has already seen

`wc_seen_<nk>` holds `{ "per-kg": [title, ...], "per-unit": [...] }` per player
and persists across sessions. On a new run the starter card and every opponent
prefer titles the player has not met.

This is a preference, never a rule. `drawOpponent()` and `pickStarter()` each
walk a three-tier ladder — unseen and not recent, then not recent, then anything
valid — and **every** tier is gated by `comparable()` first. Preferring an unseen
card can therefore never produce a cross-pool, cross-basis or near-tie pairing.
When the preferred tiers are empty the code drops back to a valid pairing rather
than bending the rule.

When a pool's unseen count falls below `MIN_UNSEEN`, `recycleSeen()` forgets
everything except what the current run has already used. The next chain draws
from the whole pool again, in a fresh random order, without repeating what is
still on screen. It cannot deadlock, because the final tier of every picker is
the full pool.

### Does the summary change between runs?

Partly, and more than it used to.

| Part of the score screen | Varies? |
|---|---|
| Points, message, lead line, pills | Yes, with the run |
| "From what you just saw" comparison lines | Yes. Ranked by how many of the cards you actually met they mention, with ties broken by a stored rotation counter |
| The action cards behind "Take further action" | Yes. Four of the six each run, always led by the biggest lever and always in dataset order |
| Green, blue and grey explainer | No. The dataset holds one version of it |
| The closing caveat panel | No. It is fixed copy |

The action rotation walks a window over actions 1 to 5 while always keeping
action 0, so consecutive runs never offer the same four and the ranking
survives. They used to sit on the score screen as a passive list; they are now
the pledge cards, which is the only place they appear.
The explainer is deliberately left alone: there is one version of it in
`water-cards.json` and rotating it would mean inventing copy that no source
backs. If more explainer framings are wanted, they belong in the dataset first.

## How it fits the rest of the repository

Everything here follows the Carbon Challenge (`environmentle-sort-it-out.html`):

- **Same shell.** `back-to-hub` header, `.screen` / `.screen.active` manager,
  `app-nav` markup kept but hidden throughout as the others keep it, 460 px
  centred frame, the same `gtag` snippet, Comfortaa for display and Montserrat
  for body text, Material Symbols for icons.
- **Same pledge screen.** `#screen-action` and its `.action-*` classes are the
  Carbon Challenge's, reproduced rather than reinvented.
- **Same palette shape.** `:root` carries `--water-dark / --water-mid /
  --water-light / --water-pale` where the Carbon Challenge carries `--green-*`,
  plus the shared `--orange`, `--cream`, `--text`, `--text-soft`, `--radius`,
  `--radius-sm`, `--nav-h`. `--water-dark` is the same navy as the Carbon
  Challenge's `--green-dark`, so the two games share their chrome; the blue ramp
  is the blue `index.html` already gives the Water Challenge card.
- **Same player conventions.** `?player=` wins, then `localStorage`
  `env_player_name`; the name is lowercased and underscored into `_nk` the same
  way. Progress is written once per run into the shared `env_progress_<nk>`
  (`totalScore`, `sessionsPlayed`, `actionsPledged`), and pledged actions into
  the shared `actions_history_<nk>`, so the hub counts this game like any other.
  The water keys sit alongside the Carbon Challenge's `sio_*` keys and mirror
  their shape: `wc_best_streak_<nk>`, `wc_last_played_<nk>`,
  `wc_last_score_<nk>`, `wc_seen_<nk>`.
- **Same once-a-day gate.** One run a day per player, with `#screen-played` as
  the water twin of the Carbon Challenge's played screen. See below for the
  first-question mulligan, which is the one place the two games differ.
- **Every read and write is wrapped.** `storeGet` / `storeSet` fall back to an
  in-memory object when `localStorage` throws or is unavailable, which it is in
  some private-browsing modes. The game then forgets between sessions; it never
  breaks.

## How to run it

Either way works.

```bash
# a local server, the normal way
cd _games
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
cd _games
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
| `per-kg` | one kilogram of product | 38 | yes |
| `per-unit` | one of the thing: one egg, one cup, one shower, one flush, one pair of jeans | 26 | yes |
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

**Nothing was dropped from the source data.** Five of the 69 rows are excluded from
play and each carries an `in_play: false` flag with an `excluded_reason`:

- **Milk, orange juice, beer, wine** (the four `L/L` rows). They are priced by
  volume, so they sit on a per-litre basis of their own. Four cards is too thin a
  pool to draw fair pairings from, so rather than fudge them onto the per-kg basis
  they are reference only. Their per-serving equivalents (a glass of milk, a glass
  of beer, a glass of wine) do play, in the `per-unit` pool.
- **Shower per minute.** A flow rate rather than a single act, and the 8-minute
  shower row already represents showering. The figure is still used by the actions
  list and the reality checks.

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
  statistical report 2022-2023, **USGS Water Science School**, **US EPA
  WaterSense**, **European Environment Agency**, **WHO** emergency water guidance
  (the household rows).

## Data caveats, please read before quoting any of this

**The research pass could not open the primary PDFs.** Direct page fetching was
blocked for every domain in that environment, including waterfootprint.org,
hess.copernicus.org and usgs.gov, so every figure was verified through search
result summaries rather than by opening the source document. The `confidence`
field on each row records what that means in practice:

- **45 of 69 rows are high confidence**, meaning the exact figure came back
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
| Cheese | 3,178 L/kg (WFN) | around 5,000 to 5,990 L/kg in some compilations, depending on cheese type and milk-to-cheese ratio |
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

- Fully keyboard playable: left arrow or `L` for Less, right arrow or `M` for
  More, `Enter` or `Space` to continue and to dismiss a reality check, `Escape`
  to end the run and go to the score screen.
- Every control is a real `<button>` with a visible label. The exit control adds
  an `aria-label` because "End run" alone does not say where it takes you.
- The progress indicator is plain text, "Round 3 of 5", not colour or shape
  alone. The bar beside it is `aria-hidden`, since it repeats that text.
- An `aria-live` region announces the result of each round, the reality checks and
  the final score.
- The count-up animation is skipped entirely under
  `prefers-reduced-motion: reduce`, along with every other transition.
- Tap targets are at least 56 pixels tall, and the Less/More buttons sit in a
  fixed footer at the bottom of the screen where a thumb can reach them.
- Layout tested from 360 pixels wide up to desktop, inside the 460 px frame the
  Carbon Challenge also uses.

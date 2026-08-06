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
it right and the bottom card slides up to become the new top card, your streak
goes up and it carries on forever; get it wrong and the run ends.

Two things are layered on top. Every five correct answers the game interrupts with
a full-screen **reality check** that converts something you just met into household
terms, for example one kilogram of roasted coffee against days of home water use.
The end screen shows your streak, the comparison lines for the items you actually
saw, the green/blue/grey explainer, and the actions ranked biggest lever first.

## How it fits the rest of the repository

Everything here follows the Carbon Challenge (`environmentle-sort-it-out.html`):

- **Same shell.** `back-to-hub` header, `.screen` / `.screen.active` manager,
  `app-nav` bottom nav, 460 px centred frame, the same `gtag` snippet, Comfortaa
  for display and Montserrat for body text, Material Symbols for icons.
- **Same palette shape.** `:root` carries `--water-dark / --water-mid /
  --water-light / --water-pale` where the Carbon Challenge carries `--green-*`,
  plus the shared `--orange`, `--cream`, `--text`, `--text-soft`, `--radius`,
  `--radius-sm`, `--nav-h`. `--water-dark` is the same navy as the Carbon
  Challenge's `--green-dark`, so the two games share their chrome; the blue ramp
  is the blue `index.html` already gives the Water Challenge card.
- **Same player conventions.** `?player=` wins, then `localStorage`
  `env_player_name`; the name is lowercased and underscored into `_nk` the same
  way. Progress is written once per run into the shared `env_progress_<nk>`
  (`totalScore` at 100 points a correct answer, `sessionsPlayed`), so the hub
  counts this game like any other. The best streak lives in
  `wc_best_streak_<nk>`, alongside the Carbon Challenge's `sio_last_*` keys.
- **No once-a-day gate.** The Carbon Challenge is one round a day. This game is
  endlessly replayable by design, so it has no `screen-played` equivalent.

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
- The pool switches only after a reality check, and the chain restarts with a fresh
  top card, so the basis never changes underneath you mid-comparison.
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
  More, `Enter` or `Space` to continue and to dismiss a reality check.
- Every control is a real `<button>` with a visible label.
- An `aria-live` region announces the result of each round, the reality checks and
  the final score.
- The count-up animation is skipped entirely under
  `prefers-reduced-motion: reduce`, along with every other transition.
- Tap targets are at least 56 pixels tall, and the Less/More buttons sit in a
  fixed footer at the bottom of the screen where a thumb can reach them.
- Layout tested from 360 pixels wide up to desktop, inside the 460 px frame the
  Carbon Challenge also uses.

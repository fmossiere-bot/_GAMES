# Envie rewrite, batch 7: card data

Applied 2026-09-13. cards.csv (44 carbon cards: comparison and comment), water-cards.json (69 card comments, the green/blue/grey explainer, 11 comparison lines), and the two inline copies regenerated: the water dataset embedded in environmentle-water-challenge.html (using the page's own documented refresh snippet) and FALLBACK_CARDS in environmentle-sort-it-out.html. Impact figures, units, sources and confidence ratings untouched.

## What changed

- Carbon comments were mostly note fragments with em-dashes or semicolons. They are now one or two plain sentences with Envie's reaction where it earns it: "Aviation accounts for 2.5% of global emissions, and it's spread very unevenly, a small share of frequent flyers rack up most of it." "Coffee production takes a lot of water. I didn't realise my morning coffee had that kind of footprint behind it."
- Comparison lines kept as one-line scale hints, only smoothed.
- Water comments were already plain, so the touch is light: "Potatoes are about 80% water themselves, and one of the thriftiest staples you can grow. Good spud, honestly."
- The explainer reads as Envie explaining what he learned: "I learned a water footprint is really three different things added together."
- The unused action columns in cards.csv (the game ignores them) had their em-dashes swapped for commas so the file passes the check.

## Method

One Sonnet pass per file, merge script checked every title, every digit sequence per item, em-dashes, semicolons, emoji and length growth. One flag (a six-word comment that grew to twenty) was read and kept.

## Left alone

- The emoji column in cards.csv and the emoji field in water-cards.json are data the games map to icons; the water page replaces them with Lucide icons at runtime, the sort page still shows them on cards. Replacing those with icon names is a game change, not copy.
- water-cards.json still carries its old `actions` list, unused since actions moved to actions.json.

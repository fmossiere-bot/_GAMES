# Environmentle — logo assets

Direction 3a. The planet drawn as a bold ring with a flat orbit ring crossing it,
and a gamepad silhouette knocked out of the green-to-orange tile; the d-pad's up
key is an arrow — the growth cue, borrowed from The Uptake's U. Wordmark in Rubik
Medium, the "le" handwritten in Caveat Bold with a swoosh underline.
Tagline: "Play. Learn. Act." — set under the name, below the swoosh, in the
same green (or grey on light surfaces). The name always reads first.

## Size tiers — one rule, three files
>= 64px   environmentle-icon-hero.svg   THE LOGO. Full drawing, orbit ring
                                        included. Use everywhere by default.
40-64px   environmentle-icon-app.svg    Reduced tile: same planet and gamepad,
                                        no orbit ring.
< 40px    environmentle-favicon.svg     Ring + arrow only, no gamepad.

## Vector (preferred — scales to anything)
environmentle-icon-hero.svg        the logo, rounded app tile
environmentle-icon-hero-round.svg  the logo in a circle — avatar of record
environmentle-mark-hero-white.svg  logo mark only, white, transparent.
                                   For photos and navy backgrounds.
environmentle-mark-hero-navy.svg   logo mark only, navy, transparent. One-ink print.
environmentle-icon-app.svg         reduced tile (40-64px)
environmentle-icon-round.svg       reduced tile, circular (40-64px)
environmentle-favicon.svg          favicon (< 40px)

## Raster
environmentle-icon-hero-1024/512/256.png        the logo
environmentle-icon-hero-round-1024/512/256.png  round avatar
environmentle-mark-hero-white-1024/512/192.png  white mark, transparent
environmentle-icon-1024/512/192.png             reduced tile
environmentle-icon-round-1024/512/192.png       reduced tile, circular
environmentle-favicon-64/32/16.png              favicon
environmentle-lockup-light.png                  horizontal lockup, transparent bg
environmentle-lockup-navy.png                   horizontal lockup, navy bg
environmentle-stacked-light/navy.png            stacked lockup
environmentle-wordmark.png                      wordmark only, transparent bg

Lockup PNGs are 3x. The wordmark is live text in the source, so if you need it
bigger or in another colour, ask rather than upscaling the PNG.

## Colour
#2f7d33  gradient start (green)
#4e8a4d  Uptake green
#b8871f  gradient mid (ochre)
#e8891a  gradient end (orange)
#0a293b  Uptake navy — wordmark on light, backgrounds
#62c46a  light green — "le" and swoosh on navy

Gradient: linear-gradient(120deg, #2f7d33 0%, #4e8a4d 38%, #b8871f 74%, #e8891a 100%)
Never flip it — orange falls to the right.

## Type
Wordmark    Rubik Medium 500, letter-spacing -0.02em
Tagline     Mulish SemiBold 600, uppercase, 12-13px at lockup size,
            tracking 0.18em with the full stops (0.3em without them)
"le"        Caveat Bold 700, 1.3x the wordmark size, baseline dropped 0.28em
UI / body   Comfortaa (headings) + Mulish (body), same as The Uptake

## Rules
Clear space: 1/4 of the tile height on all sides.
Sizes: follow the three tiers above — nothing else.
Don't retype the "le" in Rubik, don't shadow the mark, and don't put the gradient
tile on a busy photo (use environmentle-mark-hero-white.svg).

## Source
All of it is generated from "Environmentle Brand Pack.dc.html" in this project.
Edit there and re-export; the SVGs are hand-written and safe to open in
Illustrator / Figma / Inkscape.

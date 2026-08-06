# Environmentle Project Memory

## Project Overview
HTML5 hyper-casual sustainability game platform. No backend needed (V1).
PRD: `/Users/fabienmossiere/Documents/_GAMES/Environmentle-PRD.docx`

## File Locations
All files: `/Users/fabienmossiere/Documents/_GAMES/`
- `environmentle-app.html` — Landing page + hub (name entry → game selection)
- `environmentle-quiz-game.html` — Daily quiz game (Game A: multiple choice, timed, 3 questions)
- `environmentle-sort-it-out.html` — Sort It Out game (Game E: drag-and-drop carbon ranking)
- `carbon-sort/index.html` — Older prototype of sort game
- `carbon-sort/cards.csv` — Card data CSV

## Design System
- Fonts: Fraunces (serif, headings) + DM Sans (body)
- Colors: --green-dark: #1A5C38, --green-mid: #2E7D52, --green-light: #7EC8A0, --green-pale: #EAF5EE, --orange: #E07B39, --cream: #F8F5EE
- Border radius: --radius: 20px, --radius-sm: 12px

## Current Build Status (March 2026)
Built:
- ✅ Landing/hub (environmentle-app.html)
- ✅ Quiz game (environmentle-quiz-game.html)
- ✅ Sort It Out game (environmentle-sort-it-out.html)

Not yet built (per PRD):
- ❌ Slider/estimation game (Game B)
- ❌ Guess the Country game (Game C)
- ❌ True or False game (Game D)
- ❌ Linked 3-game daily challenge flow

## Game Architecture
- Each game is a standalone HTML file
- Hub (app.html) links to games via window.open() with ?player= param
- Games use localStorage for player name
- All scoring is client-side, no backend

## User Preferences
- User: Fabien Mossiere (UCD Innovation Academy)
- Irish context preferred (stats, examples, calendar events)
- Mobile-first (375px primary breakpoint)

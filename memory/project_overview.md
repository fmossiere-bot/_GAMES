# Project Overview — The Uptake Games

## What it is
A web platform at **games.the-uptake.com** for climate education. Includes:
- Hyper-casual sustainability games (quiz, sort-it-out, etc.)
- **AI Climate Companion** — a chat interface that answers climate questions using a curated wiki + Claude AI

## Tech Stack
- Frontend: vanilla HTML/CSS/JS (mobile-first, PWA-ready)
- Backend: PHP on Infomaniak shared hosting
- AI: Anthropic Claude API (answers) + Infomaniak AI API (wiki retrieval)
- Wiki: Obsidian → GitHub (`fmossiere-bot/climate-action-wiki`) → companion reads live

## Key Files (on server: games.the-uptake.com)
- `companion.php` — AI Companion chat UI
- `api-proxy.php` — Backend: wiki retrieval + Claude API calls
- `index.html` — Main game hub

## Key Files (local)
- `/Users/fabienmossiere/Documents/_The-UPTAKE Related/_GAMES/companion.php`
- `/Users/fabienmossiere/Documents/_The-UPTAKE Related/_GAMES/api-proxy.php`

## Wiki
- Obsidian vault: `/Users/fabienmossiere/Library/Mobile Documents/iCloud~md~obsidian/Documents/climate-action-wiki/`
- GitHub repo: `https://github.com/fmossiere-bot/climate-action-wiki`
- Published wiki: `https://wiki.the-uptake.com`
- 234 wiki articles across climate science, solutions, sectors, biodiversity, Ireland hub, etc.

## Deploy method
Manual upload via FTP/SFTP to Infomaniak hosting. GitHub Desktop used to push wiki changes.

## User
Fabien Mossière — climate educator, Irish context preferred, mobile-first design.

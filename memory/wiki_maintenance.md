# Wiki Maintenance — Climate Action Wiki

## Structure
- Obsidian vault synced to GitHub via GitHub Desktop
- CLAUDE.md at vault root defines ingest rules, frontmatter schema, tagging rules
- All wiki pages in `wiki/` — never edit `raw/`

## Frontmatter Schema (every wiki page must have)
```yaml
---
title: [Page Title]
category: [category]
tags: [relevant tags]
sources: [source list]
created: YYYY-MM-DD
updated: YYYY-MM-DD
summary: "comma-separated keywords covering ALL topics in the article,
          including specific subjects mentioned in body even if not in title.
          Example: coral reefs, bleaching, ocean acidification, mangroves"
---
```

## Summary Field — Critical for Retrieval
- Added 2026-06-04 to 199 of 234 articles (automated script)
- 35 stub articles still missing summary (no headings/bold to extract from)
- The summary field is what the LLM retriever uses to match questions to pages
- Without it, pages are only findable by title — misses topics buried in content
- When Claude ingests new articles, it must populate summary with 10-20 keywords

## Key Stubs Needing Enrichment (no summary yet)
- Net-Zero target explained
- Carbon Emissions and Cycle  
- How are we doing?
- The climate Mitigation Gap research
- EV Progress
- Net-Zero Buildings
- Plant-Rich Diets - Project Drawdown 3
- Renewable energy worldwide capacity and 2024 forecast
- (+ 27 others)

## GitHub Action
- File: `.github/workflows/build-snippets.yml`
- Trigger: any push to `wiki/**/*.md`
- Script: `.github/scripts/build_snippets.py`
- Output: `wiki/wiki-snippets.json` — auto-committed back to repo
- Picks up summary field from frontmatter of every page

## Future: Further Reading Links
- Plan: add `### Further Reading` subsection under relevant `##` sections in articles
- Build script will extract those curated links into wiki-snippets.json
- Companion will surface them — no scraping, no hallucination
- Not yet implemented — do when enriching stub articles

## wiki.the-uptake.com
- Published wiki site — URL structure not yet confirmed
- Once known, source badges in companion can link directly to wiki pages
- One-line change in api-proxy.php to swap GitHub URLs for wiki.the-uptake.com URLs

# Additions for CLAUDE.md

Paste these into the vault's `CLAUDE.md`. Three edits, marked with where they go.
This file is a note to you, not a wiki page. Do not put it in `wiki/`.

---

## EDIT 1 — add to "Directory Structure", under `wiki/`

```
  myths/                → One page per common climate myth or misleading claim.
                          Each page both debunks the claim and backs it with
                          detail. These pages also generate the Counter Claims
                          cards in the app, so the section headings below are a
                          contract: do not rename or reorder them.
```

---

## EDIT 2 — add to "Standard Tags", under "Themes & Topics"

```
  #myth
```

Every page in `wiki/myths/` carries `#myth` as its first tag, plus up to five
topic tags from the standard list.

---

## EDIT 3 — add a third template, after the Paper template

### Myth template

Use for any page in `wiki/myths/`. One page per claim. Never bundle several
myths into one page: the AI Companion retrieves whole pages, so a page covering
twenty myths wastes the retrieval budget on nineteen irrelevant ones.

Frontmatter is the standard block plus two extra fields:

```
---
title: "Myth: [the claim, in the words people actually use]"
category: myths
tags: ["#myth", "#topic", "#topic"]
sources: ["Publication name, Author, Date — URL"]
created: [YYYY-MM-DD]
updated: [YYYY-MM-DD]
cover_image: ""
summary: [10-20 keywords as usual]
claim_id: [kebab-case, must match the filename exactly]
verdict: [false / mostly-false / incomplete / true-but]
---
```

`claim_id` must equal the filename without `.md`, because the app keys off it.
`title` is the claim prefixed with `Myth: ` so it is unambiguous in the index.

Body:

```
# Myth: [the claim]

> [!WARNING] Verdict: [False / Mostly false / True but incomplete / Fair point, and]

## Say this
One or two sentences, in spoken language, that someone can repeat out loud in a
real conversation. This is the single most important section. Write it to be
said, not read.

## Why it sounds right
The kernel of truth. Never open by calling the claim stupid. If the claim has a
real basis, concede it plainly. If someone raising it is closer to being right
than wrong, say so.

## What is actually true
2-5 sentences. Every number carries a named source. If a figure cannot be
sourced, write it qualitatively instead of inventing precision.

## If they push back
The likely comeback in bold quotes, then a one or two sentence reply. Two or
three pairs at most.

## Also heard as
Bullet list of the other phrasings people use. These feed search, so write how
people actually talk, not how the claim would be written formally.

## Go deeper
Free-form. The detail that does not belong on a card. This section never
reaches the app.

## Connected topics
Relative markdown links to other wiki pages.

## Sources
- Publication name, Author, Date — URL
```

**Guardrails:**
- The six sections `Say this`, `Why it sounds right`, `What is actually true`,
  `If they push back`, `Also heard as` and `Sources` are read by the app. Their
  headings must match exactly.
- Everything else in the file, including `Go deeper` and `Connected topics`, is
  ignored by the app. Add as many extra sections as you like.
- Tone: concede first, correct second. A myth page that only says "wrong" is a
  failed page.
- No em-dashes in body prose. The `sources:` field and the `## Sources` list are
  the documented exception.

---

## EDIT 4 (optional, but worth deciding) — reconcile the tag rule

The documented rule is `tags: ["#fossil-fuels", "#energy"]`. Across the existing
wiki, 82 pages have an empty `tags:` line because they were written as multi-line
YAML, which `build_snippets.py` does not read. Those pages are invisible to
tag-based retrieval.

Either enforce the inline quoted form everywhere, or teach the parser to read
multi-line lists. Worth fixing whichever way, since the current state silently
loses tags.

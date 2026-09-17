# Envie rewrite, batch 4: the five stories

Applied 2026-09-13. Files: courses/course-*.json. Every fact, number, source and quote is unchanged. Slide structure, images, themes and bold phrases kept in step (the `bold` field must be a substring of its slide text; one pre-existing mismatch in the Ben & Jerry's story was fixed).

## What changed

- Titles and tags in sentence case, matching the hub: "Ireland's forestry problem", "Data centres in Ireland", "Climate & finance", "Climate wins in 2025", "The Ben & Jerry's story"; tags like "Why it matters", "Happening now", "The honest picture".
- All ten em-dashes removed (climate wins, data centres, finance).
- European spelling: flavours, criticised.
- Sentences shortened where the old copy ran long; "we" removed where it was the editorial voice.
- Envie's asides: from 11 to 24, only on text and stat slides since the player renders them there. New ones include:
  - Forestry: "New word for me, that one." (afforestation), "I thought a forest was a forest. Turns out not." (Sitka), "Right. I'll be watching this one."
  - Data centres: "More than every plane in the sky. I didn't expect that.", "That one took me a minute." (Jevons Paradox), "Worth asking your TD about."
  - Finance: "I never had a bank account up there.", "Read that number again. I did."
  - Climate wins: "I went looking for the good news. There's more than you'd think.", "Ireland went coal-free the same year. Grand.", "That's why I came down. It's moving."
  - Ben & Jerry's: "Ice cream. I had to look into ice cream.", "Loud about far away, quiet about close to home. I've seen that before."

## Left as is

- The `emoji` field on visual slides and `icon` on some stat slides. The design says no emoji in the UI; replacing them is a player change, not copy.
- Story metadata in index.html (Stories tab and ALL_STORIES) already matched after batch 1.

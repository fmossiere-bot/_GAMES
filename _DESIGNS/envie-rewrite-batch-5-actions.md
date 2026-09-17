# Envie rewrite, batch 5: actions.json and partners.json

Applied 2026-09-13. All 255 action titles and descriptions, plus the three partner descriptions. Every number, unit, shop, app, scheme, organisation and place name kept. Levels, points, impact lines, types, scopes, keywords, ids and sources untouched.

## How it was done

Four parallel passes by type (food + water, circularity + energy, nature + transport, community + mindset), each with the same brief: Envie speaking, short plain sentences, contractions, no em-dashes, European spelling, no new facts, at most three Irish phrases per pass, no repeated tic. A merge script then checked every id, every digit sequence from the old text, em-dashes, emoji, American spellings, title length (all now 70 characters or under) and title uniqueness before anything was written.

## What changed

- Descriptions: the encyclopaedic voice becomes Envie's. Examples:
  - "Cook one meal entirely from what's already in the fridge and cupboards — no new shopping. It's often surprisingly good and saves money and waste." became "Cook one meal from what's already in the fridge and cupboards, no new shopping. I tried this. It was surprisingly good, and it saves money and waste."
  - "One cup per day adds up to around 250 disposable cups per year." became "One cup a day comes to around 250 disposable cups a year. That number surprised me."
  - "Choose loose fruit and veg, and packaging-free options where you can find them." became "Loose fruit and veg, and packaging-free options where you can find them. Harder than it sounds."
- Titles: still imperative, sentence case. About 30 were tightened because they ran past 70 characters or carried an em-dash, for example the 112-character one about conflict stories in the news.
- All 50 em-dashes removed.
- Partners: "Envie will show you the spot" became "I'll show you the spot", plus one aside each on the meadow and the seagrass.

## Left alone

- `impact` lines ("~50 kg CO₂ saved/year if done weekly", "Knowledge is the first step") are data and mixed in tone. Worth a separate pass if they bother you, they show as a small line on each card.
- The old `actions` and `action_intro` fields still inside challenges.json are no longer shown anywhere. Batch 6 will leave them alone unless you want them removed.

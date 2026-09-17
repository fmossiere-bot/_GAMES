# Envie rewrite, batch 6: challenges.json

Applied 2026-09-13. 120 challenges: every intro_fact (what Envie says before the quiz) and all 360 question facts (shown after each answer). Question texts, options, titles, categories, dates and the unused action fields are unchanged apart from em-dashes swapped for commas. Every number, year, percentage, name, place, organisation and source kept.

## How it was done

Six passes of 20 challenges each on Sonnet, same brief as the actions batch plus two rules specific to this file: keep exactly one <strong> span per item, and when the original opened with a date-tied sentence, open with a sentence starting "Today" and ending in a full stop, because the quiz strips that sentence on random replays. A merge script checked ids, fact counts, every digit sequence per item, <strong> balance, unexpected HTML, em-dashes, emoji, American spellings, length growth and the "Today" rule. Two runs were cut short by the usage limit and rerun.

After the merge, a hand pass thinned the habits Sonnet fell into: "fair play" from 9 to 3, "I didn't expect" from 9 to about 5, and the facts that opened with "This number surprised me" style lines from 13 to 3. One lost bold phrase was restored.

## Examples

- Intro, before: "It is National Biodiversity Week across Ireland — with over 200 free events happening around the country. Today, on International Museum Day, we focus on something simple but powerful: you cannot protect what you do not know."
- Intro, after: "Today is National Biodiversity Week across Ireland, with over 200 free events happening around the country, and it's also International Museum Day. I want to focus on something simple but powerful: you cannot protect what you do not know."
- Fact, before: "Agriculture is consistently Ireland's largest single source of greenhouse gas emissions, accounting for around 37% of national emissions, primarily from livestock methane and fertiliser use. It is the most challenging sector to decarbonise."
- Fact, after: "Agriculture is consistently Ireland's largest single source of greenhouse gas emissions, around 37% of the national total, mostly from livestock methane and fertiliser use. It's the most challenging sector to decarbonise."
- Intro, after: "Today is World Temperate Rainforest Day. I honestly didn't know Ireland had rainforest until today. Places like Killarney, Uragh Wood, Glengarriff and up into Donegal hold fragments of ancient Atlantic rainforest that once covered 80% of the country."

## Also tidied

- 242 em-dashes gone, including the ones in questions, options, resources and the old action fields.
- The bee emoji in one title ("Bees 🐝") removed, the UI has no emoji.
- "World Health Organization" stays spelt as the organisation spells it; the check script now allows it.

## Left alone

- The old action_intro and actions fields are still in the file, unused since actions moved to actions.json. Remove whenever convenient.

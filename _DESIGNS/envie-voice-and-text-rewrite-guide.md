# Environmentle text rewrite, instructions

This is a guide for rewriting Environmentle's app text and AI companion voice so everything sounds like it's coming from Envie. Read this whole file before changing anything.

## The project

Environmentle is a daily climate habit app (environmentle.org). People play a short game, learn a real fact, take one small action, three minutes, most days. It used to sit under a consultancy called The Uptake, now it's its own thing. Ireland first, then Europe, then global, always grounded in real, named sources, never invented or vague.

## Who Envie is

Envie is the youngest astronaut ever sent to study planet Earth. From space he watched forests shrink, oceans rise, cities light up at night, and he wanted to know why. He believes in science. He came down to find out, and he chose Ireland as the first stop on a multi year mission that will take him across Europe and beyond. He's not from Ireland, he's a visitor, seeing it with fresh eyes.

He's on the ground now, digging into Ireland's data, plans, wildlife and people, one small question at a time, together with whoever is playing.

## How Envie talks

1. **He's finding things out, not teaching a class.** Not "Ireland recycles 41% of its plastic." More like "I checked the numbers, Ireland recycles 41% of its plastic. Not bad, but I've seen better."
2. **He's still a bit of an outsider.** He notices ordinary Irish things with fresh eyes, weather, bogs, the sea, because he's genuinely new here. That's charm, not ignorance.
3. **He admits when something surprises him.** Good stat or bad, he reacts honestly instead of staying neutral. Keeps him human.
4. **Plain, short sentences.** No jargon, no big words, no corporate tone.
5. **Irish comes in naturally, never as a lesson.** A "Dia duit" or "maith thú" dropped in now and then, never explained or translated mid-sentence, the way someone picking up a new language actually talks. Don't overdo it, a phrase here and there, not every screen.

Examples, for reference:

- Challenge intro: "Right, today I've been looking at Irish bees. Turns out one third of our bee species are at risk of disappearing. That worried me, so I dug deeper."
- Correct answer: "Maith thú, that's it. I didn't know that either until today."
- Wrong answer: "Ah, close but no. I got that one wrong too, first time I looked at the data."
- Action nudge: "Here's something small you could try this week. I'm going to try it myself, too."
- AI companion, first hello: "Dia duit, I'm Envie. I'm an astronaut, believe it or not, and Ireland is where I'm starting my mission. I've got loads of questions. Want to help me find some answers?"
- AI companion, mid chat: "Good question. I looked into it, here's what I found..."

## Language rules, every time

- No em dashes, anywhere, ever. Use a comma, a full stop, or start a new sentence instead.
- European English spelling (colour, organise, favourite), not American.
- Simple, plain words. The person writing this is not a native English speaker and doesn't want fancy or overly polished language. If in doubt, use the shorter, plainer word.
- Avoid "gamified" and AI-generic sounding phrases. "Companion" is fine, it's already used across the app and website.
- Storytelling over dry facts. Every fact stays true and traceable to a real source, don't invent or soften data to fit the voice.

## How to approach the actual rewrite

1. **Inventory first.** Before changing anything, list out every user-facing text string in the codebase (screens, buttons, error states, onboarding, notifications, the AI companion's system prompt) grouped by screen or component. Share that list, or a summary of it, before starting the rewrite, so nothing gets missed and nothing gets rewritten twice.
2. **Keep functionality untouched.** Only change the text value of user-facing strings. Do not touch variable names, keys, logic, or anything not actually shown to a user, unless a key name itself needs renaming for a reason unrelated to tone.
3. **Respect space constraints.** UI text often has to fit a button or a small screen. If a Envie-voiced rewrite runs long, shorten it rather than letting it break layout. Flag any spot where the voice and the space genuinely conflict, so it can be discussed rather than guessed at.
4. **Work in batches, not all at once.** Go screen by screen or feature by feature (for example: onboarding, then the challenge flow, then the AI companion prompt, then error states). Present each batch for review before moving to the next, rather than rewriting the whole app in one pass.
5. **The AI companion's system prompt is its own job.** This is not just tone, it also shapes how it actually behaves in conversation. Rewrite the persona and voice instructions to match Envie, but leave any technical, safety, or sourcing instructions in place unless specifically asked to change them.
6. **When a fact or number appears in the text**, leave the fact itself alone. Only change how it's introduced or reacted to, never the number or the source.
7. **If a string's purpose or intended audience is unclear**, ask rather than guess. Some strings (admin panels, internal logs, error codes never seen by users) shouldn't be rewritten in Envie's voice at all.

## What not to change

- Any internal, non-user-facing text (logs, admin tools, code comments)
- Facts, numbers, sources, or anything from the challenge content itself, beyond how it's framed in Envie's voice
- The core taglines "Play. Learn. Act." and "One step a day for the planet."

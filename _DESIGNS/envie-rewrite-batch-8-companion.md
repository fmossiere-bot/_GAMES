# Envie rewrite, batch 8: the companion

Applied 2026-09-13. Three parts.

## 1. Claims "Say this" view (companion.php, app.css)

Layout, at Fabien's request: Envie now stands on the left beside the bubble (same row pattern as the chat answers), the source chips under the bubble are gone, the Copy button and the "Ask the Companion about this" link are gone. One orange CTA, "Why it sounds right, and what's true", opens the detail view, which already holds the two blocks and the source links. The share icon stays beside the CTA (native share sheet on phones, copy elsewhere).

## 2. System prompt persona (api-proxy.php)

The opening line "You are the Climate Companion for Environmentle..." became Envie: the youngest astronaut sent to study Earth, Ireland the first stop, finding things out with the person asking. A new Voice section: first person, finding things out not teaching, honest reactions, short plain sentences, fresh eyes on Ireland used lightly, Irish phrases at most once per answer, and a hard line that the voice never changes a fact. The one-line takeaway and the audience note now say "in Envie's voice", the off-topic reply is phrased as Envie, the spelling rule says European, and the safety line says stay in character as Envie.

Left exactly as they were: the mandatory output format, the three source prefixes, the SOURCE and ONE LINE markers, the knowledge-source rules, the no-invented-URL rule, the follow-up logic, the formatting rules, the web-search interpretation. The proxy's regexes depend on those. The retriever prompt is internal and untouched. Em-dashes inside the prompt's own headings were swapped for commas, since the prompt tells the model not to use them.

## 3. UI strings (companion.php, api-proxy.php)

| Now | Applied |
|---|---|
| Title: AI Companion, Environmentle / iOS label: AI Companion | Ask Envie, Environmentle / Envie |
| Ask me anything. / Climate action, net zero, renewables, and I'll show you where the answer came from. | Dia duit, I'm Envie. / I'm an astronaut, believe it or not, and Ireland is where my mission starts. Ask me anything about climate and I'll show you where the answer came from. |
| Placeholder: Ask about climate action… | Ask Envie anything… |
| Thinking state: three dots, no text | Three dots plus "Let me have a look…" |
| Reset conversation | Start again |
| Server error (HTTP N): raw body / Network error: message / Something went wrong. Please try again. | I couldn't reach the server. Try again in a moment. / I can't get online right now. Check your connection and try again. / Something went wrong on my side. Try again in a moment. Technical detail goes to the console. The warning glyph is gone. |
| Loading… (claims) | Getting the claims… |
| Could not load the claim library. + raw error / Could not reach the wiki. + raw error / Could not load this page. + raw error | I couldn't load the claims. / I couldn't reach the wiki. / I couldn't load this page. Each with "Try again in a moment." and the raw error logged. |
| Try the Ask tab and the Companion will take it on. / ...will search it for you. | Try the Ask tab and I'll take it on. / ...I'll search it for you. |
| Proxy: Question too long (max 500 characters). | That's a long one. Keep it under 500 characters and I'll have a go. |
| Proxy: Unexpected response from AI service. | I got a strange answer back. Try again in a moment. |
| Heard one down the pub? I've got the fact for it. / Next time it comes up / Say this / The longer answer / source badges / tab names / suggestion chips | Keep |

## Left alone

- claims.json: generated from the wiki myth pages by the build action, so its voice pass belongs in the wiki repo. The live say line with an em-dash comes from there.
- functions/index.js push notification copy ("Your daily challenge is ready 🌿"): the file has uncommitted changes that are not part of this work, so it was not touched. Suggested: "Today's challenge is in" / "Envie has a game, a story or an action ready for you."
- The old ccCopy and ccAskCompanion functions remain in the file, unused.

## 4. Ask view follow-ups (after Fabien's first look)

- **Shorter long answer.** The prompt's formatting rule now asks for two or three short paragraphs, about 120 to 180 words, bullets only when clearer, longer only if the person asks for detail. Envie's one-line summary stays, it is the part that matters.
- **AI answers name their sources.** When the wiki has nothing and Envie answers from training knowledge, the prompt now asks him to say where each figure comes from inside the sentence (organisation, report or study, year if known), in plain text, no links, and to mark a figure as approximate rather than attach a name he is unsure of.
- **Wiki source chips open the article.** The proxy now returns the page slug with each source, and the chip under an answer is a button that opens the article in the app's own wiki view. The back button reads "Back to the answer" and returns to the conversation. The Wiki tab at the top is hidden, so there is no browsing, just read the article and come back. The wiki view code stays in the file because the chips use it.
- **Claim chip opens Claims.** The third suggestion chip on the Ask empty state ("Someone told me electric cars are worse. What do I say?", pill "Claims") now opens the Claims tab filtered to Cars & flying instead of sending the question to the AI, since the claim cards already hold the answer.

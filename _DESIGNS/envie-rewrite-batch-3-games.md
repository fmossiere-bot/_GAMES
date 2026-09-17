# Envie rewrite, batch 3: the three game pages

Applied 2026-09-13. Files: environmentle-quiz-game.html, environmentle-sort-it-out.html, environmentle-water-challenge.html. HTML and JS copy only; the data (challenges.json, cards.csv, water-cards.json and the inline copy of it) is batches 5 and 7. Facts, points and thresholds untouched.

## Shared: played-today gate (all three)

| Now | Applied |
|---|---|
| Leaf or drop emoji | Envie waving (no emoji in the UI) |
| One challenge per day, that's part of the fun. Your score from today: / One run a day, that is part of the fun. | One a day, that's the rhythm. Your score from today: / One run a day, that's the rhythm. |
| Em-dash shown when no score is stored | Score block hidden, line reads "One a day, that's the rhythm. See you tomorrow." |
| You have already played today (water) | You've already played today |

## Daily quiz

| Now | Applied |
|---|---|
| Daily Challenge (badge) / Today's Topic | Daily quiz / Today I looked at |
| Under 3 minutes | About 2 minutes (matches the hub chip; the timer maxes at 60 s) |
| Start challenge | Start the quiz |
| +N points! / Not quite… / Time's up! | That's it. +N pts / Ah, close but no. / Time's up. |
| Useful resources | Where I looked |
| out of 300 (stale placeholder) | out of 450 |
| Avg today: 140 pts (hardcoded, shown to everyone) | Removed |
| Outstanding! / You really know your stuff. Now let's turn that knowledge into action. | Maith thú! / You know your stuff. I learned a thing or two from you today. |
| Good effort! / You got N out of 3 right. Every game teaches something new. | Not bad at all. / N out of 3 right. I got one wrong too, first time I looked at the data. |
| Good start! / Don't worry, the goal is to learn something surprising. You definitely did today. | Good start. / The point is to learn something surprising, and you did today. Same as me. |
| Question N of 3, Quick recap, Your answers, Next question, Review answers →, See my score →, Finish | Keep |

## Sort it out

| Now | Applied |
|---|---|
| Title tag: Environmentle, Carbon Challenge | Environmentle, Sort it out |
| Sort It Out (badge) / The Game | Sort it out / Today's game |
| You get 6 cards, everyday things... Drag them into order with the highest CO₂ at the top, lowest at the bottom. You have 3 tries. | I've picked 6 cards, everyday things like a cheeseburger, a flight, an email. Drag them into order, highest CO₂ at the top, lowest at the bottom. You get 3 tries. I needed all three, first time. |
| 🎉 Perfect! All 6 correct. | Perfect, all 6 in the right place. |
| N of 6 correct, T tries left. Move the wrong ones and try again. | N of 6 in place, T tries left. Move the wrong ones and try again. |
| N of 6 correct. See the right order below. | N of 6 in place. Here's the right order. |
| 🌿 Quick extra fact | One more thing |
| See my score | See my score → (same as the quiz) |
| pts this round | pts today |
| First try! / You nailed it on the first attempt. Impressive. | First try! / All six, first go. I needed three. |
| Well done! / You got all 6 right in T tries. | Maith thú! / All 6 right in T tries. That's the way. |
| Almost there! / You got N out of 6 right. Keep playing to sharpen your instincts. | Almost there. / N out of 6. These numbers caught me out too, the first time. |
| Good start! / Carbon footprints are surprisingly tricky. Every game teaches you something new. | Good start. / Carbon footprints are tricky. I got most of them wrong when I first landed. |
| Carbon Footprint, 6 cards to sort, 3 tries, Faster = more points, Start sorting, Check my order, Try again, Finish | Keep |

## Water challenge

| Now | Applied |
|---|---|
| Higher or Lower (badge) / The Game | Higher or lower / Today's game |
| Which one drinks more? | Keep (this is the game's hook line, the hub calls the game Water challenge) |
| Every card shows the fresh water it takes to make one thing. Say whether the new item needs more or less than the one next to it. A run is five rounds. Get all five and you have a perfect run. | Every card shows the fresh water it takes to make one thing. Tell me if the new one needs more or less than the one beside it. A run is five rounds. Get all five and that's a perfect run. Some of these numbers surprised me. |
| Figures are global averages, mostly from the Water Footprint Network. Real numbers move with... | I took the figures from the Water Footprint Network, global averages mostly. Real numbers move with... |
| That first one caught you out, so here is one more go today. | ...so here's one more go today. |
| Both figures are for one kilogram of the product. / Both figures are for one of the thing, as labelled on the card. | Both figures are for one kilogram. / Both figures are for one item, as labelled on the card. |
| Not quite. X is the thirsty one. (Envie, while the verdict card also says Not quite) | Ah, no. X is the thirsty one. |
| pts this round | pts today |
| Five out of five. You have a real feel for this. | Five out of five. You've a real feel for this. |
| Caveat: ...That is why the list above starts with a food choice rather than a shorter shower. (stale, the list moved) | ...That's why swapping one beef meal does more than a shorter shower. |
| It does not make your choices pointless... no game should pretend otherwise. | That doesn't make your choices pointless... and I'm not going to pretend otherwise. |
| ...water-cards.json records which ones, and why. | ...I've noted which ones, and why, in the data behind the game. |
| Error screen showed the raw error message | Shows "I couldn't load the cards. Check your connection and try again." and logs the raw message to the console |
| Spot on. X is the thirsty one. / All five! / So close! / Good going! / Good start! / You stopped after N of 5. Your score is safe. / That one caught you out on round N of 5. / Your best yet. / Reality check / Keep going / Start guessing / One more go / Finish | Keep |

## Not done here, for the record

- Hardcoded numbers in copy ("six", "five rounds", "3 tries") stay as they are; they match the constants today.
- The streak bonus (+100 at 3 days, +250 at 5) is still added silently in all three games. Worth one line on the score screen some day.
- The inline copy of water-cards.json inside the water page must be regenerated when the JSON copy is rewritten (batch 7).
- Dead CSS and JS for the removed action screens is still in all three files.

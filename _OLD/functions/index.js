const { onSchedule } = require('firebase-functions/v2/scheduler');
const { onCall }     = require('firebase-functions/v2/https');
const { initializeApp }  = require('firebase-admin/app');
const { getFirestore }   = require('firebase-admin/firestore');
const { getMessaging }   = require('firebase-admin/messaging');

initializeApp();
const db = getFirestore();

// ── Subscribe a token to the daily-challenge topic ─────────────────────────
// Called from the client after permission is granted
exports.subscribeToTopic = onCall(
  { region: 'europe-west1' },
  async (request) => {
    const token = request.data.token;
    if (!token) throw new Error('No token provided');
    await getMessaging().subscribeToTopic(token, 'daily-challenge');
    return { success: true };
  }
);

// ── Daily notification — 8am Irish time every day ──────────────────────────
exports.sendDailyChallenge = onSchedule(
  { schedule: '0 8 * * *', timeZone: 'Europe/Dublin', region: 'europe-west1' },
  async () => {
    const snapshot = await db.collection('tokens').get();
    if (snapshot.empty) return;

    const tokens = snapshot.docs.map(d => d.data().token).filter(Boolean);

    // FCM accepts max 500 tokens per multicast message
    const chunks = [];
    for (let i = 0; i < tokens.length; i += 500) {
      chunks.push(tokens.slice(i, i + 500));
    }

    for (const chunk of chunks) {
      await getMessaging().sendEachForMulticast({
        tokens: chunk,
        notification: {
          title: 'Your daily challenge is ready 🌿',
          body:  'A new carbon footprint puzzle is waiting for you.',
        },
        webpush: {
          notification: {
            icon:  'https://games.the-uptake.com/_images/icon-180.png',
            badge: 'https://games.the-uptake.com/_images/favicon-32.png',
          },
          fcmOptions: {
            link: 'https://games.the-uptake.com',
          },
        },
      });
    }

    console.log(`Daily notification sent to ${tokens.length} tokens.`);
  }
);

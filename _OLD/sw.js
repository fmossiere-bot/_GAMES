// ── The Uptake Games — Service Worker ──────────────────
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey:            "AIzaSyDyWJlg8LTpxW3UqHW2zQ9WVNMZ7mf4HUM",
  authDomain:        "uptake-games.firebaseapp.com",
  projectId:         "uptake-games",
  storageBucket:     "uptake-games.firebasestorage.app",
  messagingSenderId: "1002223512756",
  appId:             "1:1002223512756:web:86afd3f7a46f0c59407be1",
});

const messaging = firebase.messaging();

// Handle push messages received while app is in the background
messaging.onBackgroundMessage(payload => {
  const notification = payload.notification || {};
  self.registration.showNotification(
    notification.title || 'The Uptake Games',
    {
      body:  notification.body || 'Your daily challenge is ready 🌿',
      icon:  '/_images/icon-180.png',
      badge: '/_images/favicon-32.png',
      data:  { url: payload.data?.url || 'https://games.the-uptake.com' }
    }
  );
});

// Open the game when the user taps the notification
self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
      // If game is already open in a tab, focus it
      for (const client of list) {
        if (client.url.includes('games.the-uptake.com') && 'focus' in client) {
          return client.focus();
        }
      }
      // Otherwise open a new tab
      return clients.openWindow(
        event.notification.data?.url || 'https://games.the-uptake.com'
      );
    })
  );
});

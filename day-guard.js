/* ============================================================
   Day guard — two challenges a day is the cap, even by direct link.
   Loaded in the <head> of every game page, after engine.js:

     <script src="engine.js"></script>
     <script src="day-guard.js" data-played-key="sio_last_played_"></script>

   The hub already refuses to open a game once the day is done. This covers
   the other way in: a bookmark, a shared link, the browser's back button.
   If this game was already played today, it steps aside and lets the game
   show its own "come back tomorrow" screen (and the Water challenge its
   one retry). Otherwise, when the day is done, it sends the player home.
   ============================================================ */
(function () {
  if (!window.Engine || !document.currentScript) return;
  var key = document.currentScript.getAttribute('data-played-key');
  var name = new URLSearchParams(location.search).get('player') || localStorage.getItem('env_player_name');
  if (!key || !name) return;
  var nk = name.toLowerCase().replace(/\s+/g, '_');
  var today = new Date().toISOString().slice(0, 10);
  if (localStorage.getItem(key + nk) === today) return;
  if (Engine.dayDone(nk)) location.replace('index.html?hub=1&capped=1');
})();

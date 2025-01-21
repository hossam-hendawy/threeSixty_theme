import Player from '@vimeo/player/src/player';

export function vimeoPlayer(player, url, options = {}) {
  const playerID = url ? url.slice(url.lastIndexOf('/') + 1, url.length) : null;
  if (!player && !url) return;
  const initialOptions = {
    id: playerID,
    responsive: true,
    playsinline: true,
    portrait: false,
    byline: false,
    ...options
  };
  let playerEl = new Player(player, initialOptions);

  playerEl.ready().then(() => playerEl.setVolume(0));

  return playerEl;
}

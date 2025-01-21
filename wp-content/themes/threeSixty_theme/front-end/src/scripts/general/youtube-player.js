import YTPlayer from "yt-player";
import {getYoutubeId} from "./get-youtube-id";

//https://www.npmjs.com/package/yt-player
export function youtubePlayer(player, url, options = {}) {
  const playerEl = new YTPlayer(player, {
    annotations: false,
    related: false,
    ...options
  })
  playerEl?.load(getYoutubeId(url))

  return playerEl;
}

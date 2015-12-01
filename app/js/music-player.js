//noinspection JSUnresolvedVariable
/**
 * music-player.js
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:34
 */
var player,
    r = false,
    videos = [],
    i = -1,
//noinspection JSUnresolvedVariable
    connection = new WebSocket('ws://' + botAddress);

//noinspection JSUnusedGlobalSymbols
function onYouTubeIframeAPIReady() {
  //noinspection JSUnresolvedFunction,JSUnresolvedVariable
  player = new YT.Player('player', {
    height: '179',
    width: '318',
    videoId: '',
    playerVars: {
      iv_load_policy: 3,
      showinfo: 0,
      showsearch: 0,
      modestbranding: 1,
      autoplay: 0
    },
    events: {
      'onReady': onPlayerReady,
      'onStateChange': onPlayerStateChange
    }
  });
}

function onPlayerReady(event) {
  //noinspection JSUnresolvedFunction
  event.target.setPlaybackQuality('auto');
  ready();
}

function ready() {
  //noinspection JSUnresolvedVariable
  if (r && botControl) {
    connection.send("ready");
  } else {
    r = true;
  }
}

function onPlayerStateChange(event) {
  //noinspection JSUnresolvedVariable
  if (botControl) {
    connection.send("state|" + event.data);
  }
  //noinspection JSUnresolvedVariable
  if (event.data == YT.PlayerState.BUFFERING) {
    //noinspection JSUnresolvedFunction
    event.target.setPlaybackQuality('auto');
  }
  //noinspection JSUnresolvedFunction
  document.getElementById('current-video-title').innerHTML = event.target.getVideoData().title;
}

connection.onopen = function () {
  ready();
};

connection.onmessage = function (e) {
  var d = e.data.split('|');

  switch (d[0]) {
    case "next":
      i++;
      if (videos[i] === null) i = 0;
      //noinspection JSUnresolvedFunction
      player.cueVideoById(videos[i], 0, "auto");
      break;
    case "previous":
      i--;
      if (videos[i] === null) i = videos.length - 1;
      //noinspection JSUnresolvedFunction
      player.cueVideoById(videos[i], 0, "auto");
      break;
    case "play":
      //noinspection JSUnresolvedFunction
      player.playVideo();
      break;
    case "pause":
      //noinspection JSUnresolvedFunction
      player.pauseVideo();
      break;
    case "add":
      //noinspection JSUnresolvedFunction
      videos.push(d[1]);
      break;
    case "reload":
      location.reload();
      break;
    case "cue":
      //noinspection JSUnresolvedFunction
      player.cueVideoById(d[1], 0, "auto");
      break;
    case "eval":
      return window[d[1]];
      break;
    default:
      break;
  }

  //noinspection JSUnresolvedVariable
  if (botControl) {
    switch (d[0]) {
      case "currentid":
        //noinspection JSUnresolvedFunction
        connection.send("currentid|" + player.getVideoUrl().match(/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?^\s]*).*/)[1]);
        break;
      case "currentvolume":
        //noinspection JSUnresolvedFunction
        connection.send("currentvolume|" + player.getVolume());
        break;
      case "setvolume":
        //noinspection JSUnresolvedFunction
        player.setVolume(d[1]);
        break;
      default:
        break;
    }
  }
};
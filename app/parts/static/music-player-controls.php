<div class="youtube-icon icons">
  <span class="fa fa-youtube-play"></span>
</div>
<div id="controls" class="icons">
  <span class="fa fa-pause" role="button" onclick="togglePlayPause(this)"></span>
</div>
<div id="controls" class="icons">
  <span class="fa fa-step-forward" role="button" onclick="doQuickCommand('skipsong')"></span>
</div>
<div class="icons">
  <?= $templates->switchToggle('<span id="music-player-shuffle" class="fa fa-random"></span>', 'doQuickCommand',
      '[\'musicplayer shuffle\']', '', (array_key_exists('song_shuffle', $botSettings) && $botSettings['song_shuffle'] == '1'), true, true) ?>
</div>
<div class="icons">
  <div class="volume-control">
    <div class="fa fa-volume-up"></div>
    <div class="volume-control-slider-wrapper">
      <div id="volume-control-slider"></div>
    </div>
  </div>
</div>
<div id="current-video-title"><?= $musicPlayerCurrentSong ?></div>
<div class="options">
  <button class="btn btn-primary btn-sm"
          onclick="openPopup('music-player.php?botControl=true', 'PhantomBot WebPanel Sound Effects')">Open Player
  </button>
</div>
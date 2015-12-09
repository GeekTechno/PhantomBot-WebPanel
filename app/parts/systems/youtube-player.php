<?php
/**
 * part-template.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:47
 */

require_once('../../../AppLoader.class.php');
\PBPanel\AppLoader::load();

$session = new \PBPanel\Util\PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$botSettings = $functions->getIniArray('settings');
$requestQueue = preg_split('/\n/', trim($functions->getOtherFile($dataStore->getVar('paths', 'youtubePlaylist'))));
$defaultPlaylist = preg_split('/\n/', trim($functions->getOtherFile($dataStore->getVar('paths', 'defaultYoutubePlaylist'))));
$defaultPlaylistLength = 0;
$requestQueueLength = 0;
$requestQueueDataRows = '';
$defaultPlaylistDataRows = '';

foreach ($requestQueue as $item) {
  if (trim($item) == '') {
    continue;
  }
  if (preg_match('/(.*)\s-\s(.+?)$/', $functions->cleanYTVideoTitle($item), $matches)) {
    $requestQueueDataRows .= '<tr><td>' . ($requestQueueLength + 1) . '.</td><td>' . $matches[1] . '</td><td>' . $matches[2] . '</td></tr>';
    ++$requestQueueLength;
  } else {
    $requestQueueDataRows .= '<tr><td colspan="2">Could not parse the song queue. Make sure the file location is correct in Extras->Preferences.</td></tr>';
    break;
  }
}

foreach ($defaultPlaylist as $item) {
  if (trim($item) == '') {
    continue;
  }
  if (preg_match('/(.{1,11})\s[0-9]+\.\s(.*)/', $functions->cleanYTVideoTitle($item), $matches)) {
    $defaultPlaylistDataRows .= '<tr><td>' . ($defaultPlaylistLength + 1) . '.</td><td>' . trim($matches[2]) . '</td><td><div class="btn-toolbar">'
        . $templates->botCommandButton('playsong ' . $matches[1], '<span class="fa fa-play"></span>', 'success btn-sm')
        . $templates->botCommandButton('defaultdelsong ' . str_replace('https://youtube.com/watch?v=', '', $matches[1]), '<span class="fa fa-trash"></span>', 'danger btn-sm')
        . $templates->botCommandButton('d !chat Youtube link for ' . $matches[2] . ' -> http://youtube.com/watch?v=' . $matches[1], 'Link In Chat', 'default btn-sm')
        . '</div></td></tr>';
    ++$defaultPlaylistLength;
  } else {
    $defaultPlaylistDataRows .= '<tr><td colspan="3">Could not parse the default Playlist. Make sure the file location is correct in Extras->Preferences and the playlist has been parsed by the bot.</td></tr>';
    break;
  }
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Youtube Player Settings
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('./addonscripts/youtubePlayer.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <?= $templates->switchToggle('Toggle Messages', 'doQuickCommand(\'musicplayer toggle\')', '[]', '',
            (array_key_exists('song_toggle', $botSettings) && $botSettings['song_toggle'] == '1')) ?>
        <?= $templates->botCommandButton('stealsong', 'Steal Song') ?>
        <?= $templates->botCommandButton('reloadplaylist', 'Reload "playlist.txt"') ?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Player Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('musicplayer limit', 'Request Limit per viewer', '[amount]', (array_key_exists('song_limit', $botSettings) ? $botSettings['song_limit'] : ''), 'Limit') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('pricecom addsong', '!addsong price', '[amount]', $functions->getIniValueByKey('pricecom', 'addsong'), 'Set') ?>
          </div>
        </div>
      </div>
      <hr/>
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('addsong', 'Add song to the queue', '[youtube url]', null, 'Add') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('delsong', 'Remove song from queue', '[youtube url]', null, 'Del') ?>
        </div>
      </div>
      <?= $templates->dataTable('Request Queue <small>(' . $requestQueueLength . ' items)</small>', ['', 'Video Title', 'Requested By'], $requestQueueDataRows, true) ?>
      <hr/>
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('defaultaddsong', 'Add song to the default playlist', '[youtube url]') ?>
        </div>
      </div>
      <?= $templates->dataTable('Default Playlist <small>(' . $defaultPlaylistLength . ' items)</small>', ['', 'Video Title', 'Actions'], $defaultPlaylistDataRows, true) ?>
    </div>
  </div>
</div>
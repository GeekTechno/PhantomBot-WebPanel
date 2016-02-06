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
$functions = new \PBPanel\Util\FunctionLibrary($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$botSettings = $functions->getDbTableArray('settings');
$youtubePlayerSettings = $functions->getDbTableArray('youtubePlayer');
$requestQueue = preg_split('/\n/', trim($functions->getOtherFile($dataStore->getVar('paths', 'youtubeRequestQueue'))));
$defaultPlaylist = preg_split('/\n/', trim($functions->getOtherFile($dataStore->getVar('paths', 'defaultYoutubePlaylist'))));
$defaultPlaylistLength = 0;
$requestQueueLength = 0;
$requestQueueDataRows = '';
$defaultPlaylistDataRows = '';

foreach ($defaultPlaylist as $item) {
  preg_match('/[0-9]+\.\s(.*)\s\(([a-z0-9=?\/.:_-]+)\)$/i', $functions->cleanYTVideoTitle($item), $matches);
  $defaultPlaylistDataRows .= '<tr><td>' . ($defaultPlaylistLength + 1) . '.</td><td>' . trim($matches[1]) . '</td><td><div class="btn-toolbar" style="width:125px;">'
      . $templates->botCommandButton('playsong ' . str_replace('https://youtube.com/watch?v=', '', $matches[2]), '<span class="fa fa-play"></span>', 'success btn-sm')
      . $templates->botCommandButton('musicplayer deldefault ' . $defaultPlaylistLength, '<span class="fa fa-trash"></span>', 'danger btn-sm')
      . $templates->botCommandButton('d !chat Youtube link for ' . $matches[1] . ' -> ' . $matches[2], '<span class="fa fa-link"></span>', 'default btn-sm') . '</div></td></tr>';
  ++$defaultPlaylistLength;
}

foreach ($requestQueue as $item) {
  if ($item == '') {
    break;
  }
  preg_match('/(.*)\s\(.+\s([a-z0-9_]+)\)$/i', $functions->cleanYTVideoTitle($item), $matches);
  $requestQueueDataRows .= '<tr><td>' . ($requestQueueLength + 1) . '.</td><td>' . $matches[1] . '</td><td>' . $matches[2] . '</td></tr>';
  ++$requestQueueLength;
}
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Youtube Player
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('youtubePlayer.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4 class="collapsible-master">Other Settings</h4>

      <div class="collapsible-content">
        <div class="btn-toolbar">
          <?= $templates->switchToggle('Toggle Song Requests', 'doQuickCommand', '[\'togglerequests\']', '',
              (array_key_exists('requestsEnabled', $youtubePlayerSettings) && $youtubePlayerSettings['requestsEnabled'] == 'true')) ?>
          <?= $templates->switchToggle('Toggle Chat Notifications', 'doQuickCommand', '[\'musicplayer togglenotify\']', '',
              (array_key_exists('updatesInChat', $youtubePlayerSettings) && $youtubePlayerSettings['updatesInChat'] == 'true')) ?>
          <?= $templates->botCommandButton('musicplayer reload', 'Reload Default Playlist') ?>
        </div>
        <div class="spacer"></div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('musicplayer limit', 'Set Song Request Limit Per User', '[amount]',
                (array_key_exists('requestLimit', $youtubePlayerSettings) ? $youtubePlayerSettings['requestLimit'] : 3)) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('musicplayer maxvideolength', 'Set max video length', '[minutes]',
                (array_key_exists('maxVideoLength', $youtubePlayerSettings) ? $youtubePlayerSettings['maxVideoLength'] / 6e4 : 8)) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('addsong', 'Request A Song', '[youtube link]') ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Request Queue <small>(' . $requestQueueLength . ' items)</small>', ['', 'Video Title', 'Requested By'], $requestQueueDataRows, true) ?>
      <hr/>
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('musicplayer adddefault', 'Add Song To Default Playlist', '[youtube url]') ?>
        </div>
      </div>
      <?= $templates->dataTable('Default Playlist <small>(' . $defaultPlaylistLength . ' items)</small>', ['', 'Video Title', 'Actions'], $defaultPlaylistDataRows, true) ?>
    </div>
  </div>
</div>
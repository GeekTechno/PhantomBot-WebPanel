<?php

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

// Merge user settings with default
$chatModSettings = array_merge(
    [
        'linksAllowed' => 'true',
        'youtubeLinksAllowed' => 'true',
        'regsLinksAllowed' => 'true',
        'linksAgressive' => 'false',
        'linkPurgeMessage' => '',
        'linkPermitTime' => 60,
        'capsAllowed' => 'true',
        'capsTriggerRatio' => 0,
        'capsTriggerLength' => 10,
        'capsPurgeMessage' => '',
        'symbolsAllowed' => 'true',
        'symbolsTriggerCount' => 0,
        'symbolsPurgeMessage' => '',
        'repeatCharAllowed' => 'true',
        'repeatCharTriggerLength' => 0,
        'repeatCharPurgeMessage' => '',
    ],
    $functions->getIniArray('chatModerator')
);

echo '<pre>' . print_r($chatModSettings, true) . '</pre>';
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Chat Moderator
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Moderate</h4>

      <div class="row">
        <div class="col-sm-6">
          <?= $templates->combinedBotCommandForm('', [
              'permit' => 'Permit Link',
              'purge' => 'Purge',
              'timeout' => 'Timeout',
              'ban' => 'Ban',
              'unban' => 'Unban',
          ], 'Moderate', '[username]') ?>
        </div>
      </div>
      <hr/>

    </div>
  </div>
</div>

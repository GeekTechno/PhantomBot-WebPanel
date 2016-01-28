<?php

require_once('../../../AppLoader.class.php');
\PBPanel\AppLoader::load();

$session = new \PBPanel\Util\PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST,
    'token'))
) {
  die('Invalid session token. Are you trying to hack me?!');
}

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

// Merge default settings
$timeSettings = array_merge([
    'timeLevel' => 'false',
    'keepTimeWhenOffline' => 'false',
    'modTimePermToggle' => 'false',
    'timePromoteHours' => '50',
    'timeZone' => '50',
], $functions->getDbTableArray('timeSettings'));

?>
  <div class="app-part">
  <div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">
      Manage Time Keeping
      <?= $templates->toggleFavoriteButton() ?>
    </h3>
  </div>
  <div class="panel-body">

  </div>
</div>

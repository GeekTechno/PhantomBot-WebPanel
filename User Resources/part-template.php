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

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        MODULE NAME
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('MODULE SCRIPT NAME')) ?>
      </h3>
    </div>
    <div class="panel-body">
      PAGE CONTENT
    </div>
  </div>
</div>

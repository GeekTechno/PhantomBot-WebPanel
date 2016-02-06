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
$incommingRaids = $functions->getDbTableArray('incommingRaids');
$outgoingRaids = $functions->getDbTableArray('outgoingRaids');
$incommingRaidsDataRows = '';
$outgoingRaidsDataRows = '';

foreach ($incommingRaids as $username => $count) {
  $incommingRaidsDataRows .= '<tr><td>' . $username . '</td><td>' . $count . '</td></tr>';
}

foreach ($outgoingRaids as $username => $count) {
  $outgoingRaidsDataRows .= '<tr><td>' . $username . '</td><td>' . $count . '</td></tr>';
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Raids
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('raidSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('raid', 'Throw a raid!', '[username]') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('raider', 'Record an incomming raid!', '[username]') ?>
        </div>
      </div>
      <hr />
      <div class="row">
        <div class="col-xs-6">
          <?=$templates->dataTable('Past Outgoing Raids', ['Username', 'Count'], $outgoingRaidsDataRows, false)?>
        </div>
        <div class="col-xs-6">
          <?=$templates->dataTable('Past Incomming Raids', ['Username', 'Count'], $incommingRaidsDataRows, false)?>
        </div>
      </div>
    </div>
  </div>
</div>
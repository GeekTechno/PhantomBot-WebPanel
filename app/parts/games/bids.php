<?php
/**
 * bids.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:40
 */

require_once('../../../AppLoader.class.php');
\PBPanel\AppLoader::load();

$session = new \PBPanel\Util\PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\ConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Bidding System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('bidSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('!bid start', 'Start a new bid', '[amount] [increment amount]') ?>
        </div>
        <div class="col-sm-8">
          <div class="spacer"></div>
          <div class="btn-group">
            <?= $templates->botCommandButton('bid warn', 'Warn About End Current Bidding') ?>
            <?= $templates->botCommandButton('bid end', 'End Current Bidding') ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
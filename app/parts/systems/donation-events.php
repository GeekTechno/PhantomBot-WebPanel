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
$connection = new \PBPanel\Util\ConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$botSettings = $functions->getIniArray('settings');

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Donations
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('./handlers/donationHandler.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Donation Event Settings</h4>

      <div class="btn-toolbar">
        <?= $templates->switchToggle('Enable Donation Alerts', 'doQuickCommand', '[\'donationalert toggle\']', '', (array_key_exists('donation_toggle', $botSettings) && $botSettings['donation_toggle'] == '1')) ?>
      </div>
      <div class="spacer"></div>
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('donationalert filepath', 'Donations File', '[filepath]', (array_key_exists('checker_storepath', $botSettings) ? $botSettings['checker_storepath'] : '')) ?>
        </div>
        <div class="col-sm-4">&nbsp;</div>
        <div class="col-sm-4">
          <?= $templates->informationPanel('Set a new file path for the donation text file.<br />This is useful when you have another program that produces an output file, you can point the bot towards it and have it send a message to the chat when there are changes.') ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
/**
 * roll.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:41
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
$rollSettings = $functions->getIniArray('roll');

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Roll Command
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('rollCommand.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <?= $templates->botCommandButton('roll', 'Make A Roll') ?>
        <?= $templates->switchToggle('Toggle Cool Down', 'doQuickCommand', '[\'roll wait\']',
            null, (array_key_exists('roll_wait', $rollSettings) && filter_var($rollSettings['roll_wait'], FILTER_VALIDATE_BOOLEAN))) ?>
        <?= $templates->switchToggle('Toggle Stream-Online-Only', 'doQuickCommand', '[\'roll stream\']',
            null, (array_key_exists('roll_stream', $rollSettings) && filter_var($rollSettings['roll_stream'], FILTER_VALIDATE_BOOLEAN))) ?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Roll Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('roll time', 'Set Roll Cool Down', '[seconds]', (array_key_exists('roll_timer', $rollSettings) ? $rollSettings['roll_timer'] : '30')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('roll bonus', 'Set Roll Doubles Multiplier', '[amount]', (array_key_exists('roll_bonus', $rollSettings) ? $rollSettings['roll_bonus'] : '2')) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
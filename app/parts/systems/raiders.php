<?php
/**
 * raiders.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:44
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
$pastRaiders = $functions->getIniArray('raiders');
$pastRaidersTableRows = '';
$doRaidCommandResponse = $functions->getIniValueByKey('command', 'doraid');
$firstTime = false;
$addCommandJsActive = $functions->getModuleStatus('addCommand.js');
$doRaidCommandForm = $templates->botCommandForm('doraid', 'Raid target', '[username]');

if ($addCommandJsActive == 1 && (!$doRaidCommandResponse || $doRaidCommandResponse == '')) {
  $connection->send('!addcom doraid Let\'s raid (1)! Go to http://twitch.tv/(1) and say "' . $dataStore->getVar('connector', 'channelOwner') . ' Raid!", throw them a follow and show the love! <3');
  $firstTime = true;
} elseif ($addCommandJsActive == '0') {
  $doRaidCommandForm = $templates->addTooltip($templates->botCommandForm('doraid', 'Raid target', '[username]', null, 'Send', true),
      'Enable the addCommand.js module to use this form.' . $templates->botCommandButton('!module enable ./commands/addCommand.js', 'Enable addCommand.js Now', 'primary btn-block'));
}

foreach ($pastRaiders as $username => $timesRaided) {
  $pastRaidersTableRows .= '<tr><td>' . str_replace('_count', '', $username) . '</td><td>' . $timesRaided . ' raids</td></tr>';
}
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Raid System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('raidSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <p>
        Keep a record of incomming raids.
      </p>
      <?= ($firstTime ? $templates->informationPanel('<p>We\'ve created the command !doraid in order to make the Raid target field work.</p>Because PhantomBot does not have such a thing...') : '') ?>
      <hr/>
      <div class="row">
        <div class="col-sm-4">
          <h4>Report Incoming Raid</h4>
          <?= $templates->botCommandForm('raider', 'Raid host') ?>
        </div>
        <div class="col-sm-4">
          <h4>Let's Raid</h4>
          <?= $doRaidCommandForm ?>
        </div>
        <div class="col-sm-4">
          <?= ($doRaidCommandResponse != '0' ? $templates->informationPanel(
              '<p>This system is not (and cannot be) automatic!</p>The current raid command is set to:<p class="text-info">' . $doRaidCommandResponse . '</p>To change this go to the Systems->Commands and modify the command "doraid".'
          ) : '') ?>
        </div>
      </div>
      <?= $templates->dataTable('PastRaiders', ['Username', '# Incomming Raids'], $pastRaidersTableRows, true) ?>
    </div>
  </div>
</div>
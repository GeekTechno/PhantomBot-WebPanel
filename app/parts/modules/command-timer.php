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
$commandTimerSettings = $functions->getDbTableArray('commandTimerSettings');
$commandTimerCommands = $functions->getDbTableArray('commandTimer');
$commandTimerCommandsDataRows = '';

foreach ($commandTimerCommands as $command => $active) {
  $commandTimerCommandsDataRows .= '<tr>'
      . '<td>' . $templates->botCommandButton($command, '!' . $command, 'default') . '</td>'
      . '<td style="width:54px;">' . $templates->botCommandButton('delcommandtimer ' . $command, '<span class="fa fa-trash"></span>', 'danger') . '</td>'
      . '</tr>';
}
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Command Timer
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('commandTimer.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <?= $templates->switchToggle('Toggle Command Timer', 'doQuickCommand', '[\'commandtimer toggle\']',
            null, (array_key_exists('active', $commandTimerSettings) ? filter_var($commandTimerSettings['active'], FILTER_VALIDATE_BOOLEAN) : true)) ?>
        <?= $templates->switchToggle('Toggle Randomizing', 'doQuickCommand', '[\'commandtimer togglerandom\']',
            null, (array_key_exists('randomize', $commandTimerSettings) ? filter_var($commandTimerSettings['randomize'], FILTER_VALIDATE_BOOLEAN) : true)) ?>
      </div>
      <hr/>
      <h4>Add Commands</h4>

      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandFormV2('addcommandtimer', 'Add command to command timer', [
            'placeholder' => '[command]',
            'autoComplete' => 'command'
          ]) ?>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Command Timer Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('commandtimer settime', 'Set Command Interval', '[seconds]', (array_key_exists('time', $commandTimerSettings) ? $commandTimerSettings['time'] : 600)) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('commandtimer setreqmsgs', 'Set Minimum Chat Message Count', '[amount]', (array_key_exists('reqMsgs', $commandTimerSettings) ? $commandTimerSettings['reqMsgs'] : 10)) ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Commands In Command Timer <span class="text-muted">(' . count($commandTimerCommands) . ')</span>', ['command', 'Remove'], $commandTimerCommandsDataRows, true) ?>
    </div>
  </div>
</div>
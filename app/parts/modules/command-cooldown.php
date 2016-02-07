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

$commandCooldowns = $functions->getDbTableArray('commandCooldown');
$commandCooldownsDataRows = '';

foreach ($commandCooldowns as $command => $seconds) {
  $seconds = intval($seconds);
  if ($seconds > 0) {
    $commandCooldownsDataRows .= '<tr>'
        . '<td>!' . $command . '</td>'
        . '<td>' . $functions->secondsToTime($seconds) . '</td>'
        . '<td style="width:64px;">' . $templates->botCommandButton('cooldown ' . $command . ' 0', '<span class="fa fa-trash"></span>', 'danger') . '</td>'
        . '</tr>';
  }
}
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Command Cooldown
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('commandCoolDown.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandFormV2('cooldown', 'Set Command Cooldown', [
              'placeholder' => '[command] [seconds]',
              'autoComplete' => 'command'
          ]) ?>
        </div>
        <div class="col-sm-4">
          <div class="btn-toolbar">
            <?= $templates->botCommandFormV2('clearcooldown', 'Clear All Cooldowns', [
                'placeholder' => '[command]',
                'autoComplete' => 'command'
            ]) ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Default Command Cooldowns', ['Command', 'Cooldown', 'Delete'], $commandCooldownsDataRows, true) ?>
    </div>
  </div>
</div>
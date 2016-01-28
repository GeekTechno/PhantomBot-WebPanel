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

$commandCooldowns = $functions->getDbTableArray('commandCooldown');

$commandCooldownTableRows = '';

foreach ($commandCooldowns as $command => $cooldown) {
  $commandCooldownTableRows .= '<tr>'
      . '<td>' . $templates->botCommandButton($command, $command, 'default btn-block') . '</td>'
      . '<td>' . $functions->secondsToTime($cooldown) . '</td>'
      . '<td>' . $templates->botCommandButton('cooldown ' . $command . ' 0', '<span class="fa fa-trash"></span>', 'danger pull-right') . '</td>'
      . '</tr>';
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Command Cooldown
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-xs-6">
          <?= $templates->botCommandForm('cooldown', 'Set the cooldown for a specific command', '[command] [seconds]') ?>
          <?= $templates->botCommandForm('clearcooldown', 'Clear all current cooldowns for a specific command', '[command]') ?>
        </div>
        <div class="col-xs-6">
          <?= $templates->informationPanel(
              '<ul>
                <li>You can set a cooldown for all commands, default AND custom.</li>
                <li>Cooldowns are tracked per user. So if user A runs a command and activates it\'s cooldown, user B will still be able to use it right after, but this will set the cooldown for them etc...</li>
              </ul>'
          ) ?>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Set Cooldowns', ['Command', 'Cooldown Time', ''], $commandCooldownTableRows) ?>
    </div>
  </div>
</div>

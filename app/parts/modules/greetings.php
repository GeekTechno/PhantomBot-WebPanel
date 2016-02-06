<?php
/**
 * greetings.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:42
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

$greetingSettings = $functions->getDbTableArray('greeting');
$userGreetingRows = '';

foreach ($greetingSettings as $username => $greeting) {
  if (!in_array($username, ['_default', 'autoGreetEnabled'])) {
    $userGreetingRows .= '<tr><td>' . ucfirst($username) . '</td><td>' . $greeting . '</td></tr>';
  }
}


?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Greeting System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('greetingSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4 class="collapsible-master">Default Greeting Settings</h4>

      <div class="collapsible-content">
        <?= $templates->switchToggle('Toggle Auto-Greeting', 'doQuickCommand', '[\'greeting toggledefault\']',
            null, (array_key_exists('autoGreetEnabled', $greetingSettings) && $greetingSettings['autoGreetEnabled'] == 'true')) ?>
        <div class="row">
          <div class="col-sm-6">
            <?= $templates->botCommandForm('greeting setdefault', 'Set global greeting message', '[message]', (array_key_exists('defaultJoin', $greetingSettings) ? $greetingSettings['defaultJoin'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->informationPanel('Users can set their own greeting using "!greeting enable &lt;message&gt;".<br /><br />To disable a personal message use "!greeting disable".') ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('User Greetings', ['Username', 'Greeting'], $userGreetingRows, true) ?>
    </div>
  </div>
</div>
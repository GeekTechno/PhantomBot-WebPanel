<?php
/**
 * subscribe-events.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:45
 */

require_once('../../../AppLoader.class.php');
\PBPanel\AppLoader::load();

$session = new \PBPanel\Util\PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$botSettings = $functions->getIniArray('settings');
$subscribers = $functions->getIniArray('subscribed');
$subscriberTableRows = '';

foreach ($subscribers as $username => $subscribed) {
  if ($subscribed == 1) {
    $subscriberTableRows .= '<tr><td>' . ucfirst($username) . '</td></tr>';
  }
}


?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Subscriber Events
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('subscribeHandler.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <?= $templates->switchToggle('Toggle Subscriber Notices', 'doQuickCommand', '[\'subsilentmode\']',
            null, (array_key_exists('sub_silentmode', $botSettings) && $botSettings['sub_silentmode'] == 1)) ?>
        <div class="btn-group">
          <?= $templates->botCommandButton('subscribecount', 'Current Subscriber Count') ?>
          <?= $templates->botCommandButton('subscribemode', 'Subscribe Mode') ?>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Subscriber Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('subscribemessage', 'Message on subscription', '[message]', (array_key_exists('subscribemessage', $botSettings) ? $botSettings['subscribemessage'] : 'Thanks for the subscription (name)!')) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('subscribereward', 'Points reward on subscription', '[amount]', (array_key_exists('subscribereward', $botSettings) ? $botSettings : '1000')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Subscribers', ['Uername'], $subscriberTableRows, true) ?>
    </div>
  </div>
</div>
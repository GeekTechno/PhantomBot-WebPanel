<?php
/**
 * notices.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:43
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
$noticeSettings = $functions->getIniArray('notice');
$notices = $functions->getIniArray('notices');
$noticesTableRows = '';

foreach ($notices as $nid => $message) {
  $noticesTableRows .= '<tr><td>' . str_replace('message_', '', $nid) . '</td><td>' . $message . '</td></tr>';
}
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Notice System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('noticeHandler.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Channel Notices</h4>

      <div class="form-group">
        <div class="btn-toolbar">
          <div class="btn-group">
            <?= $templates->switchToggle('Toggle Notices', 'doQuickCommand', '[\'notice toggle\']',
                null, (array_key_exists('notices_toggle', $noticeSettings) ? filter_var($noticeSettings['notices_toggle'], FILTER_VALIDATE_BOOLEAN) : true)) ?>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-8">
          <?= $templates->botCommandForm('addnotice', 'Add notice', '[message]') ?>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('delnotice', 'Delete notice', '[#id]') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('notice get', 'Announce Notice', '[#id]') ?>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Notice Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('notice req', 'Message amount requirement', '[amount]', (array_key_exists('reqmessages', $noticeSettings) ? $noticeSettings['reqmessages'] : '25')) ?>
            <?= $templates->botCommandForm('notice timer', 'Notice interval', '[minutes]', (array_key_exists('interval', $noticeSettings) ? $noticeSettings['interval'] : '10')) ?>
          </div>
          <div class="col-sm-4 col-sm-offset-4">
            <?= $templates->informationPanel('<b>Message Requirement</b> is the amount of messages in chat that will trigger a notice.<br>
          <b>Notice Interval</b> is the amount of time before triggering from the amount of messages in chat.') ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Notices', ['#id', 'Message'], $noticesTableRows) ?>
    </div>
  </div>
</div>
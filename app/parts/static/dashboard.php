<?php
/**
 * dashboard.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:41
 */
define('BASEPATH', realpath(dirname(__FILE__)) . '/../../..');

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');
require_once(BASEPATH . '/app/php/classes/ConnectionHandler.class.php');
require_once(BASEPATH . '/app/php/classes/Functions.class.php');
require_once(BASEPATH . '/app/php/classes/ComponentTemplates.class.php');
require_once(BASEPATH . '/app/php/classes/PanelSession.class.php');


$session = new PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$botSettings = $functions->getIniArray('settings');
$botStreamInfo = $functions->getIniArray('stream_info', true);
$noticeCount = $functions->getIniValueByKey('notice', 'num_messages');

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Dashboard <span class="text-muted">Update stream info</span></h3>
    </div>
    <div class="panel-body">
      <h4>Quick Commands</h4>

      <div class="form-group form-group-sm">
        <div class="btn-group btn-group-sm">
          <?= $templates->botCommandButton('tile', 'Announce Current Title') ?>
          <?= $templates->botCommandButton('game', 'Announce Current Game') ?>
        </div>
      </div>
      <div class="form-group">
        <div class="btn-group btn-group-sm">
          <?= $templates->botCommandButton('uptime', 'Announce Current Up-time') ?>
          <?= $templates->botCommandButton('botuptime', 'Announce Current Bot-up-time') ?>
        </div>
      </div>
      <div class="btn-toolbar">
        <?= ($noticeCount > 0 ? $templates->botCommandButton('notice get ' . rand(0, $noticeCount - 1), 'Random Notice', 'default btn-sm') : '') ?>
        <?= $templates->botCommandButton('clear', 'Clear Chat', 'default btn-sm') ?>
        <?= $templates->switchToggle('Mute Bot', $templates->_wrapInJsToggledDoQuickCommand('response', (array_key_exists('response_@all', $botSettings) && $botSettings['response_@all'] == 0 ? 'true' : 'false'), 'enable', 'disable'), '[]', '', (array_key_exists('response_@all', $botSettings) && $botSettings['response_@all'] == 0), true) ?>
        <?= $templates->botCommandButton('d !exit', 'Shutdown PhantomBot', 'danger btn-sm') ?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Edit Stream Title &amp; Game</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->botCommandForm('title', 'Title', 'title', (array_key_exists('title', $botStreamInfo) ? $botStreamInfo['title'] : '')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('game', 'Game', 'game', (array_key_exists('game', $botStreamInfo) ? $botStreamInfo['game'] : '')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Bot Voice</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->botCommandForm('d !chat', 'Use your bot\'s voice', '[/w | /me] Some text', null, 'Say') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Highlights</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->botCommandForm('highlight', 'Add highlight', 'message') ?>
          </div>
        </div>
        <div class="btn-toolbar">
          <div class="btn-group btn-group-sm">
            <?= $templates->botCommandButton('clearhighlights', 'Clear Highlights') ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

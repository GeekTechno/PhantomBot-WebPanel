<?php
/**
 * dashboard.php
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
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\FunctionLibrary($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$botSettings = $functions->getDbTableArray('settings');
$botStreamInfo = $functions->getDbTableArray('streamInfo');
$noticeCount = count($functions->getDbTableArray('notices'));
$latestFollower = $functions->getOtherFile($dataStore->getVar('paths', 'latestFollower'));
$latestDonator = $functions->getOtherFile($dataStore->getVar('paths', 'latestDonator'));

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Dashboard <span class="text-muted">Update stream info</span></h3>
    </div>
    <div class="panel-body">
      <?= ($latestFollower != '' ? '<h4><small>Latest Follower</small> ' . $latestFollower . '</h4>' : '')?>
      <?= ($latestDonator != '' ? '<h4><small>Latest Donator</small> ' . $latestDonator. '</h4>' : '')?>
      <?= ($latestFollower != '' || $latestDonator != '' ? '<hr />' : '')?>
      <h4>Quick Commands</h4>

      <div class="btn-toolbar">
        <?= ($noticeCount > 0 ? $templates->botCommandButton('notice get ' . rand(0, $noticeCount - 1), 'Random Notice', 'default btn-sm') : '') ?>
        <?= $templates->botCommandButton('title', 'Announce Current Title', 'default btn-sm') ?>
        <?= $templates->botCommandButton('game', 'Announce Current Game', 'default btn-sm') ?>
        <?= $templates->botCommandButton('pausecommands', 'Pause Commands', 'default btn-sm') ?>
      </div>
      <div class="spacer"></div>
      <div class="btn-toolbar">
        <?= $templates->botCommandButton('unhost', 'Unhost', 'default btn-sm') ?>
        <?= $templates->botCommandButton('clear', 'Clear Chat', 'default btn-sm') ?>
        <?= $templates->switchToggle('Mute bot', 'doQuickCommand', '[\'mute\']', null, (array_key_exists('response_@chat', $botSettings) && $botSettings['response_@chat'] == 'false'), true)?>
        <?= $templates->botCommandButton('d !exit', 'Shutdown ' . $dataStore->getVar('connector', 'botName'), 'danger btn-sm') ?>
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
    </div>
  </div>
</div>

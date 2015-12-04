<?php
/**
 * points.php
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

$filter = filter_input_array(INPUT_POST);
$botSettings = $functions->getIniArray('settings');
$points = $functions->getIniArray('points');
$pointsTableRows = '';
$totalPoints = 0;
$pointsName = ' ' . (array_key_exists('pointNameMultiple', $botSettings) ? $botSettings['pointNameMultiple'] : 'points');

if (!is_array($filter)) {
  $filter = [];
}
if (array_key_exists('points', $filter)) {
  if ($filter['points'] == 'MAX') {
    $points = array_filter($points, function ($value) {
      return (intval($value) > 1500);
    });
  }
  if ($filter['points'] == 'ASC') {
    asort($points, SORT_NATURAL);
  }
  if ($filter['points'] == 'DESC') {
    arsort($points, SORT_NATURAL);
  }
}
if (array_key_exists('username', $filter)) {
  if ($filter['username'] == 'DESC') {
    krsort($points, SORT_NATURAL);
  }
}


foreach ($points as $username => $amount) {
  $amount = intval($amount);
  $pointsTableRows .= '<tr><td>' . ucfirst($username) . '</td><td>' . $amount . '</td></tr>';
  $totalPoints += $amount;
}
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Points System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('pointSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Currency Statistics</h4>
      <h4>
        <small>Current Total:</small> <?= number_format($totalPoints) . $pointsName ?></h4>
      <h4>
        <small>Current Average:</small> <?= number_format(round($totalPoints / count($points))) . $pointsName ?></h4>
      <hr/>
      <h4 class="collapsible-master">Transactions</h4>

      <div class="collapsible-content">
        <p>Deposit, withdraw or set the amount of points for viewers</p>

        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points all', 'Send an amount of points to everyone', '[amount]') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('makeitrain', 'Divide an amount of points randomly over current viewers', '[amount]') ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points add', 'Send an amount of points to viewer', '[username] [amount]') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points take', 'Take an amount of points from viewer', '[username] [amount]') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points set', 'set viewer points to amount', '[username] [amount]') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Currency Control</h4>

      <div class="collapsible-content">
        <div class="btn-toolbar">
          <?= $templates->switchToggle('Toggle Command permissions for Moderators', 'doQuickCommand', '[\'points toggle\']',
              null, (array_key_exists('permTogglePoints', $botSettings) ? filter_var($botSettings['permTogglePoints'], FILTER_VALIDATE_BOOLEAN) : false)) ?>
        </div>
        <div class="spacer"></div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points name single', 'Set points name (singular)', 'name', (array_key_exists('pointNameSingle', $botSettings) ? $botSettings['pointNameSingle'] : 'point')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points name multiple', 'Set points name (plural)', 'name', (array_key_exists('pointNameMultiple', $botSettings) ? $botSettings['pointNameMultiple'] : 'points')) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points gain', 'Points gained per interval <span class="text-success">(Online)</span>', '[amount]', (array_key_exists('pointGain', $botSettings) ? $botSettings['pointGain'] : '1')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points interval', 'Point gain interval <span class="text-success">(Online)</span>', '[minutes]', (array_key_exists('pointInterval', $botSettings) ? $botSettings['pointInterval'] : '10')) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points offlinegain', 'Points gained per interval <span class="text-danger">(Offline)</span>', '[amount]', (array_key_exists('pointGainOffline', $botSettings) ? $botSettings['pointGainOffline'] : '1')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points offlineinterval', 'Point gain interval <span class="text-danger">(Offline)</span>', '[minutes]', (array_key_exists('pointIntervalOffline', $botSettings) ? $botSettings['pointIntervalOffline'] : '10')) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points mingift', 'Minimal gift', '[amount]', (array_key_exists('pointGiftMin', $botSettings) ? $botSettings['pointGiftMin'] : '10')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Viewer Points (' . count($points) . ')', ['Username', 'Amount'], $pointsTableRows, true, '', [
          [
              'display' => 'Sort Username a-z',
              'name' => 'username',
              'value' => 'ASC',
              'active' => (array_key_exists('username', $filter) && $filter['username'] == 'ASC'),
          ],
          [
              'display' => 'Sort Username z-a',
              'name' => 'username',
              'value' => 'DESC',
              'active' => (array_key_exists('username', $filter) && $filter['username'] == 'DESC'),
          ],
          [
              'display' => 'Sort ' . ucfirst(array_key_exists('pointNameMultiple', $botSettings) ? $botSettings['pointNameMultiple'] : 'points') . ' Ascending',
              'name' => 'points',
              'value' => 'ASC',
              'active' => (array_key_exists('points', $filter) && $filter['points'] == 'ASC'),
          ],
          [
              'display' => 'Sort ' . ucfirst(array_key_exists('pointNameMultiple', $botSettings) ? $botSettings['pointNameMultiple'] : 'points') . ' Descending',
              'name' => 'points',
              'value' => 'DESC',
              'active' => (array_key_exists('points', $filter) && $filter['points'] == 'DESC'),
          ],
          [
              'display' => 'Show > 1500' . ucfirst(array_key_exists('pointNameMultiple', $botSettings) ? $botSettings['pointNameMultiple'] : 'points'),
              'name' => 'points',
              'value' => 'MAX',
              'active' => (array_key_exists('points', $filter) && $filter['points'] == 'MAX'),
          ],
      ]) ?>
    </div>
  </div>
</div>
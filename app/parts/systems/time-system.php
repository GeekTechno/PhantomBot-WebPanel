<?php
/**
 * time-system.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:45
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/Configuration.class.php');
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/ConnectionHandler.class.php');
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/Functions.class.php');
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/ComponentTemplates.class.php');

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$filter = filter_input_array(INPUT_POST);
$botSettings = $functions->getIniArray('settings');
$time = $functions->getIniArray('time');
$timeTableRows = '';

if (!is_array($filter)) {
  $filter = [];
}
if (array_key_exists('time', $filter)) {
  if ($filter['time'] == 'ASC') {
    asort($time, SORT_NATURAL);
  }
  if ($filter['time'] == 'DESC') {
    arsort($time, SORT_NATURAL);
  }
}
if (array_key_exists('username', $filter)) {
  if ($filter['username'] == 'DESC') {
    krsort($time, SORT_NATURAL);
  }
}

foreach ($time as $username => $amount) {
  $timeTableRows .= '<tr><td>' . ucfirst($username) . '</td><td>' . $functions->secondsToTime($amount) . '</td></tr>';
}
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Time System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('timeSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->switchToggle('Toggle Time Command Permissions For Mods', 'doQuickCommand', '[\'time toggle\']',
              null, (array_key_exists('permToggleTime', $botSettings) && filter_var($botSettings['permToggleTime'], FILTER_VALIDATE_BOOLEAN))) ?>
        </div>
        <div class="col-sm-4 col-sm-offset-4">
          <?= $templates->informationPanel('Toggle permissions for time changing commands for Mods.<br/><b>Red means Admin-only!</b>') ?>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Time Control</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('time', 'Check viewer time') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('time set', 'Set viewer time', 'username seconds') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Promotions from Time</h4>

      <div class="collapsible-content">
        <p>
          This feature enables to automatic group level up for viewers. Based on the amount of time set a viewer will become a Regular. (You cannot edit the
          group for it.)
        </p>

        <div class="row">
          <div class="col-sm-4">
            <div class="btn-toolbar">
              <?= $templates->switchToggle('Toggle Time Promotion', 'doQuickCommand', '[\'time autolevel\']',
                  null, (array_key_exists('timeLevel', $botSettings) && filter_var($botSettings['timeLevel'], FILTER_VALIDATE_BOOLEAN))) ?>
            </div>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('time promotehours', 'Set time before promoting viewers', 'hours', (array_key_exists('timePromoteHours', $botSettings) ? $botSettings['timePromoteHours'] : '')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Bot Event Logging</h4>

      <div class="collapsible-content">
        <div class="form-group">
          <div class="btn-group btn-group-sm">
            <?= $templates->botCommandButton('log enable', 'Enable Logs', (array_key_exists('logenable', $botSettings) && $botSettings['logenable'] == 1 ? 'success' : 'default')) ?>
            <?= $templates->botCommandButton('log disable', 'Disable Logs', (array_key_exists('logenable', $botSettings) && $botSettings['logenable'] != 1 ? 'success' : 'default')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Viewers Time', ['Username', 'Time'], $timeTableRows, true, '', [
          [
              'display' => 'Sort Username a-z',
              'name'    => 'username',
              'value'   => 'ASC',
              'active'  => (array_key_exists('username', $filter) && $filter['username'] == 'ASC'),
          ],
          [
              'display' => 'Sort Username z-a',
              'name'    => 'username',
              'value'   => 'DESC',
              'active'  => (array_key_exists('username', $filter) && $filter['username'] == 'DESC'),
          ],
          [
              'display' => 'Sort Time Ascending',
              'name'    => 'time',
              'value'   => 'ASC',
              'active'  => (array_key_exists('time', $filter) && $filter['time'] == 'ASC'),
          ],
          [
              'display' => 'Sort Time Descending',
              'name'    => 'time',
              'value'   => 'DESC',
              'active'  => (array_key_exists('time', $filter) && $filter['time'] == 'DESC'),
          ],
      ]) ?>
    </div>
  </div>
</div>
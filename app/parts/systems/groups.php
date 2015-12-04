<?php
/**
 * groups.php
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
$connection = new \PBPanel\Util\ConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$botSettings = $functions->getIniArray('settings');
$groups = $functions->getIniArray('groups');
$viewerGroups = $functions->getIniArray('group');
$groupTableRows = '';
$viewerGroupTableRows = '';

foreach ($groups as $gid => $gName) {
  $groupTableRows .= '<tr><td>' . $gid . '</td><td>' . $gName . '</td></tr>';
}
foreach ($viewerGroups as $username => $gid) {
  if ($gid != 'null') {
    $viewerGroupTableRows .= '<tr><td>' . ucfirst($username) . '</td><td>' . $groups[$gid] . '</td></tr>';
  }
}
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Group System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('permissions.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4 class="collapsible-master">Group settings per viewer</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('group set', 'Set viewers group', '[username] [groupname]') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('group', 'Get group level') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Group Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('group create', 'Create new group', '[groupname]') ?>
            <?= $templates->botCommandForm('group Remove', 'Delete group', '[groupname]') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('group name', 'Modify group name', '[groupname] [newname]') ?>
            <?= $templates->botCommandForm('group points', 'Modify group points multiplier', '[groupname] [amount]') ?>
          </div>
        </div>
      </div>
      <hr/>
      <div class="row">
        <div class="col-sm-6">
          <?= $templates->dataTable('Viewer Groups', ['Username', 'Group'], $viewerGroupTableRows, true) ?>
        </div>
        <div class="col-sm-6">
          <?= $templates->dataTable('Viewer Groups', ['Group Id', 'Group Name'], $groupTableRows, true) ?>
        </div>
      </div>
    </div>
  </div>
</div>
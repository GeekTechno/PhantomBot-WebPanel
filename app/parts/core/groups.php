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

$groups = $functions->getDbTableArray('groups');

$groupsListRows = '';

foreach ($groups as $gid => $group) {
  $groupsListRows .= '<li>' . $gid . ': ' . $group . '</li>';
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Manage Groups
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-xs-6">
          <?= $templates->botCommandForm('group', 'Assign a user to a group', '[username] [groupId]') ?>
          <?= $templates->informationPanel(
              'Groups per user kan be found under <a class="text-info" onclick="openPart(\'static/viewers.php\')">Viewers Info</a>.'
          ) ?>
        </div>
        <div class="col-xs-6">
          <?= $templates->informationPanel(
              '<ul>
              <li>Users are automatically assigned groups as they enter your channel or meet the requirements for any of the groups below.</li>
              <li>The available groups are: <ul>' . $groupsListRows . '</ul></li>
              </ul>'
          ) ?>
        </div>
      </div>
    </div>
  </div>
</div>

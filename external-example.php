<?php
/**
 * Created by PhpStorm.
 * User: Robin
 * Date: 10-12-2015
 * Time: 19:49
 */

require_once('AppLoader.class.php');
\PBPanel\AppLoader::load();

$dataStore = new \PBPanel\Util\DataStore();
$connection = new PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$userPoints = $functions->getIniArray('points');
$customCommands = $functions->getIniArray('command');
$userPointsDataRows = '';
$customCommandsDataRows = '';

foreach ($userPoints as $user => $points) {
  $userPointsDataRows .= '<tr><td>' . $user . '</td><td>' . $points . '</td></tr>';
}

foreach ($customCommands as $command => $output) {
  $customCommandsDataRows .= '<tr><td>!' . $command . '</td><td>' . $output . '</td></tr>';
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title></title>
  <link href="app/css/<?= $dataStore->getVar('misc', 'theme', 'style_dark') ?>.css"
        rel="stylesheet" type="text/css"/>
  <link rel="icon" href="favicon.ico" type="image/x-icon"/>
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
</head>
<body>
<div id="page-wrapper">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">User points & commands</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-6">
          <?= $templates->dataTable('User Points', ['Username', 'Points'], $userPointsDataRows, true)?>
        </div>
        <div class="col-sm-6">
          <?= $templates->dataTable('Custom Commands', ['Command', 'Output'], $customCommandsDataRows, true)?>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>


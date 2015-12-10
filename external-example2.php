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

// Call this file like ".../external-example2.php?username=[USERNAME]"
$username = filter_input(INPUT_GET, 'username');

if ($username) {
  $singleUserPoints = $functions->getIniValueByKey('points', strtolower($username), true);
} else {
  die('Call this file like ".../external-example2.php?username=[USERNAME]"');
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
      <h3 class="panel-title">User points for <?= $username ?></h3>
    </div>
    <div class="panel-body">
      <?= $singleUserPoints ?>
    </div>
  </div>
</div>
</body>
</html>


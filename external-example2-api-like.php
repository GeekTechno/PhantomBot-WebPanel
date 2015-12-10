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

// Call this file like ".../external-example2-api-like.php?username=[USERNAME]"
$username = filter_input(INPUT_GET, 'username');

if ($username) {
  $userTime = intval($functions->getIniValueByKey('time', strtolower($username), true));
  echo json_encode([
      'username' => $username,
      'points' => $functions->getIniValueByKey('points', strtolower($username), true),
      'timeInSeconds' => $userTime,
      'timeHMS' => sprintf("%02d%s%02d%s%02d", floor($userTime/3600), ':', ($userTime/60)%60, ':', $userTime%60),
  ]);
} else {
  die('Call this file like ".../external-example2-api-like.php?username=[USERNAME]"');
}

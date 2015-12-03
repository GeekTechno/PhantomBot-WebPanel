<?php
/**
 * connect.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:46
 */

require_once('classes/Configuration.class.php');
require_once('classes/ConnectionHandler.class.php');
require_once('classes/Functions.class.php');

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$input = filter_input_array(INPUT_POST);

if (!array_key_exists('username', $input) || !array_key_exists('password', $input) || !$functions->isValidUser($input['username'], $input['password'])) {
  $functions->sendBackError('No login');
  exit;
}

if (array_key_exists('action', $input) && $input['action'] != '') {
  $action = $input['action'];

  switch ($action) {
    case 'config':
      $functions->getJSConfig();
      break;
    case 'getConfig':
      $functions->getConfig();
      break;
    case 'command':
      if (array_key_exists('command', $input)) {
        echo json_encode($connection->send('!' . $input['command']));
      } else {
        $functions->sendBackError('Command is empty');
      }
      break;
    case 'getIni':
      if (array_key_exists('uri', $input)) {
        $functions->getIniArray($input['uri'], false);
      } else {
        $functions->sendBackError('Missing ini uri');
      }
      break;
    case 'getOtherFile':
      if (array_key_exists('uri', $input)) {
        $functions->getOtherFile($input['uri'], false);
      } else {
        $functions->sendBackError('Missing ini uri');
      }
      break;
    case 'getIniValueByKey':
      if (array_key_exists('uri', $input) && array_key_exists('key', $input)) {
        $functions->sendBackOk($functions->getIniValueByKey($input['uri'], $input['key']));
      } else {
        $functions->sendBackError('Missing parameters');
      }
      break;
    case 'getCurrentTitle':
      $functions->getCurrentTitle();
      break;
    case 'getMusicPlayerPlaylist':
      $functions->sendBackOk($functions->getMusicPlayerPlaylist($config->paths['youtubePlaylist']));
      break;
    case 'saveToConfig':
      if (array_key_exists('settingPath', $input) && array_key_exists('setting', $input)) {
        if ($config->saveToConfigWeb($input['settingPath'], $input['setting'])) {
          $functions->sendBackOk('Setting Saved');
        } else {
          $functions->sendBackError('Failed to save setting', 418, 418);
        }
      } else {
        $functions->sendBackError('Missing parameters');
      }
      break;
    case 'testBotConnection':
      echo json_encode($connection->testConnection());
      break;
    case 'saveSfxCommand';
      if (array_key_exists('command', $input) && array_key_exists('file', $input)) {
        if ($functions->saveSfxCommand($input['command'], $input['file'])) {
          $functions->sendBackOk('Setting Saved');
        } else {
          $functions->sendBackError('Failed to save setting', 418, 418);
        }
      } else {
        $functions->sendBackError('Missing parameters');
      }
      break;
    case 'deleteSfxCommand';
      if (array_key_exists('command', $input)) {
        if ($functions->deleteSfxCommand($input['command'])) {
          $functions->sendBackOk('Setting Saved');
        } else {
          $functions->sendBackError('Failed to save setting', 418, 418);
        }
      } else {
        $functions->sendBackError('Missing parameters');
      }
      break;
    default:
      $functions->sendBackError('Unknown action');
      break;
  }

} else {
  $functions->sendBackError('No action defined');
}
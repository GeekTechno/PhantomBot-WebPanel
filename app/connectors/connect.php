<?php
/**
 * connect.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:46
 */
require_once('../../AppLoader.class.php');
\PBPanel\AppLoader::load();

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
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
      if (array_key_exists('command', $input) && $input['username']) {
        echo json_encode($connection->send(
            '!' . $input['command'],
            (array_key_exists(strtolower($input['username']), $functions->getDbTableArray('visited')) ? $input['username'] : $dataStore->getVar('connector', 'channelOwner'))
        ));
      } else {
        $functions->sendBackError('Command is empty');
      }
      break;
    case 'getIni':
      if (array_key_exists('uri', $input)) {
        $functions->getDbTableArray($input['uri'], false);
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
        $functions->sendBackOk($functions->getDbTableValueByKey($input['uri'], $input['key']));
      } else {
        $functions->sendBackError('Missing parameters');
      }
      break;
    case 'getCurrentTitle':
      $functions->getCurrentTitle();
      break;
    case 'getMusicPlayerPlaylist':
      $functions->sendBackOk($functions->getMusicPlayerPlaylist($dataStore->getVar('paths', 'youtubePlaylist')));
      break;
    case 'saveToConfig':
      if (array_key_exists('settingPath', $input) && array_key_exists('setting', $input)) {
        $path = explode('/', $input['settingPath']);
        if ($dataStore->setVar($path[0], $path[1], $input['setting'])) {
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
    case 'deletePanelUser':
      if (array_key_exists('panelUsername', $input)) {
        if ($dataStore->delVar('users', $input['panelUsername'])) {
          $functions->sendBackOk('Setting Saved');
        } else {
          $functions->sendBackError('Failed to save setting', 418, 418);
        }
      } else {
        $functions->sendBackError('Missing parameters');
      }
      break;
    case 'savePanelUser':
      if (array_key_exists('panelUsername', $input) && array_key_exists('panelPassword', $input)) {
        if ($dataStore->setVar('users', $input['panelUsername'], $input['panelPassword'])) {
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
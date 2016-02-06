<?php
/**
 * Created by IntelliJ IDEA.
 * User: Robin | Juraji
 * Date: 6-2-2016
 * Time: 17:31
 */

require_once('../../AppLoader.class.php');
\PBPanel\AppLoader::load();

$session = new \PBPanel\Util\PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\FunctionLibrary($dataStore, $connection);

$phpInput = $functions->getPhpInput();

if (array_key_exists('type', $phpInput) && array_key_exists('input', $phpInput)) {
  $values = [];

  if ($phpInput['type'] == 'command' || $phpInput['type'] == 'all') {
    $values = array_merge(
        $values,
        array_keys($functions->getDbTableArray('command')),
        loadDefaultCommands()
    );
  }

  if ($phpInput['type'] == 'user' || $phpInput['type'] == 'all') {
    $values = array_merge(
        $values,
        array_keys($functions->getDbTableArray('time'))
    );
  }

    sort($values);

    sendMatches(array_filter($values, function ($value) use ($phpInput) {
      return (strpos($value, $phpInput['input']) > -1);
    }));
}

function sendMatches($matches)
{
  echo json_encode(array_slice($matches, 0, 10));
}

function loadDefaultCommands()
{
  $file = @file(
      \PBPanel\AppLoader::getDocRoot() . '/app/content/botcommands.dat',
      FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES
  );

  return ($file ? $file : []);
}
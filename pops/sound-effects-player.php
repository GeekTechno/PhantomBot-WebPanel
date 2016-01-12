<?php
/**
 * Created by PhpStorm.
 * User: Robin | Juraji
 * Date: 3-12-2015
 * Time: 03:43
 */

require_once('../AppLoader.class.php');
\PBPanel\AppLoader::load();

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);

$eventServerAdress = $dataStore->getVar('connector', 'botIp') . ':' . (intval($dataStore->getVar('connector', 'botBasePort')) + 2);
?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title></title>
  <link href="../app/css/music-player.css" rel="stylesheet" type="text/css"/>
  <link rel="icon" href="../favicon.ico" type="image/x-icon"/>
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon"/>
  <script>
    var botAddress = '<?= $eventServerAdress ?>',
        commandPermissions = <?= json_encode($functions->getIniArray('permcom')) ?>,
        userGroups = <?= json_encode($functions->getIniArray('group')) ?>,
        sfxCommands = <?= json_encode($dataStore->getTableAsArray('sfxcommands')) ?>;
  </script>
  <script src="//code.jquery.com/jquery-1.11.3.min.js" type="text/javascript"></script>
  <script src="//code.jquery.com/ui/1.11.3/jquery-ui.min.js" type="text/javascript"></script>
  <script src="../app/js/rsocket.min.js"></script>
  <script src="../app/js/sfx-host.min.js"></script>
</head>
<body>
<div class="info">
  <h3 class="title">Sfx commands on <a
        href="http://twitch.tv/<?= $dataStore->getVar('connector', 'channelOwner') ?>"><?= $dataStore->getVar('connector', 'channelOwner') ?></a>
  </h3>
  <span id="sfx-display">Waiting for commands...</span>
</div>
<div id="sfx-history-wrapper">
  <h3 class="title">History</h3>

  <div id="sfx-history"></div>
</div>
</body>
</html>
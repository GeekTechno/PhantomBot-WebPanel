<?php
/**
 * Created by PhpStorm.
 * User: Robin | Juraji
 * Date: 3-12-2015
 * Time: 03:43
 */

define('BASEPATH', realpath(dirname(__FILE__)));

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');

$config = new Configuration();

$eventServerAdress = $config->botIp . ':' . (intval($config->botBasePort) + 2);
?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title></title>
  <link href="app/css/music-player.css" rel="stylesheet" type="text/css"/>
  <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
  <script src="https://www.youtube.com/iframe_api"></script>
  <script>
    var botAddress = '<?= $eventServerAdress ?>',
        sfxEnabled = <?=$config->sfxSettings['enabled']?>,
        sfxCommands = <?= json_encode($config->sfxSettings['commands']) ?>;
  </script>
  <script src="//code.jquery.com/jquery-1.11.3.min.js" type="text/javascript"></script>
  <script src="app/js/rsocket.min.js"></script>
  <script src="app/js/sfx-host.js"></script>
</head>
<body>
<div class="info">
  <h3 class="title">Sfx commands on <a href="http://twitch.tv/<?= $config->channelOwner ?>"><?= $config->channelOwner ?></a>
  </h3>
  <span id="sfx-display">Waiting for commands...</span>
</div>
<div id="sfx-history-wrapper">
  <h3 class="title">History</h3>
  <div id="sfx-history"></div>
</div>
</body>
</html>
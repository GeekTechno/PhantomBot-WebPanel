<?php
/**
 * Created by PhpStorm.
 * User: Robin | Juraji
 * Date: 3-12-2015
 * Time: 03:43
 */

require_once('AppLoader.class.php');
\PBPanel\AppLoader::loadUtil('DataStore');

$dataStore = new \PBPanel\Util\DataStore();

$eventServerAdress = $dataStore->getVar('connector', 'botIp') . ':' . (intval($dataStore->getVar('connector', 'botBasePort')) + 2);
$alertSettings = json_encode([
  'bgColor' => $dataStore->getVar('misc', 'streamAlertBG', '#ffffff'),
  'follow' => [
    'bgImage' => addslashes($dataStore->getVar('streamalertsettings', 'followerAlertBG')),
    'textTemplate' => $dataStore->getVar('streamalertsettings', 'followerAlertText'),
    'soundFile' => addslashes($dataStore->getVar('streamalertsettings', 'followerAlertSound')),
    'customCss' => $dataStore->getVar('streamalertsettings', 'followerAlertCSS'),
  ],
  'host' => [
    'bgImage' => addslashes($dataStore->getVar('streamalertsettings', 'hostAlertBG')),
    'textTemplate' => $dataStore->getVar('streamalertsettings', 'hostAlertText'),
    'soundFile' => addslashes($dataStore->getVar('streamalertsettings', 'hostAlertSound')),
    'customCss' => $dataStore->getVar('streamalertsettings', 'hostAlertCSS'),
  ],
]);
?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title></title>
  <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
  <link rel="stylesheet" href="app/css/defaultAlertCSS.css"/>
  <script>
    var botAddress = '<?= $eventServerAdress ?>',
        alertSettings = <?= $alertSettings ?>;
  </script>
  <script src="//code.jquery.com/jquery-1.11.3.min.js" type="text/javascript"></script>
  <script src="app/js/rsocket.min.js"></script>
  <script src="app/js/stream-alerts.min.js"></script>
</head>
<body>

</body>
</html>